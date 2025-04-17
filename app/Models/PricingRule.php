<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
        'conditions' => 'array',
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
        // return $this->evaluateConditions(json_decode($this->conditions, true), $context);
        // return $this->evaluateConditions($this->conditions, $context);
        $conditions = is_string($this->conditions)
            ? json_decode($this->conditions, true)
            : $this->conditions;

        return $this->evaluateConditions($conditions, $context);
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
