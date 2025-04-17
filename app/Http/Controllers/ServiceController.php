<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    /**
     * عرض قائمة الخدمات
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $services = Service::orderBy('code')->paginate(10);
        return view('services.index', compact('services'));
    }

    /**
     * عرض نموذج إنشاء خدمة جديدة
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('services.create');
    }

    /**
     * تخزين خدمة جديدة في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:services',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'nullable|accepted',
        ]);

        Service::create([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('services.index')
            ->with('success', 'تم إنشاء الخدمة بنجاح.');
    }

    /**
     * عرض تفاصيل خدمة محددة
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\View\View
     */
    public function show(Service $service)
    {
        $tariffCategories = $service->tariffCategories()->paginate(5);
        return view('services.show', compact('service', 'tariffCategories'));
    }

    /**
     * عرض نموذج تعديل خدمة محددة
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\View\View
     */
    public function edit(Service $service)
    {
        return view('services.edit', compact('service'));
    }

    /**
     * تحديث خدمة محددة في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Service $service)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:services,code,' . $service->id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'active' => 'nullable|accepted',
        ]);

        $service->update([
            'code' => $request->code,
            'name' => $request->name,
            'description' => $request->description,
            'active' => $request->has('active'),
        ]);

        return redirect()->route('services.index')
            ->with('success', 'تم تحديث الخدمة بنجاح.');
    }

    /**
     * حذف خدمة محددة من قاعدة البيانات
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Service $service)
    {
        // التحقق من عدم وجود فئات تعريفة مرتبطة بالخدمة
        if ($service->tariffCategories()->count() > 0) {
            return redirect()->route('services.index')
                ->with('error', 'لا يمكن حذف الخدمة لأنها تحتوي على فئات تعريفة مرتبطة بها.');
        }

        $service->delete();

        return redirect()->route('services.index')
            ->with('success', 'تم حذف الخدمة بنجاح.');
    }

    /**
     * تغيير حالة نشاط الخدمة
     *
     * @param  \App\Models\Service  $service
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(Service $service)
    {
        $service->update([
            'active' => !$service->active
        ]);

        $status = $service->active ? 'تفعيل' : 'تعطيل';
        return redirect()->route('services.index')
            ->with('success', "تم {$status} الخدمة بنجاح.");
    }
}
