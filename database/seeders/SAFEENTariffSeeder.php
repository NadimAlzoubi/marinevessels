<?php

namespace Database\Seeders;

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
