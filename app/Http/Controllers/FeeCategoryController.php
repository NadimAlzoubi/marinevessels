<?php

namespace App\Http\Controllers;

use App\Models\FeeCategory;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class FeeCategoryController extends Controller
{
    use AuthorizesRequests;
    
    /**
     * عرض جميع فئات الرسوم.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $fee_categories = FeeCategory::query();
            $canDelete = Auth::check() && (Auth::user()->role === 'admin' || Auth::user()->role === 'editor');

            return DataTables::of($fee_categories)
                ->addColumn('action', function ($fee_category) use ($canDelete) {
                    $action = '
                <div class="dropdown">
                    <button class="btn btn-primary btn-sm dropdown-toggle" type="button" id="dropdownMenuButton' . $fee_category->id . '" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bx bx-dots-vertical-rounded"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton' . $fee_category->id . '">
                        <li>
                            <a class="dropdown-item" href="' . route('fee_categories.show', $fee_category->id) . '">
                                <i class="bx bx-show-alt"></i> View
                            </a>
                        </li>';

                    if ($canDelete) {
                        $action .= '
                        <li>
                            <a class="dropdown-item" href="' . route('fee_categories.edit', $fee_category->id) . '" data-id="' . $fee_category->id . '">
                                <i class="bx bx-edit"></i> Edit
                            </a>
                        </li>
                        <li>
                            <form class="d-inline" action="' . route('fee_categories.destroy', $fee_category->id) . '" method="POST">
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
                ->rawColumns(['action']) // السماح بعرض الـ HTML في عمود "action"
                ->make(true); // إرجاع الاستجابة بتنسيق JSON
        }

        return view('fee_categories.index');
    }

    /**
     * عرض نموذج إنشاء فئة رسوم جديدة.
     */
    public function create()
    {
        // $this->authorize('create', FeeCategory::class); 
        return view('fee_categories.create');
    }

    /**
     * حفظ فئة الرسوم الجديدة في قاعدة البيانات.
     */
    public function store(Request $request)
    {
        // $this->authorize('create', FeeCategory::class); 
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        FeeCategory::create($request->only(['category_name', 'description']));

        return redirect()->route('fee_categories.index')->with('success', 'Created successfully.');
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
        // $this->authorize('update', FeeCategory::class); 
        $category = FeeCategory::findOrFail($id);
        return view('fee_categories.edit', compact('category'));
    }

    /**
     * تحديث بيانات فئة الرسوم.
     */
    public function update(Request $request, $id)
    {
        // $this->authorize('update', FeeCategory::class); 
        $request->validate([
            'category_name' => 'required|string|max:255',
            'description'   => 'nullable|string',
        ]);

        $category = FeeCategory::findOrFail($id);
        $category->update($request->only(['category_name', 'description']));

        return redirect()->route('fee_categories.index')->with('success', 'Updated successfully.');
    }

    /**
     * حذف فئة الرسوم.
     */
    public function destroy($id)
    {
        // $this->authorize('delete', FeeCategory::class); 
        $category = FeeCategory::findOrFail($id);
        $category->delete();

        return redirect()->route('fee_categories.index')->with('success', 'Deleted successfully.');
    }
}
