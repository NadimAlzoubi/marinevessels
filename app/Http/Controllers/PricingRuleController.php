<?php

namespace App\Http\Controllers;

use App\Models\PricingRule;
use App\Models\TariffCategory;
use App\Models\ConditionType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PricingRuleController extends Controller
{
    /**
     * عرض قائمة قواعد التسعير
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $pricingRules = PricingRule::with('tariffCategory.service')
            ->orderBy('priority', 'desc')
            ->paginate(10);
        return view('pricing-rules.index', compact('pricingRules'));
    }

    /**
     * عرض نموذج إنشاء قاعدة تسعير جديدة
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $tariffCategories = TariffCategory::where('active', true)
            ->with('service')
            ->get()
            ->pluck('name', 'id');
        $conditionTypes = ConditionType::where('active', true)
            ->orderBy('name')
            ->get();

        $selectedTariffCategoryId = $request->input('tariff_category_id');

        return view('pricing-rules.create', compact('tariffCategories', 'conditionTypes', 'selectedTariffCategoryId'));
    }

    /**
     * تخزين قاعدة تسعير جديدة في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'tariff_category_id' => 'required|exists:tariff_categories,id',
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'conditions' => 'required|json',
            'priority' => 'required|integer|min:0',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'active' => 'nullable|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pricingRule = PricingRule::create([
            'tariff_category_id' => $request->tariff_category_id,
            'name' => $request->name,
            'rate' => $request->rate,
            'conditions' => $request->conditions,
            'priority' => $request->priority,
            'effective_from' => $request->effective_from,
            'effective_to' => $request->effective_to,
            'active' => $request->has('active'),
        ]);

        if ($request->has('redirect_to_category') && $request->redirect_to_category) {
            return redirect()->route('tariff-categories.show', $pricingRule->tariff_category_id)
                ->with('success', 'تم إنشاء قاعدة التسعير بنجاح.');
        }

        return redirect()->route('pricing-rules.index')
            ->with('success', 'تم إنشاء قاعدة التسعير بنجاح.');
    }

    /**
     * عرض تفاصيل قاعدة تسعير محددة
     *
     * @param  \App\Models\PricingRule  $pricingRule
     * @return \Illuminate\View\View
     */
    public function show(PricingRule $pricingRule)
    {
        $conditionTypes = ConditionType::where('active', true)->get();
        return view('pricing-rules.show', compact('pricingRule', 'conditionTypes'));
    }

    /**
     * عرض نموذج تعديل قاعدة تسعير محددة
     *
     * @param  \App\Models\PricingRule  $pricingRule
     * @return \Illuminate\View\View
     */
    public function edit(PricingRule $pricingRule)
    {
        $tariffCategories = TariffCategory::where('active', true)
            ->with('service')
            ->get()
            ->pluck('name_with_service', 'id');

        $conditionTypes = ConditionType::where('active', true)
            ->orderBy('name')
            ->get();

        return view('pricing-rules.edit', compact('pricingRule', 'tariffCategories', 'conditionTypes'));
    }

    /**
     * تحديث قاعدة تسعير محددة في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PricingRule  $pricingRule
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, PricingRule $pricingRule)
    {
        $validator = Validator::make($request->all(), [
            'tariff_category_id' => 'required|exists:tariff_categories,id',
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'conditions' => 'required|json',
            'priority' => 'required|integer|min:0',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'active' => 'nullable|accepted',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $pricingRule->update([
            'tariff_category_id' => $request->tariff_category_id,
            'name' => $request->name,
            'rate' => $request->rate,
            'conditions' => $request->conditions,
            'priority' => $request->priority,
            'effective_from' => $request->effective_from,
            'effective_to' => $request->effective_to,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('pricing-rules.index')
            ->with('success', 'تم تحديث قاعدة التسعير بنجاح.');
    }

    /**
     * حذف قاعدة تسعير محددة من قاعدة البيانات
     *
     * @param  \App\Models\PricingRule  $pricingRule
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(PricingRule $pricingRule)
    {
        $pricingRule->delete();

        return redirect()->route('pricing-rules.index')
            ->with('success', 'تم حذف قاعدة التسعير بنجاح.');
    }

    /**
     * تغيير حالة نشاط قاعدة التسعير
     *
     * @param  \App\Models\PricingRule  $pricingRule
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(PricingRule $pricingRule)
    {
        $pricingRule->update([
            'active' => !$pricingRule->active
        ]);

        $status = $pricingRule->active ? 'تفعيل' : 'تعطيل';
        return redirect()->route('pricing-rules.index')
            ->with('success', "تم {$status} قاعدة التسعير بنجاح.");
    }

    /**
     * اختبار قاعدة تسعير محددة
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PricingRule  $pricingRule
     * @return \Illuminate\Http\JsonResponse
     */
    public function testRule(Request $request, PricingRule $pricingRule)
    {
        $context = $request->input('context', []);
        // dd($pricingRule->conditions, gettype($pricingRule->conditions));
        // dd($pricingRule->conditions);

        try {
            $isApplicable = $pricingRule->isApplicable($context);

            if ($isApplicable) {
                // حساب السعر النهائي باستخدام السياق
                $price = $this->calculatePrice($pricingRule, $context);

                return response()->json([
                    'success' => true,
                    'applicable' => true,
                    'message' => 'القاعدة قابلة للتطبيق على السياق المحدد',
                    'price' => $price,
                    'unit' => $pricingRule->tariffCategory->unit_of_measurement
                ]);
            } else {
                return response()->json([
                    'success' => true,
                    'applicable' => false,
                    'message' => 'القاعدة غير قابلة للتطبيق على السياق المحدد'
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء اختبار القاعدة: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * حساب السعر النهائي بناءً على قاعدة التسعير والسياق
     *
     * @param  \App\Models\PricingRule  $rule
     * @param  array  $context
     * @return float
     */
    private function calculatePrice(PricingRule $rule, array $context): float
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

            case '5.3': // السفن الأخرى التي تبقى ≤ 5 أيام
                $stayDuration = $context['stay_duration'] ?? 0;
                return $baseRate * ($context['gt_size'] ?? 0) * $stayDuration;

            case '5.4': // السفن الأخرى التي تبقى > 5 أيام
                $stayDuration = $context['stay_duration'] ?? 0;
                return $baseRate * ($context['gt_size'] ?? 0) * $stayDuration;

                // رسوم القناة
            case '6.3': // للسفن ≤ 100 طن إجمالي
            case '6.4': // للسفن 100.01 طن إجمالي إلى 500 طن إجمالي
                return $baseRate;

            case '6.5': // للسفن 500.01 طن إجمالي إلى 1,000 طن إجمالي
                // رسوم إضافية بقيمة 1.25 درهم لكل سفينة لكل طن إجمالي
                return $baseRate + (1.25 * ($context['gt_size'] ?? 0));

            case '6.6': // للسفن 1,000.01 طن إجمالي إلى 3,000 طن إجمالي
            case '6.7': // للسفن < 3,000.01 وما فوق
                // رسوم إضافية بقيمة 1.75 درهم لكل سفينة لكل طن إجمالي
                return $baseRate + (1.75 * ($context['gt_size'] ?? 0));

                // الإرشاد
            case '7.1': // السفن بطول: حتى 100 متر
            case '7.2': // السفن بطول: 100.01 متر إلى 160 متر
            case '7.3': // السفن بطول: 160.01 متر إلى 250 متر
            case '7.4': // السفن بطول: 250 متر وما فوق
                $hours = $context['service_hours'] ?? 1;
                return $baseRate * $hours;

                // الحساب الافتراضي
            default:
                return $baseRate;
        }
    }
}
