# هيكل قاعدة بيانات Laravel لقواعد التسعير

## نظرة عامة على الهيكل المقترح

بناءً على تحليل ملف SAFEEN، نقترح هيكل قاعدة بيانات مرن يمكنه التعامل مع مختلف قواعد التسعير والشروط. سنستخدم نهج "Rule Engine" لتنفيذ قواعد التسعير المعقدة.

## الجداول الرئيسية

### 1. جدول الخدمات (`services`)
```php
Schema::create('services', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // مثل 'ANCHORAGE', 'CHANNEL', 'PILOTAGE'
    $table->string('name');
    $table->text('description')->nullable();
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

### 2. جدول فئات التعريفة (`tariff_categories`)
```php
Schema::create('tariff_categories', function (Blueprint $table) {
    $table->id();
    $table->foreignId('service_id')->constrained();
    $table->string('code')->unique(); // مثل '5.1', '5.2', '6.1'
    $table->string('name');
    $table->text('description')->nullable();
    $table->string('unit_of_measurement'); // مثل 'Per GT', 'Per Vessel Per Transit'
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

### 3. جدول قواعد التسعير (`pricing_rules`)
```php
Schema::create('pricing_rules', function (Blueprint $table) {
    $table->id();
    $table->foreignId('tariff_category_id')->constrained();
    $table->string('name');
    $table->decimal('rate', 10, 2); // السعر الأساسي
    $table->json('conditions'); // شروط تطبيق هذه القاعدة بتنسيق JSON
    $table->integer('priority')->default(0); // أولوية تطبيق القاعدة (الأعلى يتم تقييمه أولاً)
    $table->date('effective_from')->nullable();
    $table->date('effective_to')->nullable();
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

### 4. جدول الشروط (`condition_types`)
```php
Schema::create('condition_types', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique(); // مثل 'vessel_type', 'gt_size', 'stay_duration'
    $table->string('name');
    $table->string('data_type'); // مثل 'string', 'number', 'boolean', 'date'
    $table->string('operator_type'); // مثل 'comparison', 'range', 'list', 'boolean'
    $table->json('available_operators')->nullable(); // العمليات المتاحة لهذا النوع من الشروط
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

### 5. جدول الموانئ (`ports`)
```php
Schema::create('ports', function (Blueprint $table) {
    $table->id();
    $table->string('code')->unique();
    $table->string('name');
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

### 6. جدول السفن (`vessels`)
```php
Schema::create('vessels', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('type'); // مثل 'CARGO', 'OTHER'
    $table->decimal('gt_size', 10, 2); // الحجم الإجمالي
    $table->decimal('loa', 10, 2); // الطول الإجمالي
    $table->boolean('active')->default(true);
    $table->timestamps();
});
```

### 7. جدول الفواتير (`invoices`)
```php
Schema::create('invoices', function (Blueprint $table) {
    $table->id();
    $table->foreignId('vessel_id')->constrained();
    $table->string('invoice_number')->unique();
    $table->date('invoice_date');
    $table->decimal('total_amount', 12, 2)->default(0);
    $table->string('status'); // مثل 'DRAFT', 'ISSUED', 'PAID'
    $table->timestamps();
});
```

### 8. جدول بنود الفاتورة (`invoice_items`)
```php
Schema::create('invoice_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('invoice_id')->constrained();
    $table->foreignId('tariff_category_id')->constrained();
    $table->foreignId('pricing_rule_id')->constrained();
    $table->decimal('quantity', 10, 2);
    $table->decimal('unit_price', 10, 2);
    $table->decimal('total_price', 12, 2);
    $table->json('applied_conditions')->nullable(); // الشروط المطبقة بتنسيق JSON
    $table->timestamps();
});
```

## هيكل JSON للشروط

سيتم تخزين الشروط في حقل `conditions` في جدول `pricing_rules` بتنسيق JSON. فيما يلي مثال على هيكل JSON:

```json
{
  "operator": "AND",
  "conditions": [
    {
      "type": "vessel_type",
      "operator": "=",
      "value": "CARGO"
    },
    {
      "type": "stay_duration",
      "operator": "<=",
      "value": 3
    },
    {
      "type": "gt_size",
      "operator": ">",
      "value": 0
    }
  ]
}
```

يمكن أيضًا تضمين شروط متداخلة:

```json
{
  "operator": "AND",
  "conditions": [
    {
      "type": "vessel_type",
      "operator": "=",
      "value": "CARGO"
    },
    {
      "operator": "OR",
      "conditions": [
        {
          "type": "port",
          "operator": "=",
          "value": "KHALIFA"
        },
        {
          "type": "port",
          "operator": "=",
          "value": "ZAYED"
        }
      ]
    }
  ]
}
```

## نماذج Laravel (Models)

### نموذج الخدمة (Service Model)
```php
class Service extends Model
{
    protected $fillable = ['code', 'name', 'description', 'active'];
    
    public function tariffCategories()
    {
        return $this->hasMany(TariffCategory::class);
    }
}
```

### نموذج فئة التعريفة (TariffCategory Model)
```php
class TariffCategory extends Model
{
    protected $fillable = ['service_id', 'code', 'name', 'description', 'unit_of_measurement', 'active'];
    
    public function service()
    {
        return $this->belongsTo(Service::class);
    }
    
    public function pricingRules()
    {
        return $this->hasMany(PricingRule::class);
    }
}
```

### نموذج قاعدة التسعير (PricingRule Model)
```php
class PricingRule extends Model
{
    protected $fillable = ['tariff_category_id', 'name', 'rate', 'conditions', 'priority', 'effective_from', 'effective_to', 'active'];
    
    protected $casts = [
        'conditions' => 'json',
        'effective_from' => 'date',
        'effective_to' => 'date',
    ];
    
    public function tariffCategory()
    {
        return $this->belongsTo(TariffCategory::class);
    }
    
    public function isApplicable($context)
    {
        // تنفيذ منطق تقييم الشروط
        return $this->evaluateConditions($this->conditions, $context);
    }
    
    private function evaluateConditions($conditionGroup, $context)
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
    
    private function evaluateCondition($condition, $context)
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
                return $contextValue == $value;
            case '!=':
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
            default:
                return false;
        }
    }
}
```

## خدمة حساب الرسوم (PricingService)

```php
class PricingService
{
    public function calculatePrice($tariffCategoryCode, $context)
    {
        $tariffCategory = TariffCategory::where('code', $tariffCategoryCode)
            ->where('active', true)
            ->first();
            
        if (!$tariffCategory) {
            throw new Exception("Tariff category not found: {$tariffCategoryCode}");
        }
        
        $today = now()->format('Y-m-d');
        
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
            
        foreach ($applicableRules as $rule) {
            if ($rule->isApplicable($context)) {
                return [
                    'rule' => $rule,
                    'rate' => $rule->rate,
                    'tariff_category' => $tariffCategory
                ];
            }
        }
        
        throw new Exception("No applicable pricing rule found for tariff category: {$tariffCategoryCode}");
    }
}
```

## مثال على استخدام الخدمة

```php
// مثال على استخدام خدمة التسعير
$pricingService = new PricingService();

// سياق البيانات للسفينة والخدمة
$context = [
    'vessel_type' => 'CARGO',
    'gt_size' => 5000,
    'stay_duration' => 2,
    'port' => 'KHALIFA',
    'loa' => 120,
    'movement_type' => 'ARRIVAL'
];

try {
    // حساب سعر رسوم المرساة (كود 5.1)
    $anchoragePrice = $pricingService->calculatePrice('5.1', $context);
    
    echo "Applicable rate: " . $anchoragePrice['rate'];
    echo "Total price: " . ($anchoragePrice['rate'] * $context['gt_size']);
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

## تنفيذ واجهة برمجة التطبيقات (API)

يمكن إنشاء واجهة برمجة تطبيقات RESTful للتعامل مع قواعد التسعير:

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{service}/tariff-categories', [TariffCategoryController::class, 'index']);
    Route::post('calculate-price', [PricingController::class, 'calculatePrice']);
});
```

```php
// PricingController
public function calculatePrice(Request $request)
{
    $request->validate([
        'tariff_category_code' => 'required|string',
        'context' => 'required|array'
    ]);
    
    try {
        $pricingService = new PricingService();
        $result = $pricingService->calculatePrice(
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
```

## واجهة إدارة قواعد التسعير

يمكن إنشاء واجهة إدارة لتمكين المستخدمين من إدارة قواعد التسعير بسهولة:

1. عرض وإدارة الخدمات
2. عرض وإدارة فئات التعريفة
3. إنشاء وتحرير قواعد التسعير مع واجهة مستخدم سهلة لإدارة الشروط
4. اختبار قواعد التسعير باستخدام بيانات اختبار

## ملاحظات تنفيذية

1. **التخزين المؤقت (Caching)**: يمكن تخزين قواعد التسعير المستخدمة بشكل متكرر في ذاكرة التخزين المؤقت لتحسين الأداء.
2. **التسجيل (Logging)**: تسجيل كل عملية حساب سعر لأغراض التدقيق.
3. **التحقق من الصحة (Validation)**: التحقق من صحة بيانات السياق قبل تطبيق قواعد التسعير.
4. **الأمان (Security)**: تنفيذ ضوابط الوصول المناسبة لإدارة قواعد التسعير.
5. **الاختبار (Testing)**: كتابة اختبارات وحدة وتكامل شاملة لضمان دقة حسابات التسعير.
