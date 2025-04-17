# نظام قواعد التسعير لخدمات SAFEEN - دليل شامل

## جدول المحتويات

1. [نظرة عامة على النظام](#نظرة-عامة-على-النظام)
2. [هيكل قاعدة البيانات](#هيكل-قاعدة-البيانات)
3. [النماذج والعلاقات](#النماذج-والعلاقات)
4. [الكنترولرات والطرق](#الكنترولرات-والطرق)
5. [واجهات المستخدم](#واجهات-المستخدم)
6. [محرك قواعد التسعير](#محرك-قواعد-التسعير)
7. [دليل الاستخدام](#دليل-الاستخدام)
8. [خطوات التنفيذ](#خطوات-التنفيذ)
9. [الأسئلة الشائعة](#الأسئلة-الشائعة)

## نظرة عامة على النظام

### الغرض من النظام

نظام قواعد التسعير لخدمات SAFEEN هو نظام متكامل لإدارة وتطبيق قواعد تسعير مرنة ومعقدة للخدمات البحرية. يهدف النظام إلى:

- تمكين إدارة قواعد تسعير متعددة لكل خدمة بحرية
- دعم شروط تطبيق معقدة ومتداخلة
- توفير آلية مرنة لحساب الرسوم بناءً على خصائص السفن والخدمات
- تسهيل إدارة التعريفات وتحديثها
- توفير واجهة مستخدم سهلة الاستخدام لإدارة القواعد واختبارها

### المكونات الرئيسية

يتكون النظام من أربعة مكونات رئيسية:

1. **الخدمات (Services)**: تمثل الخدمات الأساسية التي تقدمها SAFEEN مثل رسوم المرساة، رسوم القناة، الإرشاد، إلخ.

2. **فئات التعريفة (Tariff Categories)**: تمثل فئات التعريفة المختلفة ضمن كل خدمة، مثل فئات تعريفة رسوم المرساة المختلفة حسب نوع السفينة ومدة البقاء.

3. **قواعد التسعير (Pricing Rules)**: تمثل القواعد المحددة لحساب الأسعار، مع دعم للشروط المعقدة والمتداخلة.

4. **أنواع الشروط (Condition Types)**: تمثل أنواع الشروط المختلفة التي يمكن استخدامها في قواعد التسعير، مثل نوع السفينة، الحجم الإجمالي، نوع الزيارة، إلخ.

### تدفق العمل الأساسي

1. إنشاء الخدمات الأساسية
2. إنشاء فئات التعريفة لكل خدمة
3. تعريف أنواع الشروط المستخدمة في قواعد التسعير
4. إنشاء قواعد التسعير مع شروط التطبيق
5. استخدام قواعد التسعير لحساب رسوم الخدمات في الفواتير

## هيكل قاعدة البيانات

### الجداول الرئيسية

#### جدول الخدمات (services)

```sql
CREATE TABLE services (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### جدول فئات التعريفة (tariff_categories)

```sql
CREATE TABLE tariff_categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    service_id BIGINT UNSIGNED NOT NULL,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    unit_of_measurement VARCHAR(100) NOT NULL,
    description TEXT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (service_id) REFERENCES services(id) ON DELETE CASCADE
);
```

#### جدول قواعد التسعير (pricing_rules)

```sql
CREATE TABLE pricing_rules (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    tariff_category_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    rate DECIMAL(15, 2) NOT NULL,
    conditions JSON NOT NULL,
    priority INT NOT NULL DEFAULT 0,
    effective_from DATE NULL,
    effective_to DATE NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    FOREIGN KEY (tariff_category_id) REFERENCES tariff_categories(id) ON DELETE CASCADE
);
```

#### جدول أنواع الشروط (condition_types)

```sql
CREATE TABLE condition_types (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    data_type ENUM('string', 'number', 'boolean', 'date', 'array') NOT NULL,
    operator_type ENUM('comparison', 'boolean', 'text', 'date') NOT NULL,
    available_operators JSON NOT NULL,
    active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL
);
```

#### تحديث جدول الرسوم الثابتة (fixed_fees)

```sql
ALTER TABLE fixed_fees ADD COLUMN tariff_category_id BIGINT UNSIGNED NULL;
ALTER TABLE fixed_fees ADD CONSTRAINT fk_fixed_fees_tariff_category FOREIGN KEY (tariff_category_id) REFERENCES tariff_categories(id) ON DELETE SET NULL;
```

#### تحديث جدول رسوم الفواتير (invoice_fees)

```sql
ALTER TABLE invoice_fees ADD COLUMN pricing_method VARCHAR(50) NULL AFTER total;
ALTER TABLE invoice_fees ADD COLUMN pricing_context JSON NULL AFTER pricing_method;
```

### العلاقات بين الجداول

- **الخدمة (Service)** لها العديد من **فئات التعريفة (Tariff Categories)**
- **فئة التعريفة (Tariff Category)** لها العديد من **قواعد التسعير (Pricing Rules)**
- **فئة التعريفة (Tariff Category)** لها العديد من **الرسوم الثابتة (Fixed Fees)**
- **رسوم الفاتورة (Invoice Fee)** تحتفظ بمعلومات عن طريقة التسعير وسياق التسعير

## النماذج والعلاقات

### نموذج الخدمة (Service)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    /**
     * الخصائص القابلة للتعبئة الجماعية.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'description',
        'active',
    ];

    /**
     * الخصائص التي يجب تحويلها.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * الحصول على فئات التعريفة المرتبطة بهذه الخدمة.
     */
    public function tariffCategories()
    {
        return $this->hasMany(TariffCategory::class);
    }
}
```

### نموذج فئة التعريفة (TariffCategory)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TariffCategory extends Model
{
    use HasFactory;

    /**
     * الخصائص القابلة للتعبئة الجماعية.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'service_id',
        'code',
        'name',
        'unit_of_measurement',
        'description',
        'active',
    ];

    /**
     * الخصائص التي يجب تحويلها.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
    ];

    /**
     * الحصول على الخدمة المرتبطة بفئة التعريفة هذه.
     */
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    /**
     * الحصول على قواعد التسعير المرتبطة بفئة التعريفة هذه.
     */
    public function pricingRules()
    {
        return $this->hasMany(PricingRule::class);
    }

    /**
     * الحصول على الرسوم الثابتة المرتبطة بفئة التعريفة هذه.
     */
    public function fixedFees()
    {
        return $this->hasMany(FixedFee::class);
    }
}
```

### نموذج قاعدة التسعير (PricingRule)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PricingRule extends Model
{
    use HasFactory;

    /**
     * الخصائص القابلة للتعبئة الجماعية.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'tariff_category_id',
        'name',
        'rate',
        'conditions',
        'priority',
        'effective_from',
        'effective_to',
        'active',
    ];

    /**
     * الخصائص التي يجب تحويلها.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rate' => 'decimal:2',
        'conditions' => 'json',
        'priority' => 'integer',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'active' => 'boolean',
    ];

    /**
     * الحصول على فئة التعريفة المرتبطة بقاعدة التسعير هذه.
     */
    public function tariffCategory()
    {
        return $this->belongsTo(TariffCategory::class);
    }

    /**
     * التحقق مما إذا كانت قاعدة التسعير سارية في تاريخ معين.
     *
     * @param  \Carbon\Carbon|string|null  $date
     * @return bool
     */
    public function isEffectiveAt($date = null)
    {
        $date = $date ? Carbon::parse($date) : Carbon::now();
        
        // إذا لم يتم تحديد تاريخ بدء أو انتهاء، فالقاعدة سارية دائمًا
        if (!$this->effective_from && !$this->effective_to) {
            return true;
        }
        
        // التحقق من تاريخ البدء (إذا تم تحديده)
        if ($this->effective_from && $date->lt($this->effective_from)) {
            return false;
        }
        
        // التحقق من تاريخ الانتهاء (إذا تم تحديده)
        if ($this->effective_to && $date->gt($this->effective_to)) {
            return false;
        }
        
        return true;
    }

    /**
     * تقييم ما إذا كانت قاعدة التسعير تنطبق على سياق معين.
     *
     * @param  array  $context
     * @return bool
     */
    public function evaluateConditions(array $context)
    {
        if (!$this->conditions || !isset($this->conditions['conditions'])) {
            return true; // إذا لم تكن هناك شروط، فالقاعدة تنطبق دائمًا
        }
        
        return $this->evaluateConditionGroup($this->conditions, $context);
    }

    /**
     * تقييم مجموعة شروط.
     *
     * @param  array  $group
     * @param  array  $context
     * @return bool
     */
    protected function evaluateConditionGroup(array $group, array $context)
    {
        $operator = $group['operator'] ?? 'AND';
        $conditions = $group['conditions'] ?? [];
        
        if (empty($conditions)) {
            return true;
        }
        
        // تقييم الشروط باستخدام العملية المنطقية المحددة
        if ($operator === 'AND') {
            foreach ($conditions as $condition) {
                if (isset($condition['conditions'])) {
                    // مجموعة شروط متداخلة
                    if (!$this->evaluateConditionGroup($condition, $context)) {
                        return false;
                    }
                } else {
                    // شرط فردي
                    if (!$this->evaluateCondition($condition, $context)) {
                        return false;
                    }
                }
            }
            return true;
        } else { // OR
            foreach ($conditions as $condition) {
                if (isset($condition['conditions'])) {
                    // مجموعة شروط متداخلة
                    if ($this->evaluateConditionGroup($condition, $context)) {
                        return true;
                    }
                } else {
                    // شرط فردي
                    if ($this->evaluateCondition($condition, $context)) {
                        return true;
                    }
                }
            }
            return false;
        }
    }

    /**
     * تقييم شرط فردي.
     *
     * @param  array  $condition
     * @param  array  $context
     * @return bool
     */
    protected function evaluateCondition(array $condition, array $context)
    {
        $type = $condition['type'] ?? null;
        $operator = $condition['operator'] ?? null;
        $value = $condition['value'] ?? null;
        
        if (!$type || !$operator || !isset($context[$type])) {
            return false;
        }
        
        $contextValue = $context[$type];
        
        // تقييم الشرط باستخدام العملية المحددة
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
                $values = is_string($value) ? json_decode($value, true) : $value;
                return is_array($values) && in_array($contextValue, $values);
            case 'not_in':
                $values = is_string($value) ? json_decode($value, true) : $value;
                return is_array($values) && !in_array($contextValue, $values);
            case 'between':
                $range = is_string($value) ? json_decode($value, true) : $value;
                return is_array($range) && count($range) === 2 && $contextValue >= $range[0] && $contextValue <= $range[1];
            case 'contains':
                return is_string($contextValue) && is_string($value) && strpos($contextValue, $value) !== false;
            case 'starts_with':
                return is_string($contextValue) && is_string($value) && strpos($contextValue, $value) === 0;
            case 'ends_with':
                return is_string($contextValue) && is_string($value) && substr($contextValue, -strlen($value)) === $value;
            default:
                return false;
        }
    }
}
```

### نموذج نوع الشرط (ConditionType)

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ConditionType extends Model
{
    use HasFactory;

    /**
     * الخصائص القابلة للتعبئة الجماعية.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'name',
        'data_type',
        'operator_type',
        'available_operators',
        'active',
    ];

    /**
     * الخصائص التي يجب تحويلها.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'available_operators' => 'json',
        'active' => 'boolean',
    ];

    /**
     * الحصول على العمليات المتاحة لنوع البيانات المحدد.
     *
     * @param  string  $dataType
     * @return array
     */
    public static function getAvailableOperatorsForDataType($dataType)
    {
        switch ($dataType) {
            case 'string':
                return ['=', '!=', 'in', 'not_in', 'contains', 'starts_with', 'ends_with'];
            case 'number':
                return ['=', '!=', '>', '>=', '<', '<=', 'in', 'not_in', 'between'];
            case 'boolean':
                return ['=', '!='];
            case 'date':
                return ['=', '!=', '>', '>=', '<', '<=', 'between'];
            case 'array':
                return ['contains', 'in', 'not_in'];
            default:
                return [];
        }
    }

    /**
     * الحصول على العمليات المتاحة لنوع العملية المحدد.
     *
     * @param  string  $operatorType
     * @return array
     */
    public static function getAvailableOperatorsForOperatorType($operatorType)
    {
        switch ($operatorType) {
            case 'comparison':
                return ['=', '!=', '>', '>=', '<', '<='];
            case 'boolean':
                return ['=', '!='];
            case 'text':
                return ['=', '!=', 'contains', 'starts_with', 'ends_with', 'in', 'not_in'];
            case 'date':
                return ['=', '!=', '>', '>=', '<', '<=', 'between'];
            default:
                return [];
        }
    }
}
```

## الكنترولرات والطرق

### طرق النظام (Routes)

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TariffCategoryController;
use App\Http\Controllers\PricingRuleController;
use App\Http\Controllers\ConditionTypeController;

// طرق الخدمات
Route::resource('services', ServiceController::class);

// طرق فئات التعريفة
Route::resource('tariff-categories', TariffCategoryController::class);

// طرق قواعد التسعير
Route::resource('pricing-rules', PricingRuleController::class);
Route::patch('pricing-rules/{pricingRule}/toggle-active', [PricingRuleController::class, 'toggleActive'])->name('pricing-rules.toggle-active');
Route::post('pricing-rules/{pricingRule}/test', [PricingRuleController::class, 'testRule'])->name('pricing-rules.test');

// طرق أنواع الشروط
Route::resource('condition-types', ConditionTypeController::class);
Route::patch('condition-types/{conditionType}/toggle-active', [ConditionTypeController::class, 'toggleActive'])->name('condition-types.toggle-active');

// طرق إضافية للفواتير
Route::get('vessels/{vessel}/applicable-rules', [PricingRuleController::class, 'getApplicableRules'])->name('vessels.applicable-rules');
```

### كنترولر الخدمة (ServiceController)

```php
<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Http\Requests\ServiceRequest;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * عرض قائمة بجميع الخدمات.
     */
    public function index()
    {
        $services = Service::orderBy('name')->get();
        return view('services.index', compact('services'));
    }

    /**
     * عرض نموذج إنشاء خدمة جديدة.
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * تخزين خدمة جديدة في قاعدة البيانات.
     */
    public function store(ServiceRequest $request)
    {
        $service = Service::create($request->validated());
        
        return redirect()->route('services.show', $service)
            ->with('success', 'تم إنشاء الخدمة بنجاح.');
    }

    /**
     * عرض تفاصيل خدمة محددة.
     */
    public function show(Service $service)
    {
        $tariffCategories = $service->tariffCategories()->orderBy('name')->get();
        return view('services.show', compact('service', 'tariffCategories'));
    }

    /**
     * عرض نموذج تعديل خدمة محددة.
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * تحديث خدمة محددة في قاعدة البيانات.
     */
    public function update(ServiceRequest $request, Service $service)
    {
        $service->update($request->validated());
        
        return redirect()->route('services.show', $service)
            ->with('success', 'تم تحديث الخدمة بنجاح.');
    }

    /**
     * حذف خدمة محددة من قاعدة البيانات.
     */
    public function destroy(Service $service)
    {
        $service->delete();
        
        return redirect()->route('services.index')
            ->with('success', 'تم حذف الخدمة بنجاح.');
    }
}
```

### كنترولر فئة التعريفة (TariffCategoryController)

```php
<?php

namespace App\Http\Controllers;

use App\Models\TariffCategory;
use App\Models\Service;
use App\Http\Requests\TariffCategoryRequest;
use Illuminate\Http\Request;

class TariffCategoryController extends Controller
{
    /**
     * عرض قائمة بجميع فئات التعريفة.
     */
    public function index()
    {
        $tariffCategories = TariffCategory::with('service')->orderBy('name')->get();
        return view('tariff-categories.index', compact('tariffCategories'));
    }

    /**
     * عرض نموذج إنشاء فئة تعريفة جديدة.
     */
    public function create()
    {
        $services = Service::where('active', true)->orderBy('name')->pluck('name', 'id');
        return view('tariff-categories.create', compact('services'));
    }

    /**
     * تخزين فئة تعريفة جديدة في قاعدة البيانات.
     */
    public function store(TariffCategoryRequest $request)
    {
        $tariffCategory = TariffCategory::create($request->validated());
        
        return redirect()->route('tariff-categories.show', $tariffCategory)
            ->with('success', 'تم إنشاء فئة التعريفة بنجاح.');
    }

    /**
     * عرض تفاصيل فئة تعريفة محددة.
     */
    public function show(TariffCategory $tariffCategory)
    {
        $pricingRules = $tariffCategory->pricingRules()->orderBy('priority', 'desc')->get();
        return view('tariff-categories.show', compact('tariffCategory', 'pricingRules'));
    }

    /**
     * عرض نموذج تعديل فئة تعريفة محددة.
     */
    public function edit(TariffCategory $tariffCategory)
    {
        $services = Service::where('active', true)->orderBy('name')->pluck('name', 'id');
        return view('tariff-categories.edit', compact('tariffCategory', 'services'));
    }

    /**
     * تحديث فئة تعريفة محددة في قاعدة البيانات.
     */
    public function update(TariffCategoryRequest $request, TariffCategory $tariffCategory)
    {
        $tariffCategory->update($request->validated());
        
        return redirect()->route('tariff-categories.show', $tariffCategory)
            ->with('success', 'تم تحديث فئة التعريفة بنجاح.');
    }

    /**
     * حذف فئة تعريفة محددة من قاعدة البيانات.
     */
    public function destroy(TariffCategory $tariffCategory)
    {
        $tariffCategory->delete();
        
        return redirect()->route('tariff-categories.index')
            ->with('success', 'تم حذف فئة التعريفة بنجاح.');
    }
}
```

### كنترولر قاعدة التسعير (PricingRuleController)

```php
<?php

namespace App\Http\Controllers;

use App\Models\PricingRule;
use App\Models\TariffCategory;
use App\Models\ConditionType;
use App\Http\Requests\PricingRuleRequest;
use Illuminate\Http\Request;

class PricingRuleController extends Controller
{
    /**
     * عرض قائمة بجميع قواعد التسعير.
     */
    public function index()
    {
        $pricingRules = PricingRule::with(['tariffCategory', 'tariffCategory.service'])
            ->orderBy('priority', 'desc')
            ->get();
        return view('pricing-rules.index', compact('pricingRules'));
    }

    /**
     * عرض نموذج إنشاء قاعدة تسعير جديدة.
     */
    public function create(Request $request)
    {
        $tariffCategories = TariffCategory::where('active', true)
            ->with('service')
            ->get()
            ->mapWithKeys(function ($category) {
                return [$category->id => $category->service->name . ' - ' . $category->name];
            });
        
        $conditionTypes = ConditionType::where('active', true)->get();
        $selectedTariffCategoryId = $request->input('tariff_category_id');
        
        return view('pricing-rules.create', compact('tariffCategories', 'conditionTypes', 'selectedTariffCategoryId'));
    }

    /**
     * تخزين قاعدة تسعير جديدة في قاعدة البيانات.
     */
    public function store(PricingRuleRequest $request)
    {
        $pricingRule = PricingRule::create($request->validated());
        
        if ($request->has('redirect_to_category')) {
            return redirect()->route('tariff-categories.show', $pricingRule->tariff_category_id)
                ->with('success', 'تم إنشاء قاعدة التسعير بنجاح.');
        }
        
        return redirect()->route('pricing-rules.show', $pricingRule)
            ->with('success', 'تم إنشاء قاعدة التسعير بنجاح.');
    }

    /**
     * عرض تفاصيل قاعدة تسعير محددة.
     */
    public function show(PricingRule $pricingRule)
    {
        $conditionTypes = ConditionType::where('active', true)->get();
        return view('pricing-rules.show', compact('pricingRule', 'conditionTypes'));
    }

    /**
     * عرض نموذج تعديل قاعدة تسعير محددة.
     */
    public function edit(PricingRule $pricingRule)
    {
        $tariffCategories = TariffCategory::where('active', true)
            ->with('service')
            ->get()
            ->mapWithKeys(function ($category) {
                return [$category->id => $category->service->name . ' - ' . $category->name];
            });
        
        $conditionTypes = ConditionType::where('active', true)->get();
        
        return view('pricing-rules.edit', compact('pricingRule', 'tariffCategories', 'conditionTypes'));
    }

    /**
     * تحديث قاعدة تسعير محددة في قاعدة البيانات.
     */
    public function update(PricingRuleRequest $request, PricingRule $pricingRule)
    {
        $pricingRule->update($request->validated());
        
        return redirect()->route('pricing-rules.show', $pricingRule)
            ->with('success', 'تم تحديث قاعدة التسعير بنجاح.');
    }

    /**
     * حذف قاعدة تسعير محددة من قاعدة البيانات.
     */
    public function destroy(PricingRule $pricingRule)
    {
        $tariffCategoryId = $pricingRule->tariff_category_id;
        $pricingRule->delete();
        
        return redirect()->route('tariff-categories.show', $tariffCategoryId)
            ->with('success', 'تم حذف قاعدة التسعير بنجاح.');
    }

    /**
     * تبديل حالة تفعيل قاعدة تسعير محددة.
     */
    public function toggleActive(PricingRule $pricingRule)
    {
        $pricingRule->active = !$pricingRule->active;
        $pricingRule->save();
        
        return redirect()->back()->with('success', 'تم تحديث حالة قاعدة التسعير بنجاح.');
    }

    /**
     * اختبار قاعدة تسعير محددة.
     */
    public function testRule(Request $request, PricingRule $pricingRule)
    {
        $testValues = $request->input('test_values', []);
        
        // التحقق من تطبيق القاعدة
        $applies = $pricingRule->evaluateConditions($testValues);
        
        if ($applies) {
            return response()->json([
                'success' => true,
                'price' => $pricingRule->rate,
                'message' => 'تنطبق القاعدة على القيم المدخلة.'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'price' => 0,
                'message' => 'لا تنطبق القاعدة على القيم المدخلة.'
            ]);
        }
    }

    /**
     * الحصول على قواعد التسعير المطبقة على سفينة معينة.
     */
    public function getApplicableRules(Request $request, $vesselId)
    {
        // الحصول على بيانات السفينة
        $vessel = Vessel::findOrFail($vesselId);
        
        // إنشاء سياق التقييم
        $context = [
            'vessel_type' => $vessel->type,
            'gt_size' => $vessel->gt,
            'loa' => $vessel->loa,
            'call_type' => $request->input('call_type', 'IMPORT'),
            // إضافة المزيد من البيانات حسب الحاجة
        ];
        
        // الحصول على جميع قواعد التسعير النشطة
        $rules = PricingRule::where('active', true)
            ->whereHas('tariffCategory', function ($query) {
                $query->where('active', true);
            })
            ->with(['tariffCategory', 'tariffCategory.service'])
            ->orderBy('priority', 'desc')
            ->get();
        
        // تصفية القواعد المطبقة
        $applicableRules = $rules->filter(function ($rule) use ($context) {
            return $rule->isEffectiveAt() && $rule->evaluateConditions($context);
        });
        
        return response()->json([
            'success' => true,
            'vessel' => $vessel,
            'context' => $context,
            'applicable_rules' => $applicableRules
        ]);
    }
}
```

### كنترولر نوع الشرط (ConditionTypeController)

```php
<?php

namespace App\Http\Controllers;

use App\Models\ConditionType;
use App\Http\Requests\ConditionTypeRequest;
use Illuminate\Http\Request;

class ConditionTypeController extends Controller
{
    /**
     * عرض قائمة بجميع أنواع الشروط.
     */
    public function index()
    {
        $conditionTypes = ConditionType::orderBy('name')->get();
        return view('condition-types.index', compact('conditionTypes'));
    }

    /**
     * عرض نموذج إنشاء نوع شرط جديد.
     */
    public function create()
    {
        $dataTypes = [
            'string' => 'نص',
            'number' => 'رقم',
            'boolean' => 'منطقي (نعم/لا)',
            'date' => 'تاريخ',
            'array' => 'قائمة'
        ];
        
        $operatorTypes = [
            'comparison' => 'مقارنة',
            'boolean' => 'منطقي',
            'text' => 'نصي',
            'date' => 'تاريخ'
        ];
        
        $availableOperators = [
            'comparison' => ['=', '!=', '>', '>=', '<', '<='],
            'boolean' => ['=', '!='],
            'text' => ['=', '!=', 'contains', 'starts_with', 'ends_with', 'in', 'not_in'],
            'date' => ['=', '!=', '>', '>=', '<', '<=', 'between']
        ];
        
        $operatorLabels = [
            '=' => 'يساوي',
            '!=' => 'لا يساوي',
            '>' => 'أكبر من',
            '>=' => 'أكبر من أو يساوي',
            '<' => 'أصغر من',
            '<=' => 'أصغر من أو يساوي',
            'in' => 'ضمن القائمة',
            'not_in' => 'ليس ضمن القائمة',
            'between' => 'بين قيمتين',
            'contains' => 'يحتوي على',
            'starts_with' => 'يبدأ بـ',
            'ends_with' => 'ينتهي بـ'
        ];
        
        return view('condition-types.create', compact('dataTypes', 'operatorTypes', 'availableOperators', 'operatorLabels'));
    }

    /**
     * تخزين نوع شرط جديد في قاعدة البيانات.
     */
    public function store(ConditionTypeRequest $request)
    {
        $data = $request->validated();
        
        // تحويل مصفوفة العمليات المتاحة إلى JSON
        if (isset($data['available_operators'])) {
            $data['available_operators'] = json_encode($data['available_operators']);
        } else {
            $data['available_operators'] = json_encode([]);
        }
        
        $conditionType = ConditionType::create($data);
        
        return redirect()->route('condition-types.show', $conditionType)
            ->with('success', 'تم إنشاء نوع الشرط بنجاح.');
    }

    /**
     * عرض تفاصيل نوع شرط محدد.
     */
    public function show(ConditionType $conditionType)
    {
        $dataTypes = [
            'string' => 'نص',
            'number' => 'رقم',
            'boolean' => 'منطقي (نعم/لا)',
            'date' => 'تاريخ',
            'array' => 'قائمة'
        ];
        
        $operatorTypes = [
            'comparison' => 'مقارنة',
            'boolean' => 'منطقي',
            'text' => 'نصي',
            'date' => 'تاريخ'
        ];
        
        $operatorLabels = [
            '=' => 'يساوي',
            '!=' => 'لا يساوي',
            '>' => 'أكبر من',
            '>=' => 'أكبر من أو يساوي',
            '<' => 'أصغر من',
            '<=' => 'أصغر من أو يساوي',
            'in' => 'ضمن القائمة',
            'not_in' => 'ليس ضمن القائمة',
            'between' => 'بين قيمتين',
            'contains' => 'يحتوي على',
            'starts_with' => 'يبدأ بـ',
            'ends_with' => 'ينتهي بـ'
        ];
        
        return view('condition-types.show', compact('conditionType', 'dataTypes', 'operatorTypes', 'operatorLabels'));
    }

    /**
     * عرض نموذج تعديل نوع شرط محدد.
     */
    public function edit(ConditionType $conditionType)
    {
        $dataTypes = [
            'string' => 'نص',
            'number' => 'رقم',
            'boolean' => 'منطقي (نعم/لا)',
            'date' => 'تاريخ',
            'array' => 'قائمة'
        ];
        
        $operatorTypes = [
            'comparison' => 'مقارنة',
            'boolean' => 'منطقي',
            'text' => 'نصي',
            'date' => 'تاريخ'
        ];
        
        $availableOperators = [
            'comparison' => ['=', '!=', '>', '>=', '<', '<='],
            'boolean' => ['=', '!='],
            'text' => ['=', '!=', 'contains', 'starts_with', 'ends_with', 'in', 'not_in'],
            'date' => ['=', '!=', '>', '>=', '<', '<=', 'between']
        ];
        
        $operatorLabels = [
            '=' => 'يساوي',
            '!=' => 'لا يساوي',
            '>' => 'أكبر من',
            '>=' => 'أكبر من أو يساوي',
            '<' => 'أصغر من',
            '<=' => 'أصغر من أو يساوي',
            'in' => 'ضمن القائمة',
            'not_in' => 'ليس ضمن القائمة',
            'between' => 'بين قيمتين',
            'contains' => 'يحتوي على',
            'starts_with' => 'يبدأ بـ',
            'ends_with' => 'ينتهي بـ'
        ];
        
        $selectedOperators = json_decode($conditionType->available_operators, true) ?? [];
        
        return view('condition-types.edit', compact('conditionType', 'dataTypes', 'operatorTypes', 'availableOperators', 'operatorLabels', 'selectedOperators'));
    }

    /**
     * تحديث نوع شرط محدد في قاعدة البيانات.
     */
    public function update(ConditionTypeRequest $request, ConditionType $conditionType)
    {
        $data = $request->validated();
        
        // تحويل مصفوفة العمليات المتاحة إلى JSON
        if (isset($data['available_operators'])) {
            $data['available_operators'] = json_encode($data['available_operators']);
        } else {
            $data['available_operators'] = json_encode([]);
        }
        
        $conditionType->update($data);
        
        return redirect()->route('condition-types.show', $conditionType)
            ->with('success', 'تم تحديث نوع الشرط بنجاح.');
    }

    /**
     * حذف نوع شرط محدد من قاعدة البيانات.
     */
    public function destroy(ConditionType $conditionType)
    {
        $conditionType->delete();
        
        return redirect()->route('condition-types.index')
            ->with('success', 'تم حذف نوع الشرط بنجاح.');
    }

    /**
     * تبديل حالة تفعيل نوع شرط محدد.
     */
    public function toggleActive(ConditionType $conditionType)
    {
        $conditionType->active = !$conditionType->active;
        $conditionType->save();
        
        return redirect()->back()->with('success', 'تم تحديث حالة نوع الشرط بنجاح.');
    }
}
```

### خدمة التسعير (PricingService)

```php
<?php

namespace App\Services;

use App\Models\PricingRule;
use App\Models\TariffCategory;
use Carbon\Carbon;

class PricingService
{
    /**
     * حساب سعر خدمة بناءً على السياق المقدم.
     *
     * @param  \App\Models\TariffCategory  $tariffCategory
     * @param  array  $context
     * @return array
     */
    public function calculatePrice(TariffCategory $tariffCategory, array $context)
    {
        // الحصول على قواعد التسعير النشطة لفئة التعريفة
        $rules = $this->getApplicableRules($tariffCategory, $context);
        
        if ($rules->isEmpty()) {
            return [
                'success' => false,
                'price' => 0,
                'method' => 'pricing_rule',
                'rule_id' => null,
                'message' => 'لا توجد قواعد تسعير مطبقة.'
            ];
        }
        
        // استخدام القاعدة ذات الأولوية الأعلى
        $rule = $rules->first();
        
        return [
            'success' => true,
            'price' => $rule->rate,
            'method' => 'pricing_rule',
            'rule_id' => $rule->id,
            'rule_name' => $rule->name,
            'message' => 'تم تطبيق قاعدة التسعير: ' . $rule->name
        ];
    }

    /**
     * الحصول على قواعد التسعير المطبقة لفئة تعريفة وسياق محددين.
     *
     * @param  \App\Models\TariffCategory  $tariffCategory
     * @param  array  $context
     * @return \Illuminate\Support\Collection
     */
    public function getApplicableRules(TariffCategory $tariffCategory, array $context)
    {
        // الحصول على جميع قواعد التسعير النشطة لفئة التعريفة
        $rules = $tariffCategory->pricingRules()
            ->where('active', true)
            ->orderBy('priority', 'desc')
            ->get();
        
        // تصفية القواعد المطبقة
        return $rules->filter(function ($rule) use ($context) {
            return $rule->isEffectiveAt() && $rule->evaluateConditions($context);
        });
    }

    /**
     * تقييم ما إذا كانت قاعدة تسعير تنطبق على سياق معين.
     *
     * @param  \App\Models\PricingRule  $rule
     * @param  array  $context
     * @return bool
     */
    public function evaluateRule(PricingRule $rule, array $context)
    {
        return $rule->isEffectiveAt() && $rule->evaluateConditions($context);
    }
}
```

## واجهات المستخدم

### قالب التصميم الرئيسي (app.blade.php)

```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'نظام إدارة رسوم السفن') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;500;700&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom Styles -->
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
        }
        
        .navbar-brand img {
            height: 40px;
        }
        
        .sidebar {
            min-height: calc(100vh - 56px);
            background-color: #f8f9fa;
            border-left: 1px solid #dee2e6;
        }
        
        .sidebar .nav-link {
            color: #495057;
            padding: 0.5rem 1rem;
            border-radius: 0.25rem;
            margin-bottom: 0.25rem;
        }
        
        .sidebar .nav-link:hover {
            background-color: #e9ecef;
        }
        
        .sidebar .nav-link.active {
            background-color: #0d6efd;
            color: white;
        }
        
        .sidebar .nav-link i {
            margin-left: 0.5rem;
        }
        
        .content {
            padding: 1.5rem;
        }
        
        .footer {
            background-color: #f8f9fa;
            padding: 1rem 0;
            border-top: 1px solid #dee2e6;
        }
    </style>
    
    @yield('styles')
</head>
<body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    <img src="https://via.placeholder.com/150x50?text=SAFEEN" alt="{{ config('app.name', 'نظام إدارة رسوم السفن') }}">
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ url('/') }}">
                                <i class="fas fa-home"></i> الرئيسية
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                                <i class="fas fa-cogs"></i> الخدمات
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('tariff-categories.*') ? 'active' : '' }}" href="{{ route('tariff-categories.index') }}">
                                <i class="fas fa-tags"></i> فئات التعريفة
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('pricing-rules.*') ? 'active' : '' }}" href="{{ route('pricing-rules.index') }}">
                                <i class="fas fa-calculator"></i> قواعد التسعير
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('condition-types.*') ? 'active' : '' }}" href="{{ route('condition-types.index') }}">
                                <i class="fas fa-filter"></i> أنواع الشروط
                            </a>
                        </li>
                    </ul>
                    <ul class="navbar-nav">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user"></i> المستخدم
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <li><a class="dropdown-item" href="#"><i class="fas fa-user-cog"></i> الإعدادات</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="#" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i> تسجيل الخروج
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main>
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-3 col-lg-2 d-md-block sidebar collapse">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ url('/') }}">
                                    <i class="fas fa-home"></i> الرئيسية
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('services.*') ? 'active' : '' }}" href="{{ route('services.index') }}">
                                    <i class="fas fa-cogs"></i> الخدمات
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('tariff-categories.*') ? 'active' : '' }}" href="{{ route('tariff-categories.index') }}">
                                    <i class="fas fa-tags"></i> فئات التعريفة
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('pricing-rules.*') ? 'active' : '' }}" href="{{ route('pricing-rules.index') }}">
                                    <i class="fas fa-calculator"></i> قواعد التسعير
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link {{ request()->routeIs('condition-types.*') ? 'active' : '' }}" href="{{ route('condition-types.index') }}">
                                    <i class="fas fa-filter"></i> أنواع الشروط
                                </a>
                            </li>
                            <li class="nav-item mt-3">
                                <hr>
                                <h6 class="sidebar-heading px-3 mt-4 mb-1 text-muted">
                                    <i class="fas fa-chart-bar"></i> التقارير
                                </h6>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-file-invoice-dollar"></i> تقرير الفواتير
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">
                                    <i class="fas fa-ship"></i> تقرير السفن
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-9 col-lg-10 content">
                    @yield('content')
                </div>
            </div>
        </div>
    </main>

    <footer class="footer mt-auto py-3">
        <div class="container text-center">
            <span class="text-muted">© {{ date('Y') }} نظام إدارة رسوم السفن. جميع الحقوق محفوظة.</span>
        </div>
    </footer>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')
</body>
</html>
```

### واجهة إنشاء قاعدة تسعير (pricing-rules/create.blade.php)

```html
@extends('layouts.app')

@section('styles')
<style>
    .condition-group {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #f8f9fa;
    }
    
    .condition-item {
        border: 1px solid #e0e0e0;
        border-radius: 5px;
        padding: 10px;
        margin-bottom: 10px;
        background-color: #fff;
    }
    
    .nested-group {
        margin-left: 20px;
        border-left: 3px solid #007bff;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">إضافة قاعدة تسعير جديدة</h5>
                    <a href="{{ route('pricing-rules.index') }}" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-right"></i> العودة للقائمة
                    </a>
                </div>

                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('pricing-rules.store') }}" method="POST" id="pricing-rule-form">
                        @csrf
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="tariff_category_id" class="form-label">فئة التعريفة <span class="text-danger">*</span></label>
                                <select class="form-select @error('tariff_category_id') is-invalid @enderror" id="tariff_category_id" name="tariff_category_id" required>
                                    <option value="">-- اختر فئة التعريفة --</option>
                                    @foreach ($tariffCategories as $id => $name)
                                        <option value="{{ $id }}" {{ old('tariff_category_id', $selectedTariffCategoryId) == $id ? 'selected' : '' }}>{{ $name }}</option>
                                    @endforeach
                                </select>
                                @error('tariff_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="name" class="form-label">اسم القاعدة <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="rate" class="form-label">السعر الأساسي <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" step="0.01" min="0" class="form-control @error('rate') is-invalid @enderror" id="rate" name="rate" value="{{ old('rate') }}" required>
                                    <span class="input-group-text">درهم</span>
                                </div>
                                @error('rate')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="priority" class="form-label">الأولوية <span class="text-danger">*</span></label>
                                <input type="number" step="1" min="0" class="form-control @error('priority') is-invalid @enderror" id="priority" name="priority" value="{{ old('priority', 10) }}" required>
                                @error('priority')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">القيمة الأعلى تعني أولوية أعلى</small>
                            </div>
                            
                            <div class="col-md-4">
                                <div class="mb-3 form-check mt-4">
                                    <input type="checkbox" class="form-check-input" id="active" name="active" {{ old('active') ? 'checked' : 'checked' }}>
                                    <label class="form-check-label" for="active">مفعل</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="effective_from" class="form-label">تاريخ بدء السريان</label>
                                <input type="date" class="form-control @error('effective_from') is-invalid @enderror" id="effective_from" name="effective_from" value="{{ old('effective_from') }}">
                                @error('effective_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-6">
                                <label for="effective_to" class="form-label">تاريخ انتهاء السريان</label>
                                <input type="date" class="form-control @error('effective_to') is-invalid @enderror" id="effective_to" name="effective_to" value="{{ old('effective_to') }}">
                                @error('effective_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <hr>
                        
                        <h5 class="mb-3">شروط تطبيق القاعدة</h5>
                        
                        <div id="conditions-builder" class="mb-4">
                            <div class="condition-group" id="root-group">
                                <div class="mb-3">
                                    <label class="form-label">نوع العملية</label>
                                    <select class="form-select operator-select">
                                        <option value="AND">AND - يجب تحقق جميع الشروط</option>
                                        <option value="OR">OR - يكفي تحقق أحد الشروط</option>
                                    </select>
                                </div>
                                
                                <div class="conditions-container">
                                    <!-- هنا سيتم إضافة الشروط بشكل ديناميكي -->
                                </div>
                                
                                <div class="mt-2">
                                    <button type="button" class="btn btn-sm btn-primary add-condition">
                                        <i class="fas fa-plus"></i> إضافة شرط
                                    </button>
                                    <button type="button" class="btn btn-sm btn-info add-group">
                                        <i class="fas fa-layer-group"></i> إضافة مجموعة شروط
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="conditions" id="conditions-json" value="{{ old('conditions', '{"operator":"AND","conditions":[]}') }}">
                        @error('conditions')
                            <div class="text-danger mb-3">{{ $message }}</div>
                        @enderror
                        
                        @if($selectedTariffCategoryId)
                            <input type="hidden" name="redirect_to_category" value="1">
                        @endif
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary">حفظ</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // بيانات أنواع الشروط
        const conditionTypes = @json($conditionTypes);
        
        // استرجاع الشروط المحفوظة سابقًا (إن وجدت)
        let savedConditions = null;
        try {
            savedConditions = JSON.parse(document.getElementById('conditions-json').value);
        } catch (e) {
            savedConditions = { operator: "AND", conditions: [] };
        }
        
        // تهيئة منشئ الشروط
        initConditionsBuilder(savedConditions);
        
        // تحديث حقل الشروط المخفي قبل إرسال النموذج
        document.getElementById('pricing-rule-form').addEventListener('submit', function() {
            const conditionsJson = buildConditionsJson();
            document.getElementById('conditions-json').value = JSON.stringify(conditionsJson);
        });
        
        // تهيئة منشئ الشروط
        function initConditionsBuilder(initialConditions) {
            const rootGroup = document.getElementById('root-group');
            
            // تعيين نوع العملية
            rootGroup.querySelector('.operator-select').value = initialConditions.operator;
            
            // إضافة الشروط المحفوظة
            if (initialConditions.conditions && initialConditions.conditions.length > 0) {
                initialConditions.conditions.forEach(condition => {
                    if (condition.conditions) {
                        // مجموعة شروط
                        addConditionGroup(rootGroup.querySelector('.conditions-container'), condition);
                    } else {
                        // شرط فردي
                        addCondition(rootGroup.querySelector('.conditions-container'), condition);
                    }
                });
            }
            
            // إضافة مستمعي الأحداث للأزرار
            setupEventListeners(rootGroup);
        }
        
        // إعداد مستمعي الأحداث
        function setupEventListeners(container) {
            // زر إضافة شرط
            container.querySelector('.add-condition').addEventListener('click', function() {
                addCondition(container.querySelector('.conditions-container'));
            });
            
            // زر إضافة مجموعة شروط
            container.querySelector('.add-group').addEventListener('click', function() {
                addConditionGroup(container.querySelector('.conditions-container'));
            });
        }
        
        // إضافة شرط فردي
        function addCondition(container, initialData = null) {
            const conditionItem = document.createElement('div');
            conditionItem.className = 'condition-item';
            
            let typeOptions = '<option value="">-- اختر نوع الشرط --</option>';
            conditionTypes.forEach(type => {
                const selected = initialData && initialData.type === type.code ? 'selected' : '';
                typeOptions += `<option value="${type.code}" ${selected}>${type.name}</option>`;
            });
            
            conditionItem.innerHTML = `
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">نوع الشرط</label>
                        <select class="form-select condition-type">
                            ${typeOptions}
                        </select>
                    </div>
                    <div class="col-md-3 mb-2">
                        <label class="form-label">العملية</label>
                        <select class="form-select condition-operator">
                            <option value="">-- اختر العملية --</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">القيمة</label>
                        <input type="text" class="form-control condition-value" value="${initialData ? initialData.value : ''}">
                    </div>
                    <div class="col-md-1 mb-2 d-flex align-items-end">
                        <button type="button" class="btn btn-danger btn-sm remove-condition">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            
            container.appendChild(conditionItem);
            
            // إضافة مستمع حدث لزر الحذف
            conditionItem.querySelector('.remove-condition').addEventListener('click', function() {
                container.removeChild(conditionItem);
            });
            
            // إضافة مستمع حدث لتغيير نوع الشرط
            const typeSelect = conditionItem.querySelector('.condition-type');
            const operatorSelect = conditionItem.querySelector('.condition-operator');
            
            typeSelect.addEventListener('change', function() {
                updateOperators(typeSelect.value, operatorSelect);
            });
            
            // إذا كان هناك بيانات أولية، قم بتحديث العمليات وتحديد العملية المناسبة
            if (initialData && initialData.type) {
                updateOperators(initialData.type, operatorSelect, initialData.operator);
            }
        }
        
        // إضافة مجموعة شروط
        function addConditionGroup(container, initialData = null) {
            const groupDiv = document.createElement('div');
            groupDiv.className = 'condition-group nested-group';
            
            groupDiv.innerHTML = `
                <div class="mb-3">
                    <label class="form-label">نوع العملية</label>
                    <select class="form-select operator-select">
                        <option value="AND" ${initialData && initialData.operator === 'AND' ? 'selected' : ''}>AND - يجب تحقق جميع الشروط</option>
                        <option value="OR" ${initialData && initialData.operator === 'OR' ? 'selected' : ''}>OR - يكفي تحقق أحد الشروط</option>
                    </select>
                </div>
                
                <div class="conditions-container">
                    <!-- هنا سيتم إضافة الشروط بشكل ديناميكي -->
                </div>
                
                <div class="mt-2">
                    <button type="button" class="btn btn-sm btn-primary add-condition">
                        <i class="fas fa-plus"></i> إضافة شرط
                    </button>
                    <button type="button" class="btn btn-sm btn-info add-group">
                        <i class="fas fa-layer-group"></i> إضافة مجموعة شروط
                    </button>
                    <button type="button" class="btn btn-sm btn-danger remove-group">
                        <i class="fas fa-trash"></i> حذف المجموعة
                    </button>
                </div>
            `;
            
            container.appendChild(groupDiv);
            
            // إضافة مستمعي الأحداث للأزرار
            groupDiv.querySelector('.add-condition').addEventListener('click', function() {
                addCondition(groupDiv.querySelector('.conditions-container'));
            });
            
            groupDiv.querySelector('.add-group').addEventListener('click', function() {
                addConditionGroup(groupDiv.querySelector('.conditions-container'));
            });
            
            groupDiv.querySelector('.remove-group').addEventListener('click', function() {
                container.removeChild(groupDiv);
            });
            
            // إضافة الشروط المحفوظة إذا وجدت
            if (initialData && initialData.conditions && initialData.conditions.length > 0) {
                initialData.conditions.forEach(condition => {
                    if (condition.conditions) {
                        // مجموعة شروط
                        addConditionGroup(groupDiv.querySelector('.conditions-container'), condition);
                    } else {
                        // شرط فردي
                        addCondition(groupDiv.querySelector('.conditions-container'), condition);
                    }
                });
            }
        }
        
        // تحديث قائمة العمليات بناءً على نوع الشرط
        function updateOperators(typeCode, operatorSelect, selectedOperator = null) {
            operatorSelect.innerHTML = '<option value="">-- اختر العملية --</option>';
            
            const conditionType = conditionTypes.find(type => type.code === typeCode);
            if (!conditionType) return;
            
            let operators = [];
            try {
                operators = JSON.parse(conditionType.available_operators);
            } catch (e) {
                operators = [];
            }
            
            const operatorLabels = {
                '=': 'يساوي',
                '!=': 'لا يساوي',
                '>': 'أكبر من',
                '>=': 'أكبر من أو يساوي',
                '<': 'أصغر من',
                '<=': 'أصغر من أو يساوي',
                'in': 'ضمن القائمة',
                'not_in': 'ليس ضمن القائمة',
                'between': 'بين قيمتين',
                'contains': 'يحتوي على',
                'starts_with': 'يبدأ بـ',
                'ends_with': 'ينتهي بـ'
            };
            
            operators.forEach(op => {
                const label = operatorLabels[op] || op;
                const selected = selectedOperator === op ? 'selected' : '';
                operatorSelect.innerHTML += `<option value="${op}" ${selected}>${label}</option>`;
            });
        }
        
        // بناء كائن JSON للشروط
        function buildConditionsJson() {
            const rootGroup = document.getElementById('root-group');
            return buildGroupJson(rootGroup);
        }
        
        // بناء كائن JSON لمجموعة شروط
        function buildGroupJson(groupElement) {
            const operator = groupElement.querySelector('.operator-select').value;
            const conditions = [];
            
            // جمع الشروط الفردية والمجموعات
            const conditionsContainer = groupElement.querySelector('.conditions-container');
            conditionsContainer.childNodes.forEach(child => {
                if (child.classList && child.classList.contains('condition-item')) {
                    // شرط فردي
                    const typeSelect = child.querySelector('.condition-type');
                    const operatorSelect = child.querySelector('.condition-operator');
                    const valueInput = child.querySelector('.condition-value');
                    
                    if (typeSelect.value && operatorSelect.value) {
                        conditions.push({
                            type: typeSelect.value,
                            operator: operatorSelect.value,
                            value: valueInput.value
                        });
                    }
                } else if (child.classList && child.classList.contains('condition-group')) {
                    // مجموعة شروط
                    conditions.push(buildGroupJson(child));
                }
            });
            
            return {
                operator: operator,
                conditions: conditions
            };
        }
    });
</script>
@endsection
```

## محرك قواعد التسعير

### هيكل قواعد التسعير

قواعد التسعير في النظام تتكون من:

1. **المعلومات الأساسية**:
   - فئة التعريفة المرتبطة بها
   - الاسم
   - السعر الأساسي
   - الأولوية
   - تاريخ بدء السريان (اختياري)
   - تاريخ انتهاء السريان (اختياري)
   - حالة التفعيل

2. **شروط التطبيق**:
   - تُخزن في حقل JSON يدعم الشروط المعقدة والمتداخلة
   - تدعم عمليات AND/OR المنطقية
   - يمكن تعشيش مجموعات الشروط بلا حدود

### هيكل الشروط

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
      "type": "gt_size",
      "operator": "between",
      "value": "[5000, 10000]"
    },
    {
      "operator": "OR",
      "conditions": [
        {
          "type": "call_type",
          "operator": "=",
          "value": "IMPORT"
        },
        {
          "type": "call_type",
          "operator": "=",
          "value": "EXPORT"
        }
      ]
    }
  ]
}
```

### آلية تقييم الشروط

1. **تقييم مجموعة الشروط**:
   - إذا كان العامل هو AND، يجب أن تتحقق جميع الشروط
   - إذا كان العامل هو OR، يكفي أن يتحقق شرط واحد

2. **تقييم الشرط الفردي**:
   - مقارنة قيمة السياق مع قيمة الشرط باستخدام العملية المحددة
   - دعم مجموعة واسعة من العمليات مثل =، !=، >، >=، <، <=، in، not_in، between، contains، starts_with، ends_with

3. **تقييم الشروط المتداخلة**:
   - يتم تقييم كل مجموعة شروط بشكل مستقل
   - يتم دمج النتائج باستخدام العامل المنطقي للمجموعة الأم

### آلية اختيار قاعدة التسعير

1. **تصفية القواعد النشطة**:
   - استبعاد القواعد غير النشطة
   - استبعاد القواعد خارج فترة السريان

2. **تقييم الشروط**:
   - تقييم شروط كل قاعدة باستخدام سياق التقييم
   - استبعاد القواعد التي لا تنطبق شروطها

3. **ترتيب حسب الأولوية**:
   - ترتيب القواعد المطبقة حسب الأولوية (تنازلياً)
   - اختيار القاعدة ذات الأولوية الأعلى

## دليل الاستخدام

### إدارة الخدمات

#### إنشاء خدمة جديدة

1. انتقل إلى صفحة "الخدمات"
2. انقر على زر "إضافة خدمة جديدة"
3. أدخل المعلومات المطلوبة:
   - الكود: رمز فريد للخدمة (مثل: ANCHORAGE)
   - الاسم: اسم الخدمة (مثل: رسوم المرساة)
   - الوصف: وصف اختياري للخدمة
4. انقر على زر "حفظ"

#### تعديل خدمة

1. انتقل إلى صفحة "الخدمات"
2. انقر على زر "تعديل" بجانب الخدمة المطلوبة
3. قم بتعديل المعلومات حسب الحاجة
4. انقر على زر "تحديث"

### إدارة فئات التعريفة

#### إنشاء فئة تعريفة جديدة

1. انتقل إلى صفحة "فئات التعريفة"
2. انقر على زر "إضافة فئة تعريفة جديدة"
3. أدخل المعلومات المطلوبة:
   - الخدمة: اختر الخدمة المرتبطة
   - الكود: رمز فريد لفئة التعريفة (مثل: ANCHORAGE_CARGO)
   - الاسم: اسم فئة التعريفة (مثل: رسوم المرساة للسفن التجارية)
   - وحدة القياس: وحدة قياس الرسوم (مثل: يوم، ساعة، GT)
   - الوصف: وصف اختياري لفئة التعريفة
4. انقر على زر "حفظ"

#### تعديل فئة تعريفة

1. انتقل إلى صفحة "فئات التعريفة"
2. انقر على زر "تعديل" بجانب فئة التعريفة المطلوبة
3. قم بتعديل المعلومات حسب الحاجة
4. انقر على زر "تحديث"

### إدارة أنواع الشروط

#### إنشاء نوع شرط جديد

1. انتقل إلى صفحة "أنواع الشروط"
2. انقر على زر "إضافة نوع شرط جديد"
3. أدخل المعلومات المطلوبة:
   - الكود: رمز فريد لنوع الشرط (مثل: vessel_type)
   - الاسم: اسم نوع الشرط (مثل: نوع السفينة)
   - نوع البيانات: نوع البيانات (نص، رقم، منطقي، تاريخ، قائمة)
   - نوع العمليات: نوع العمليات المتاحة (مقارنة، منطقي، نصي، تاريخ)
   - العمليات المتاحة: اختر العمليات المتاحة لهذا النوع
4. انقر على زر "حفظ"

#### تعديل نوع شرط

1. انتقل إلى صفحة "أنواع الشروط"
2. انقر على زر "تعديل" بجانب نوع الشرط المطلوب
3. قم بتعديل المعلومات حسب الحاجة
4. انقر على زر "تحديث"

### إدارة قواعد التسعير

#### إنشاء قاعدة تسعير جديدة

1. انتقل إلى صفحة "قواعد التسعير"
2. انقر على زر "إضافة قاعدة تسعير جديدة"
3. أدخل المعلومات المطلوبة:
   - فئة التعريفة: اختر فئة التعريفة المرتبطة
   - اسم القاعدة: اسم وصفي للقاعدة
   - السعر الأساسي: السعر الأساسي للقاعدة
   - الأولوية: أولوية القاعدة (القيمة الأعلى تعني أولوية أعلى)
   - تاريخ بدء السريان: تاريخ بدء سريان القاعدة (اختياري)
   - تاريخ انتهاء السريان: تاريخ انتهاء سريان القاعدة (اختياري)
4. أضف شروط تطبيق القاعدة:
   - انقر على زر "إضافة شرط" لإضافة شرط فردي
   - انقر على زر "إضافة مجموعة شروط" لإضافة مجموعة شروط متداخلة
   - اختر نوع العملية (AND/OR) لكل مجموعة شروط
   - لكل شرط فردي، اختر نوع الشرط والعملية والقيمة
5. انقر على زر "حفظ"

#### تعديل قاعدة تسعير

1. انتقل إلى صفحة "قواعد التسعير"
2. انقر على زر "تعديل" بجانب قاعدة التسعير المطلوبة
3. قم بتعديل المعلومات وشروط التطبيق حسب الحاجة
4. انقر على زر "تحديث"

#### اختبار قاعدة تسعير

1. انتقل إلى صفحة تفاصيل قاعدة التسعير
2. انتقل إلى قسم "اختبار القاعدة"
3. أدخل قيم الاختبار لكل نوع شرط مستخدم في القاعدة
4. انقر على زر "حساب السعر"
5. ستظهر نتيجة الاختبار مع السعر المحسوب إذا كانت القاعدة تنطبق

### استخدام قواعد التسعير في الفواتير

#### إنشاء فاتورة جديدة

1. عند إنشاء فاتورة جديدة، يقوم النظام تلقائياً بتطبيق قواعد التسعير المناسبة
2. يتم اختيار القاعدة ذات الأولوية الأعلى من بين القواعد المطبقة
3. يتم حساب السعر بناءً على القاعدة المختارة
4. يتم تخزين معلومات التسعير في الفاتورة للرجوع إليها لاحقاً

## خطوات التنفيذ

### 1. إنشاء هجرات قاعدة البيانات

1. إنشاء هجرة لجدول الخدمات:
```bash
php artisan make:migration create_services_table
```

2. إنشاء هجرة لجدول فئات التعريفة:
```bash
php artisan make:migration create_tariff_categories_table
```

3. إنشاء هجرة لجدول قواعد التسعير:
```bash
php artisan make:migration create_pricing_rules_table
```

4. إنشاء هجرة لجدول أنواع الشروط:
```bash
php artisan make:migration create_condition_types_table
```

5. إنشاء هجرة لإضافة حقل tariff_category_id إلى جدول fixed_fees:
```bash
php artisan make:migration add_tariff_category_id_to_fixed_fees_table
```

6. إنشاء هجرة لإضافة حقول pricing_method و pricing_context إلى جدول invoice_fees:
```bash
php artisan make:migration add_pricing_fields_to_invoice_fees_table
```

### 2. إنشاء النماذج

1. إنشاء نموذج Service:
```bash
php artisan make:model Service
```

2. إنشاء نموذج TariffCategory:
```bash
php artisan make:model TariffCategory
```

3. إنشاء نموذج PricingRule:
```bash
php artisan make:model PricingRule
```

4. إنشاء نموذج ConditionType:
```bash
php artisan make:model ConditionType
```

### 3. إنشاء الكنترولرات

1. إنشاء كنترولر ServiceController:
```bash
php artisan make:controller ServiceController --resource
```

2. إنشاء كنترولر TariffCategoryController:
```bash
php artisan make:controller TariffCategoryController --resource
```

3. إنشاء كنترولر PricingRuleController:
```bash
php artisan make:controller PricingRuleController --resource
```

4. إنشاء كنترولر ConditionTypeController:
```bash
php artisan make:controller ConditionTypeController --resource
```

### 4. إنشاء طلبات التحقق

1. إنشاء طلب ServiceRequest:
```bash
php artisan make:request ServiceRequest
```

2. إنشاء طلب TariffCategoryRequest:
```bash
php artisan make:request TariffCategoryRequest
```

3. إنشاء طلب PricingRuleRequest:
```bash
php artisan make:request PricingRuleRequest
```

4. إنشاء طلب ConditionTypeRequest:
```bash
php artisan make:request ConditionTypeRequest
```

### 5. إنشاء خدمة التسعير

1. إنشاء مجلد Services في app/:
```bash
mkdir -p app/Services
```

2. إنشاء ملف PricingService.php في app/Services/

### 6. إنشاء واجهات المستخدم

1. إنشاء قالب التصميم الرئيسي:
```bash
mkdir -p resources/views/layouts
touch resources/views/layouts/app.blade.php
```

2. إنشاء واجهات الخدمات:
```bash
mkdir -p resources/views/services
touch resources/views/services/index.blade.php
touch resources/views/services/create.blade.php
touch resources/views/services/edit.blade.php
touch resources/views/services/show.blade.php
```

3. إنشاء واجهات فئات التعريفة:
```bash
mkdir -p resources/views/tariff-categories
touch resources/views/tariff-categories/index.blade.php
touch resources/views/tariff-categories/create.blade.php
touch resources/views/tariff-categories/edit.blade.php
touch resources/views/tariff-categories/show.blade.php
```

4. إنشاء واجهات قواعد التسعير:
```bash
mkdir -p resources/views/pricing-rules
touch resources/views/pricing-rules/index.blade.php
touch resources/views/pricing-rules/create.blade.php
touch resources/views/pricing-rules/edit.blade.php
touch resources/views/pricing-rules/show.blade.php
```

5. إنشاء واجهات أنواع الشروط:
```bash
mkdir -p resources/views/condition-types
touch resources/views/condition-types/index.blade.php
touch resources/views/condition-types/create.blade.php
touch resources/views/condition-types/edit.blade.php
touch resources/views/condition-types/show.blade.php
```

### 7. تحديث ملف الطرق

1. تحديث ملف routes/web.php لإضافة طرق النظام

### 8. تنفيذ الهجرات

```bash
php artisan migrate
```

### 9. إنشاء بذور البيانات (اختياري)

1. إنشاء بذرة لبيانات SAFEEN:
```bash
php artisan make:seeder SAFEENTariffSeeder
```

2. تنفيذ البذرة:
```bash
php artisan db:seed --class=SAFEENTariffSeeder
```

## الأسئلة الشائعة

### كيف يمكنني إنشاء شروط معقدة؟

يمكنك إنشاء شروط معقدة باستخدام مجموعات الشروط المتداخلة. يمكنك إضافة مجموعة شروط داخل مجموعة أخرى، واختيار نوع العملية (AND/OR) لكل مجموعة. هذا يتيح لك إنشاء تعبيرات منطقية معقدة مثل:

```
(نوع السفينة = CARGO) AND (الحجم الإجمالي بين 5000 و 10000) AND ((نوع الزيارة = IMPORT) OR (نوع الزيارة = EXPORT))
```

### كيف يتم اختيار قاعدة التسعير المناسبة؟

يتم اختيار قاعدة التسعير المناسبة بناءً على الخطوات التالية:

1. تصفية القواعد النشطة وضمن فترة السريان
2. تقييم شروط كل قاعدة باستخدام سياق التقييم (بيانات السفينة والزيارة)
3. ترتيب القواعد المطبقة حسب الأولوية (تنازلياً)
4. اختيار القاعدة ذات الأولوية الأعلى

### ما هي أنواع الشروط المدعومة؟

يدعم النظام أنواع الشروط التالية:

- **نص (string)**: للقيم النصية مثل نوع السفينة
- **رقم (number)**: للقيم الرقمية مثل الحجم الإجمالي
- **منطقي (boolean)**: للقيم المنطقية (نعم/لا)
- **تاريخ (date)**: للقيم التاريخية
- **قائمة (array)**: للقوائم مثل قائمة أنواع السفن

### ما هي العمليات المدعومة؟

يدعم النظام العمليات التالية:

- **=**: يساوي
- **!=**: لا يساوي
- **>**: أكبر من
- **>=**: أكبر من أو يساوي
- **<**: أصغر من
- **<=**: أصغر من أو يساوي
- **in**: ضمن القائمة
- **not_in**: ليس ضمن القائمة
- **between**: بين قيمتين
- **contains**: يحتوي على
- **starts_with**: يبدأ بـ
- **ends_with**: ينتهي بـ

### كيف يمكنني اختبار قاعدة تسعير؟

يمكنك اختبار قاعدة تسعير من خلال صفحة تفاصيل القاعدة. انتقل إلى قسم "اختبار القاعدة"، وأدخل قيم الاختبار لكل نوع شرط مستخدم في القاعدة، ثم انقر على زر "حساب السعر". ستظهر نتيجة الاختبار مع السعر المحسوب إذا كانت القاعدة تنطبق.

### كيف يمكنني تحديد فترة سريان قاعدة التسعير؟

يمكنك تحديد فترة سريان قاعدة التسعير من خلال حقلي "تاريخ بدء السريان" و"تاريخ انتهاء السريان" عند إنشاء أو تعديل القاعدة. إذا لم يتم تحديد هذه الحقول، فستكون القاعدة سارية دائمًا.

### كيف يمكنني تعطيل قاعدة تسعير مؤقتًا؟

يمكنك تعطيل قاعدة تسعير مؤقتًا من خلال إلغاء تحديد خانة "مفعل" عند إنشاء أو تعديل القاعدة. يمكنك أيضًا استخدام زر تبديل الحالة في قائمة قواعد التسعير.
