<?php

namespace App\Http\Controllers;

use App\Models\FixedFee;
use App\Models\FeeCategory;
use Illuminate\Http\Request;

class FixedFeeController extends Controller
{
    /**
     * عرض جميع الرسوم الثابتة.
     */
    public function index()
    {
        $fixedFees = FixedFee::with('feeCategory')->get();
        return view('fixed_fees.index', compact('fixedFees'));
    }

    /**
     * عرض نموذج إنشاء رسم ثابت جديد.
     */
    public function create()
    {
        // جلب فئات الرسوم لإظهارها في القائمة المنسدلة
        $categories = FeeCategory::all();
        return view('fixed_fees.create', compact('categories'));
    }

    /**
     * حفظ الرسم الثابت الجديد.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fee_name'        => 'required|string|max:255',
            'description'     => 'nullable|string',
            'amount'          => 'required|numeric',
            'tax_rate'        => 'required|numeric',
            'fee_category_id' => 'required|exists:fee_categories,id',
        ]);

        FixedFee::create($request->only(['fee_name', 'description', 'amount', 'tax_rate', 'fee_category_id']));

        return redirect()->route('fixed_fees.index')->with('success', 'تم إنشاء الرسم الثابت بنجاح.');
    }

    /**
     * عرض تفاصيل رسم ثابت معين.
     */
    public function show($id)
    {
        $fixedFee = FixedFee::with('feeCategory')->findOrFail($id);
        return view('fixed_fees.show', compact('fixedFee'));
    }

    /**
     * عرض نموذج تحرير الرسم الثابت.
     */
    public function edit($id)
    {
        $fixedFee = FixedFee::findOrFail($id);
        $categories = FeeCategory::all();
        return view('fixed_fees.edit', compact('fixedFee', 'categories'));
    }

    /**
     * تحديث بيانات الرسم الثابت.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'fee_name'        => 'required|string|max:255',
            'description'     => 'nullable|string',
            'amount'          => 'required|numeric',
            'tax_rate'        => 'required|numeric',
            'fee_category_id' => 'required|exists:fee_categories,id',
        ]);

        $fixedFee = FixedFee::findOrFail($id);
        $fixedFee->update($request->only(['fee_name', 'description', 'amount', 'tax_rate', 'fee_category_id']));

        return redirect()->route('fixed_fees.index')->with('success', 'تم تحديث الرسم الثابت بنجاح.');
    }

    /**
     * حذف الرسم الثابت.
     */
    public function destroy($id)
    {
        $fixedFee = FixedFee::findOrFail($id);
        $fixedFee->delete();

        return redirect()->route('fixed_fees.index')->with('success', 'تم حذف الرسم الثابت بنجاح.');
    }
}
