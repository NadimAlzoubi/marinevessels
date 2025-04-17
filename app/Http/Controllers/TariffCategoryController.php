<?php

namespace App\Http\Controllers;

use App\Models\TariffCategory;
use App\Models\Service;
use Illuminate\Http\Request;

class TariffCategoryController extends Controller
{
    /**
     * عرض قائمة فئات التعريفة
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $tariffCategories = TariffCategory::with('service')->orderBy('code')->paginate(10);
        return view('tariff-categories.index', compact('tariffCategories'));
    }

    /**
     * عرض نموذج إنشاء فئة تعريفة جديدة
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View
     */
    public function create(Request $request)
    {
        $services = Service::where('active', true)->orderBy('name')->pluck('name', 'id');
        $selectedServiceId = $request->input('service_id');

        return view('tariff-categories.create', compact('services', 'selectedServiceId'));
    }

    /**
     * تخزين فئة تعريفة جديدة في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'code' => 'required|string|max:50|unique:tariff_categories',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_of_measurement' => 'required|string|max:100',
            'active' => 'nullable|accepted',
        ]);

        $tariffCategory = TariffCategory::create([
            'service_id' => $request->service_id,
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'unit_of_measurement' => $request->unit_of_measurement,
            'active' => $request->has('active'),
        ]);

        if ($request->has('redirect_to_service') && $request->redirect_to_service) {
            return redirect()->route('services.show', $tariffCategory->service_id)
                ->with('success', 'تم إنشاء فئة التعريفة بنجاح.');
        }

        return redirect()->route('tariff-categories.index')
            ->with('success', 'تم إنشاء فئة التعريفة بنجاح.');
    }

    /**
     * عرض تفاصيل فئة تعريفة محددة
     *
     * @param  \App\Models\TariffCategory  $tariffCategory
     * @return \Illuminate\View\View
     */
    public function show(TariffCategory $tariffCategory)
    {
        $pricingRules = $tariffCategory->pricingRules()->orderBy('priority', 'desc')->paginate(5);
        return view('tariff-categories.show', compact('tariffCategory', 'pricingRules'));
    }

    /**
     * عرض نموذج تعديل فئة تعريفة محددة
     *
     * @param  \App\Models\TariffCategory  $tariffCategory
     * @return \Illuminate\View\View
     */
    public function edit(TariffCategory $tariffCategory)
    {
        $services = Service::orderBy('name')->pluck('name', 'id');
        return view('tariff-categories.edit', compact('tariffCategory', 'services'));
    }

    /**
     * تحديث فئة تعريفة محددة في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\TariffCategory  $tariffCategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, TariffCategory $tariffCategory)
    {
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'code' => 'required|string|max:50|unique:tariff_categories,code,' . $tariffCategory->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_of_measurement' => 'required|string|max:100',
            'active' => 'nullable|accepted',
        ]);

        $tariffCategory->update([
            'service_id' => $request->service_id,
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'unit_of_measurement' => $request->unit_of_measurement,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('tariff-categories.index')
            ->with('success', 'تم تحديث فئة التعريفة بنجاح.');
    }

    /**
     * حذف فئة تعريفة محددة من قاعدة البيانات
     *
     * @param  \App\Models\TariffCategory  $tariffCategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(TariffCategory $tariffCategory)
    {
        // التحقق من عدم وجود قواعد تسعير مرتبطة بفئة التعريفة
        if ($tariffCategory->pricingRules()->count() > 0) {
            return redirect()->route('tariff-categories.index')
                ->with('error', 'لا يمكن حذف فئة التعريفة لأنها تحتوي على قواعد تسعير مرتبطة بها.');
        }

        // التحقق من عدم وجود رسوم ثابتة مرتبطة بفئة التعريفة
        if ($tariffCategory->fixedFees()->count() > 0) {
            return redirect()->route('tariff-categories.index')
                ->with('error', 'لا يمكن حذف فئة التعريفة لأنها مرتبطة برسوم ثابتة.');
        }

        $tariffCategory->delete();

        return redirect()->route('tariff-categories.index')
            ->with('success', 'تم حذف فئة التعريفة بنجاح.');
    }

    /**
     * تغيير حالة نشاط فئة التعريفة
     *
     * @param  \App\Models\TariffCategory  $tariffCategory
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(TariffCategory $tariffCategory)
    {
        $tariffCategory->update([
            'active' => !$tariffCategory->active
        ]);

        $status = $tariffCategory->active ? 'تفعيل' : 'تعطيل';
        return redirect()->route('tariff-categories.index')
            ->with('success', "تم {$status} فئة التعريفة بنجاح.");
    }
}
