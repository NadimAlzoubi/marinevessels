<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Service extends Model
{
    protected $fillable = ['code', 'name', 'description', 'active'];
    
    public function tariffCategories(): HasMany
    {
        return $this->hasMany(TariffCategory::class);
    }
}

class TariffCategory extends Model
{
    protected $fillable = ['service_id', 'code', 'name', 'description', 'unit_of_measurement', 'active'];
    
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
    
    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class);
    }
}

class PricingRule extends Model
{
    protected $fillable = [
        'tariff_category_id', 
        'name', 
        'rate', 
        'conditions', 
        'priority', 
        'effective_from', 
        'effective_to', 
        'active'
    ];
    
    protected $casts = [
        'conditions' => 'json',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];
    
    public function tariffCategory(): BelongsTo
    {
        return $this->belongsTo(TariffCategory::class);
    }
    
    /**
     * Check if this pricing rule is applicable to the given context
     *
     * @param array $context The context data to evaluate against
     * @return bool
     */
    public function isApplicable(array $context): bool
    {
        // Evaluate the conditions against the context
        return $this->evaluateConditions($this->conditions, $context);
    }
    
    /**
     * Evaluate a group of conditions against the context
     *
     * @param array $conditionGroup The condition group to evaluate
     * @param array $context The context data to evaluate against
     * @return bool
     */
    private function evaluateConditions(array $conditionGroup, array $context): bool
    {
        $operator = $conditionGroup['operator'] ?? 'AND';
        $conditions = $conditionGroup['conditions'] ?? [];
        
        if (empty($conditions)) {
            return true;
        }
        
        if ($operator === 'AND') {
            foreach ($conditions as $condition) {
                if (isset($condition['conditions'])) {
                    if (!$this->evaluateConditions($condition, $context)) {
                        return false;
                    }
                } else {
                    if (!$this->evaluateCondition($condition, $context)) {
                        return false;
                    }
                }
            }
            return true;
        } else { // OR
            foreach ($conditions as $condition) {
                if (isset($condition['conditions'])) {
                    if ($this->evaluateConditions($condition, $context)) {
                        return true;
                    }
                } else {
                    if ($this->evaluateCondition($condition, $context)) {
                        return true;
                    }
                }
            }
            return false;
        }
    }
    
    /**
     * Evaluate a single condition against the context
     *
     * @param array $condition The condition to evaluate
     * @param array $context The context data to evaluate against
     * @return bool
     */
    private function evaluateCondition(array $condition, array $context): bool
    {
        $type = $condition['type'];
        $operator = $condition['operator'];
        $value = $condition['value'];
        
        if (!isset($context[$type])) {
            return false;
        }
        
        $contextValue = $context[$type];
        
        switch ($operator) {
            case '=':
            case '==':
                return $contextValue == $value;
            case '!=':
            case '<>':
                return $contextValue != $value;
            case '>':
                return $contextValue > $value;
            case '>=':
                return $contextValue >= $value;
            case '<':
                return $contextValue < $value;
            case '<=':
                return $contextValue <= $value;
            case 'in':
                return in_array($contextValue, (array) $value);
            case 'not_in':
                return !in_array($contextValue, (array) $value);
            case 'between':
                return $contextValue >= $value[0] && $contextValue <= $value[1];
            case 'contains':
                return strpos($contextValue, $value) !== false;
            case 'starts_with':
                return strpos($contextValue, $value) === 0;
            case 'ends_with':
                return substr($contextValue, -strlen($value)) === $value;
            default:
                return false;
        }
    }
}

namespace App\Services;

use App\Models\TariffCategory;
use App\Models\PricingRule;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PricingService
{
    /**
     * Calculate the price for a given tariff category and context
     *
     * @param string $tariffCategoryCode The tariff category code
     * @param array $context The context data for evaluation
     * @return array The pricing result
     * @throws Exception If no applicable pricing rule is found
     */
    public function calculatePrice(string $tariffCategoryCode, array $context): array
    {
        // Try to get from cache first
        $cacheKey = "pricing_{$tariffCategoryCode}_" . md5(json_encode($context));
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // Find the tariff category
        $tariffCategory = TariffCategory::where('code', $tariffCategoryCode)
            ->where('active', true)
            ->first();
            
        if (!$tariffCategory) {
            throw new Exception("Tariff category not found: {$tariffCategoryCode}");
        }
        
        $today = now()->format('Y-m-d');
        
        // Find applicable pricing rules
        $applicableRules = $tariffCategory->pricingRules()
            ->where('active', true)
            ->where(function ($query) use ($today) {
                $query->whereNull('effective_from')
                    ->orWhere('effective_from', '<=', $today);
            })
            ->where(function ($query) use ($today) {
                $query->whereNull('effective_to')
                    ->orWhere('effective_to', '>=', $today);
            })
            ->orderBy('priority', 'desc')
            ->get();
        
        // Log the pricing calculation attempt
        Log::info("Calculating price for tariff category {$tariffCategoryCode}", [
            'context' => $context,
            'rules_count' => $applicableRules->count()
        ]);
        
        // Evaluate each rule
        foreach ($applicableRules as $rule) {
            if ($rule->isApplicable($context)) {
                // Calculate the price based on the rule and context
                $price = $this->calculateFinalPrice($rule, $context);
                
                $result = [
                    'rule_id' => $rule->id,
                    'rule_name' => $rule->name,
                    'tariff_category' => $tariffCategory->name,
                    'base_rate' => $rule->rate,
                    'calculated_price' => $price,
                    'unit_of_measurement' => $tariffCategory->unit_of_measurement,
                    'applied_conditions' => $rule->conditions
                ];
                
                // Cache the result for 1 hour
                Cache::put($cacheKey, $result, now()->addHour());
                
                return $result;
            }
        }
        
        // If we get here, no applicable rule was found
        throw new Exception("No applicable pricing rule found for tariff category: {$tariffCategoryCode}");
    }
    
    /**
     * Calculate the final price based on the rule and context
     *
     * @param PricingRule $rule The pricing rule
     * @param array $context The context data
     * @return float The calculated price
     */
    private function calculateFinalPrice(PricingRule $rule, array $context): float
    {
        $baseRate = $rule->rate;
        $tariffCategory = $rule->tariffCategory;
        
        // Apply different calculation logic based on the tariff category
        switch ($tariffCategory->code) {
            // Anchorage Charges
            case '5.1': // Vessel stays ≤ 3 days (CARGO)
                return $baseRate * ($context['gt_size'] ?? 0);
                
            case '5.2': // Vessel stays > 4 days (CARGO)
                $stayDuration = $context['stay_duration'] ?? 0;
                return $baseRate * ($context['gt_size'] ?? 0) * $stayDuration;
                
            case '5.3': // Vessel stays ≤ 5 days (OTHER)
                $stayDuration = $context['stay_duration'] ?? 0;
                return $baseRate * ($context['gt_size'] ?? 0) * $stayDuration;
                
            case '5.4': // Vessel stays > 5 days (OTHER)
                $stayDuration = $context['stay_duration'] ?? 0;
                return $baseRate * ($context['gt_size'] ?? 0) * $stayDuration;
            
            // Channel Charges
            case '6.3': // For vessels ≤ 100 GT
            case '6.4': // For vessels 100.01 GT to 500 GT
                return $baseRate;
                
            case '6.5': // For vessels 500.01 GT to 1,000 GT
                // Additional charge of AED 1.25 per vessel per GT
                return $baseRate + (1.25 * ($context['gt_size'] ?? 0));
                
            case '6.6': // For vessels 1,000.01 GT to 3,000 GT
            case '6.7': // For Vessels < 3,000.01 & Above
                // Additional charge of AED 1.75 per vessel per GT
                return $baseRate + (1.75 * ($context['gt_size'] ?? 0));
            
            // Pilotage
            case '7.1': // Vessels LOA: up to 100 m
            case '7.2': // Vessels LOA: 100.01 m to 160 m
            case '7.3': // Vessels LOA: 160.01 m to 250 m
            case '7.4': // Vessels LOA: 250 m and above
                $hours = $context['service_hours'] ?? 1;
                return $baseRate * $hours;
            
            // Default calculation
            default:
                return $baseRate;
        }
    }
    
    /**
     * Get all applicable pricing rules for a vessel
     *
     * @param int $vesselId The vessel ID
     * @return array The applicable pricing rules
     */
    public function getApplicableRulesForVessel(int $vesselId): array
    {
        // Get the vessel details
        $vessel = \App\Models\Vessel::findOrFail($vesselId);
        
        // Create the context from vessel data
        $context = [
            'vessel_type' => $vessel->type,
            'gt_size' => $vessel->gt_size,
            'loa' => $vessel->loa
        ];
        
        // Get all active tariff categories
        $tariffCategories = TariffCategory::where('active', true)->get();
        
        $applicableRules = [];
        
        foreach ($tariffCategories as $category) {
            try {
                $result = $this->calculatePrice($category->code, $context);
                $applicableRules[] = $result;
            } catch (Exception $e) {
                // Skip categories with no applicable rules
                continue;
            }
        }
        
        return $applicableRules;
    }
}

namespace App\Http\Controllers;

use App\Services\PricingService;
use Illuminate\Http\Request;
use Exception;

class PricingController extends Controller
{
    protected $pricingService;
    
    public function __construct(PricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }
    
    /**
     * Calculate price based on tariff category and context
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function calculatePrice(Request $request)
    {
        $request->validate([
            'tariff_category_code' => 'required|string',
            'context' => 'required|array'
        ]);
        
        try {
            $result = $this->pricingService->calculatePrice(
                $request->tariff_category_code,
                $request->context
            );
            
            return response()->json([
                'success' => true,
                'data' => $result
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Get applicable rules for a vessel
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getApplicableRulesForVessel(Request $request)
    {
        $request->validate([
            'vessel_id' => 'required|integer|exists:vessels,id'
        ]);
        
        try {
            $rules = $this->pricingService->getApplicableRulesForVessel($request->vessel_id);
            
            return response()->json([
                'success' => true,
                'data' => $rules
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }
    
    /**
     * Test pricing rules with sample data
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function testPricingRules()
    {
        $testCases = [
            [
                'name' => 'Cargo vessel under 3 days',
                'tariff_category_code' => '5.1',
                'context' => [
                    'vessel_type' => 'CARGO',
                    'gt_size' => 5000,
                    'stay_duration' => 2
                ]
            ],
            [
                'name' => 'Cargo vessel over 4 days',
                'tariff_category_code' => '5.2',
                'context' => [
                    'vessel_type' => 'CARGO',
                    'gt_size' => 5000,
                    'stay_duration' => 5
                ]
            ],
            [
                'name' => 'Channel charge for medium vessel',
                'tariff_category_code' => '6.5',
                'context' => [
                    'gt_size' => 750
                ]
            ],
            [
                'name' => 'Pilotage for large vessel',
                'tariff_category_code' => '7.3',
                'context' => [
                    'loa' => 180,
                    'service_hours' => 3
                ]
            ]
        ];
        
        $results = [];
        
        foreach ($testCases as $testCase) {
            try {
                $result = $this->pricingService->calculatePrice(
                    $testCase['tariff_category_code'],
                    $testCase['context']
                );
                
                $results[] = [
                    'test_case' => $testCase['name'],
                    'success' => true,
                    'result' => $result
                ];
            } catch (Exception $e) {
                $results[] = [
                    'test_case' => $testCase['name'],
                    'success' => false,
                    'message' => $e->getMessage()
                ];
            }
        }
        
        return response()->json([
            'success' => true,
            'data' => $results
        ]);
    }
}

// Example database migrations
namespace App\Database\Migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePricingTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('tariff_categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('code')->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('unit_of_measurement');
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('pricing_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tariff_category_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->decimal('rate', 10, 2);
            $table->json('conditions');
            $table->integer('priority')->default(0);
            $table->date('effective_from')->nullable();
            $table->date('effective_to')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('condition_types', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('data_type');
            $table->string('operator_type');
            $table->json('available_operators')->nullable();
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('vessels', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');
            $table->decimal('gt_size', 10, 2);
            $table->decimal('loa', 10, 2);
            $table->boolean('active')->default(true);
            $table->timestamps();
        });

        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vessel_id')->constrained()->onDelete('cascade');
            $table->string('invoice_number')->unique();
            $table->date('invoice_date');
            $table->decimal('total_amount', 12, 2)->default(0);
            $table->string('status');
            $table->timestamps();
        });

        Schema::create('invoice_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('tariff_category_id')->constrained();
            $table->foreignId('pricing_rule_id')->constrained();
            $table->decimal('quantity', 10, 2);
            $table->decimal('unit_price', 10, 2);
            $table->decimal('total_price', 12, 2);
            $table->json('applied_conditions')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('invoice_items');
        Schema::dropIfExists('invoices');
        Schema::dropIfExists('vessels');
        Schema::dropIfExists('pricing_rules');
        Schema::dropIfExists('tariff_categories');
        Schema::dropIfExists('services');
        Schema::dropIfExists('condition_types');
    }
}

// Example seeder for SAFEEN tariff data
namespace App\Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\TariffCategory;
use App\Models\PricingRule;
use App\Models\ConditionType;

class SAFEENTariffSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create condition types
        $conditionTypes = [
            [
                'code' => 'vessel_type',
                'name' => 'Vessel Type',
                'data_type' => 'string',
                'operator_type' => 'comparison',
                'available_operators' => json_encode(['=', '!=', 'in', 'not_in'])
            ],
            [
                'code' => 'gt_size',
                'name' => 'GT Size',
                'data_type' => 'number',
                'operator_type' => 'comparison',
                'available_operators' => json_encode(['=', '!=', '>', '>=', '<', '<=', 'between'])
            ],
            [
                'code' => 'stay_duration',
                'name' => 'Stay Duration',
                'data_type' => 'number',
                'operator_type' => 'comparison',
                'available_operators' => json_encode(['=', '!=', '>', '>=', '<', '<=', 'between'])
            ],
            [
                'code' => 'loa',
                'name' => 'Length Overall',
                'data_type' => 'number',
                'operator_type' => 'comparison',
                'available_operators' => json_encode(['=', '!=', '>', '>=', '<', '<=', 'between'])
            ],
            [
                'code' => 'port',
                'name' => 'Port',
                'data_type' => 'string',
                'operator_type' => 'comparison',
                'available_operators' => json_encode(['=', '!=', 'in', 'not_in'])
            ],
            [
                'code' => 'waiting_for_berth',
                'name' => 'Waiting for Berth',
                'data_type' => 'boolean',
                'operator_type' => 'boolean',
                'available_operators' => json_encode(['='])
            ]
        ];
        
        foreach ($conditionTypes as $type) {
            ConditionType::create($type);
        }
        
        // Create services
        $anchorageService = Service::create([
            'code' => 'ANCHORAGE',
            'name' => 'Anchorage Charges',
            'description' => 'Charges for vessels at anchorage'
        ]);
        
        $channelService = Service::create([
            'code' => 'CHANNEL',
            'name' => 'Channel Charges',
            'description' => 'Charges for channel transit'
        ]);
        
        $pilotageService = Service::create([
            'code' => 'PILOTAGE',
            'name' => 'Pilotage',
            'description' => 'Charges for pilotage services'
        ]);
        
        // Create tariff categories for Anchorage
        $anchorageCategories = [
            [
                'code' => '5.1',
                'name' => 'Cargo Vessel stays ≤ 3 days',
                'unit_of_measurement' => 'Per GT'
            ],
            [
                'code' => '5.2',
                'name' => 'Cargo Vessel stays > 4 days',
                'unit_of_measurement' => 'Per GT per day or part thereof'
            ],
            [
                'code' => '5.3',
                'name' => 'Other Vessel stays ≤ 5 days',
                'unit_of_measurement' => 'Per GT per day or part thereof'
            ],
            [
                'code' => '5.4',
                'name' => 'Other Vessel stays > 5 days',
                'unit_of_measurement' => 'Per GT per day or part thereof'
            ]
        ];
        
        foreach ($anchorageCategories as $category) {
            TariffCategory::create(array_merge($category, ['service_id' => $anchorageService->id]));
        }
        
        // Create pricing rules for Anchorage
        $anchorageRules = [
            [
                'tariff_category_id' => 1, // 5.1
                'name' => 'Standard Cargo Vessel ≤ 3 days',
                'rate' => 0.10,
                'conditions' => json_encode([
                    'operator' => 'AND',
                    'conditions' => [
                        [
                            'type' => 'vessel_type',
                            'operator' => '=',
                            'value' => 'CARGO'
                        ],
                        [
                            'type' => 'stay_duration',
                            'operator' => '<=',
                            'value' => 3
                        ]
                    ]
                ]),
                'priority' => 10
            ],
            [
                'tariff_category_id' => 2, // 5.2
                'name' => 'Standard Cargo Vessel > 4 days',
                'rate' => 0.05,
                'conditions' => json_encode([
                    'operator' => 'AND',
                    'conditions' => [
                        [
                            'type' => 'vessel_type',
                            'operator' => '=',
                            'value' => 'CARGO'
                        ],
                        [
                            'type' => 'stay_duration',
                            'operator' => '>',
                            'value' => 4
                        ]
                    ]
                ]),
                'priority' => 10
            ],
            [
                'tariff_category_id' => 3, // 5.3
                'name' => 'Other Vessel ≤ 5 days',
                'rate' => 0.10,
                'conditions' => json_encode([
                    'operator' => 'AND',
                    'conditions' => [
                        [
                            'type' => 'vessel_type',
                            'operator' => '!=',
                            'value' => 'CARGO'
                        ],
                        [
                            'type' => 'stay_duration',
                            'operator' => '<=',
                            'value' => 5
                        ]
                    ]
                ]),
                'priority' => 10
            ],
            [
                'tariff_category_id' => 4, // 5.4
                'name' => 'Other Vessel > 5 days',
                'rate' => 0.15,
                'conditions' => json_encode([
                    'operator' => 'AND',
                    'conditions' => [
                        [
                            'type' => 'vessel_type',
                            'operator' => '!=',
                            'value' => 'CARGO'
                        ],
                        [
                            'type' => 'stay_duration',
                            'operator' => '>',
                            'value' => 5
                        ]
                    ]
                ]),
                'priority' => 10
            ],
            [
                'tariff_category_id' => 1, // 5.1 - Minimum charge rule
                'name' => 'Minimum Charge for Cargo Vessel ≤ 3 days',
                'rate' => 100,
                'conditions' => json_encode([
                    'operator' => 'AND',
                    'conditions' => [
                        [
                            'type' => 'vessel_type',
                            'operator' => '=',
                            'value' => 'CARGO'
                        ],
                        [
                            'type' => 'stay_duration',
                            'operator' => '<=',
                            'value' => 3
                        ],
                        [
                            'type' => 'gt_size',
                            'operator' => '<',
                            'value' => 1000 // GT size that would result in less than 100 AED
                        ]
                    ]
                ]),
                'priority' => 20 // Higher priority to override standard rule
            ]
        ];
        
        foreach ($anchorageRules as $rule) {
            PricingRule::create($rule);
        }
        
        // Add more services, tariff categories, and pricing rules as needed
    }
}
