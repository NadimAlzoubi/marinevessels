<?php

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
