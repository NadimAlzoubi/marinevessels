# توثيق نظام قواعد التسعير لخدمات SAFEEN في Laravel

## نظرة عامة

هذا المستند يشرح تنفيذ نظام قواعد التسعير المرن لخدمات SAFEEN البحرية في إطار عمل Laravel. النظام مصمم للتعامل مع قواعد التسعير المعقدة والشروط المتنوعة الموجودة في وثيقة تعريفات SAFEEN.

## المفاهيم الأساسية

النظام مبني على المفاهيم التالية:

1. **الخدمات (Services)**: الفئات الرئيسية للخدمات مثل رسوم المرساة، رسوم القناة، الإرشاد، إلخ.
2. **فئات التعريفة (Tariff Categories)**: فئات فرعية ضمن كل خدمة، مثل "سفن البضائع التي تبقى ≤ 3 أيام".
3. **قواعد التسعير (Pricing Rules)**: القواعد التي تحدد السعر المطبق بناءً على مجموعة من الشروط.
4. **الشروط (Conditions)**: المعايير التي تحدد متى تنطبق قاعدة تسعير معينة.
5. **محرك القواعد (Rule Engine)**: آلية لتقييم الشروط وتحديد القاعدة المناسبة.

## هيكل قاعدة البيانات

### الجداول الرئيسية

1. **جدول الخدمات (`services`)**: يخزن الخدمات الرئيسية.
2. **جدول فئات التعريفة (`tariff_categories`)**: يخزن فئات التعريفة لكل خدمة.
3. **جدول قواعد التسعير (`pricing_rules`)**: يخزن قواعد التسعير مع الشروط والأسعار.
4. **جدول أنواع الشروط (`condition_types`)**: يخزن أنواع الشروط المتاحة.
5. **جدول السفن (`vessels`)**: يخزن معلومات السفن.
6. **جدول الفواتير (`invoices`)**: يخزن الفواتير.
7. **جدول بنود الفاتورة (`invoice_items`)**: يخزن بنود الفاتورة.

### العلاقات بين الجداول

- خدمة واحدة لها العديد من فئات التعريفة.
- فئة تعريفة واحدة لها العديد من قواعد التسعير.
- فاتورة واحدة لها العديد من بنود الفاتورة.
- كل بند فاتورة مرتبط بفئة تعريفة وقاعدة تسعير.

## تنفيذ النماذج (Models)

### نموذج الخدمة (Service Model)

```php
class Service extends Model
{
    protected $fillable = ['code', 'name', 'description', 'active'];
    
    public function tariffCategories(): HasMany
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
    
    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
    
    public function pricingRules(): HasMany
    {
        return $this->hasMany(PricingRule::class);
    }
}
```

### نموذج قاعدة التسعير (PricingRule Model)

```php
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
    
    public function isApplicable(array $context): bool
    {
        return $this->evaluateConditions($this->conditions, $context);
    }
    
    // ... المزيد من الدوال لتقييم الشروط
}
```

## هيكل الشروط (Conditions Structure)

الشروط مخزنة في حقل JSON في جدول `pricing_rules`. فيما يلي مثال على هيكل الشروط:

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

## خدمة التسعير (PricingService)

خدمة التسعير هي المكون الرئيسي الذي يقوم بحساب الأسعار بناءً على قواعد التسعير والسياق المقدم.

```php
class PricingService
{
    /**
     * حساب السعر لفئة تعريفة معينة وسياق معين
     */
    public function calculatePrice(string $tariffCategoryCode, array $context): array
    {
        // التحقق من وجود النتيجة في ذاكرة التخزين المؤقت
        $cacheKey = "pricing_{$tariffCategoryCode}_" . md5(json_encode($context));
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        // البحث عن فئة التعريفة
        $tariffCategory = TariffCategory::where('code', $tariffCategoryCode)
            ->where('active', true)
            ->first();
            
        if (!$tariffCategory) {
            throw new Exception("Tariff category not found: {$tariffCategoryCode}");
        }
        
        $today = now()->format('Y-m-d');
        
        // البحث عن قواعد التسعير المطبقة
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
        
        // تقييم كل قاعدة
        foreach ($applicableRules as $rule) {
            if ($rule->isApplicable($context)) {
                // حساب السعر بناءً على القاعدة والسياق
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
                
                // تخزين النتيجة في ذاكرة التخزين المؤقت لمدة ساعة
                Cache::put($cacheKey, $result, now()->addHour());
                
                return $result;
            }
        }
        
        // إذا وصلنا إلى هنا، فلم يتم العثور على قاعدة مطبقة
        throw new Exception("No applicable pricing rule found for tariff category: {$tariffCategoryCode}");
    }
    
    /**
     * حساب السعر النهائي بناءً على القاعدة والسياق
     */
    private function calculateFinalPrice(PricingRule $rule, array $context): float
    {
        $baseRate = $rule->rate;
        $tariffCategory = $rule->tariffCategory;
        
        // تطبيق منطق حساب مختلف بناءً على فئة التعريفة
        switch ($tariffCategory->code) {
            // رسوم المرساة
            case '5.1': // سفن البضائع التي تبقى ≤ 3 أيام
                return $baseRate * ($context['gt_size'] ?? 0);
                
            case '5.2': // سفن البضائع التي تبقى > 4 أيام
                $stayDuration = $context['stay_duration'] ?? 0;
                return $baseRate * ($context['gt_size'] ?? 0) * $stayDuration;
                
            // ... المزيد من حالات الحساب
                
            // الحساب الافتراضي
            default:
                return $baseRate;
        }
    }
}
```

## واجهة برمجة التطبيقات (API)

النظام يوفر واجهة برمجة تطبيقات RESTful للتعامل مع قواعد التسعير:

```php
// routes/api.php
Route::prefix('v1')->group(function () {
    Route::get('services', [ServiceController::class, 'index']);
    Route::get('services/{service}/tariff-categories', [TariffCategoryController::class, 'index']);
    Route::post('calculate-price', [PricingController::class, 'calculatePrice']);
    Route::get('vessels/{vessel}/applicable-rules', [PricingController::class, 'getApplicableRulesForVessel']);
    Route::get('test-pricing-rules', [PricingController::class, 'testPricingRules']);
});
```

## كيفية استخدام النظام

### 1. إعداد قاعدة البيانات

قم بتشغيل الهجرات وزراعة البيانات:

```bash
php artisan migrate
php artisan db:seed --class=SAFEENTariffSeeder
```

### 2. إنشاء الخدمات وفئات التعريفة وقواعد التسعير

يمكنك استخدام واجهة الإدارة أو إنشاء البيانات برمجيًا:

```php
// إنشاء خدمة
$service = Service::create([
    'code' => 'ANCHORAGE',
    'name' => 'رسوم المرساة',
    'description' => 'رسوم للسفن في المرساة'
]);

// إنشاء فئة تعريفة
$tariffCategory = TariffCategory::create([
    'service_id' => $service->id,
    'code' => '5.1',
    'name' => 'سفن البضائع التي تبقى ≤ 3 أيام',
    'unit_of_measurement' => 'لكل GT'
]);

// إنشاء قاعدة تسعير
$pricingRule = PricingRule::create([
    'tariff_category_id' => $tariffCategory->id,
    'name' => 'سفن البضائع القياسية ≤ 3 أيام',
    'rate' => 0.10,
    'conditions' => [
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
    ],
    'priority' => 10
]);
```

### 3. حساب الأسعار

استخدم خدمة التسعير لحساب الأسعار:

```php
$pricingService = new PricingService();

// سياق البيانات للسفينة والخدمة
$context = [
    'vessel_type' => 'CARGO',
    'gt_size' => 5000,
    'stay_duration' => 2,
    'port' => 'KHALIFA',
    'loa' => 120
];

try {
    // حساب سعر رسوم المرساة (كود 5.1)
    $result = $pricingService->calculatePrice('5.1', $context);
    
    echo "السعر المطبق: " . $result['base_rate'];
    echo "السعر المحسوب: " . $result['calculated_price'];
} catch (Exception $e) {
    echo "خطأ: " . $e->getMessage();
}
```

### 4. استخدام واجهة برمجة التطبيقات

يمكنك استخدام واجهة برمجة التطبيقات لحساب الأسعار:

```
POST /api/v1/calculate-price
{
    "tariff_category_code": "5.1",
    "context": {
        "vessel_type": "CARGO",
        "gt_size": 5000,
        "stay_duration": 2,
        "port": "KHALIFA",
        "loa": 120
    }
}
```

## أمثلة على قواعد التسعير

### 1. رسوم المرساة لسفن البضائع التي تبقى ≤ 3 أيام

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
    }
  ]
}
```

### 2. رسوم القناة للسفن من 500.01 GT إلى 1,000 GT

```json
{
  "operator": "AND",
  "conditions": [
    {
      "type": "gt_size",
      "operator": ">",
      "value": 500
    },
    {
      "type": "gt_size",
      "operator": "<=",
      "value": 1000
    }
  ]
}
```

### 3. رسوم الإرشاد للسفن ذات الطول الإجمالي من 100.01 م إلى 160 م

```json
{
  "operator": "AND",
  "conditions": [
    {
      "type": "loa",
      "operator": ">",
      "value": 100
    },
    {
      "type": "loa",
      "operator": "<=",
      "value": 160
    }
  ]
}
```

## ميزات متقدمة

### 1. التخزين المؤقت (Caching)

النظام يستخدم التخزين المؤقت لتحسين الأداء:

```php
$cacheKey = "pricing_{$tariffCategoryCode}_" . md5(json_encode($context));

if (Cache::has($cacheKey)) {
    return Cache::get($cacheKey);
}

// ... حساب السعر

Cache::put($cacheKey, $result, now()->addHour());
```

### 2. التسجيل (Logging)

النظام يسجل عمليات حساب الأسعار:

```php
Log::info("Calculating price for tariff category {$tariffCategoryCode}", [
    'context' => $context,
    'rules_count' => $applicableRules->count()
]);
```

### 3. الأولوية (Priority)

يمكن تعيين أولوية لقواعد التسعير لتحديد أي القواعد يجب تطبيقها أولاً:

```php
$applicableRules = $tariffCategory->pricingRules()
    // ... شروط أخرى
    ->orderBy('priority', 'desc')
    ->get();
```

### 4. تواريخ السريان (Effective Dates)

يمكن تحديد تواريخ بدء وانتهاء لقواعد التسعير:

```php
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
    // ... شروط أخرى
    ->get();
```

## الخلاصة

هذا النظام يوفر حلاً مرنًا وقويًا لإدارة قواعد التسعير المعقدة لخدمات SAFEEN البحرية. يمكن توسيع النظام بسهولة لدعم المزيد من أنواع الشروط وقواعد التسعير.

المميزات الرئيسية:
1. هيكل قاعدة بيانات مرن
2. محرك قواعد قوي لتقييم الشروط
3. دعم للشروط المتداخلة والمعقدة
4. تخزين مؤقت لتحسين الأداء
5. واجهة برمجة تطبيقات RESTful
6. دعم لتواريخ السريان والأولوية

يمكن استخدام هذا النظام كأساس لتطوير نظام فوترة كامل لخدمات SAFEEN البحرية.
