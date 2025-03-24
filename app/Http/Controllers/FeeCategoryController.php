<?php

namespace App\Http\Controllers;

use App\Models\FeeCategory;
use Illuminate\Http\Request;

class FeeCategoryController extends Controller
{
    /**
     * عرض جميع فئات الرسوم.
     */
    public function index()
    {
        $categories = FeeCategory::all();
        return view('fee_categories.index', compact('categories'));
    }

    /**
     * عرض نموذج إنشاء فئة رسوم جديدة.
     */
    public function create()
    {
        return view('fee_categories.create');
    }

    /**
     * حفظ فئة الرسوم الجديدة في قاعدة البيانات.
     */
    public function store(Request $request)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        FeeCategory::create($request->only(['category_name', 'description']));

        return redirect()->route('fee_categories.index')->with('success', 'تم إنشاء الفئة بنجاح.');
    }

    /**
     * عرض تفاصيل فئة رسوم معينة.
     */
    public function show($id)
    {
        $category = FeeCategory::findOrFail($id);
        return view('fee_categories.show', compact('category'));
    }

    /**
     * عرض نموذج تحرير فئة رسوم.
     */
    public function edit($id)
    {
        $category = FeeCategory::findOrFail($id);
        return view('fee_categories.edit', compact('category'));
    }

    /**
     * تحديث بيانات فئة الرسوم.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        $category = FeeCategory::findOrFail($id);
        $category->update($request->only(['category_name', 'description']));

        return redirect()->route('fee_categories.index')->with('success', 'تم تحديث الفئة بنجاح.');
    }

    /**
     * حذف فئة الرسوم.
     */
    public function destroy($id)
    {
        $category = FeeCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('fee_categories.index')->with('success', 'تم حذف الفئة بنجاح.');
    }
}
