<?php

namespace App\Http\Controllers;

use App\Models\FixedFee;
use App\Models\FeeCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FixedFeeController extends Controller
{
    use AuthorizesRequests;

    /**
     * عرض جميع الرسوم الثابتة.
     */
    public function index(Request $request)
    {
        
        if ($request->ajax()) {
            $fixedFees = FixedFee::with('feeCategory')->get();
            $canDelete = Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'editor');

            return DataTables::of($fixedFees)
                ->addColumn('action', function ($fixedFee) use ($canDelete) {
                    $action = '
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton' . $fixedFee->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $fixedFee->id . '">
                        <li>
                            <a class="dropdown-item" href="' . route('fixed_fees.show', $fixedFee->id) . '">
                                <i class="bx bx-show-alt"></i> View
                            </a>
                        </li>';

                    if ($canDelete) {
                        $action .= '
                        <li>
                            <a class="dropdown-item" href="' . route('fixed_fees.edit', $fixedFee->id) . '" data-id="' . $fixedFee->id . '">
                                <i class="bx bx-edit"></i> Edit
                            </a>
                        </li>
                        <li>
                            <form class="d-inline" action="' . route('fixed_fees.destroy', $fixedFee->id) . '" method="POST">
                                ' . csrf_field() . '
                                ' . method_field('DELETE') . '
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bx bx-trash"></i> Delete
                                </button>
                            </form>
                        </li>';
                    }

                    $action .= '</ul></div>';

                    return $action;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('fixed_fees.index');
    }

    /**
     * عرض نموذج إنشاء رسم ثابت جديد.
     */
    public function create()
    {
        // $this->authorize('create', FixedFee::class); 
        // جلب فئات الرسوم لإظهارها في القائمة المنسدلة
        $categories = FeeCategory::all();
        return view('fixed_fees.create', compact('categories'));
    }

    /**
     * حفظ الرسم الثابت الجديد.
     */
    public function store(Request $request)
    {
        // $this->authorize('create', FixedFee::class); 
        $request->validate([
            'fee_name'        => 'required|string|max:255',
            'description'     => 'nullable|string',
            'amount'          => 'required|numeric',
            'pricing_rule'    => 'required|string',
            'fee_category_id' => 'required|exists:fee_categories,id',
        ]);

        FixedFee::create($request->only(['fee_name', 'description', 'amount', 'pricing_rule', 'fee_category_id']));

        return redirect()->route('fixed_fees.index')->with('success', 'Created successfuly.');
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
        // $this->authorize('update', FixedFee::class); 
        $fixedFee = FixedFee::findOrFail($id);
        $categories = FeeCategory::all();
        return view('fixed_fees.edit', compact('fixedFee', 'categories'));
    }

    /**
     * تحديث بيانات الرسم الثابت.
     */
    public function update(Request $request, $id)
    {
        // $this->authorize('update', FixedFee::class); 
        $request->validate([
            'fee_name'        => 'required|string|max:255',
            'description'     => 'nullable|string',
            'amount'          => 'required|numeric',
            'pricing_rule'    => 'required|string',
            'fee_category_id' => 'required|exists:fee_categories,id',
        ]);

        $fixedFee = FixedFee::findOrFail($id);
        $fixedFee->update($request->only(['fee_name', 'description', 'amount', 'pricing_rule', 'fee_category_id']));

        return redirect()->route('fixed_fees.index')->with('success', 'Updated successfuly.');
    }

    /**
     * حذف الرسم الثابت.
     */
    public function destroy($id)
    {
        // $this->authorize('delete', FixedFee::class); 
        $fixedFee = FixedFee::findOrFail($id);
        $fixedFee->delete();

        return redirect()->route('fixed_fees.index')->with('success', 'Deleted successfuly.');
    }
}
