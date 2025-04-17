<?php

namespace App\Http\Controllers;

use App\Models\ConditionType;
use Illuminate\Http\Request;

class ConditionTypeController extends Controller
{
    /**
     * عرض قائمة أنواع الشروط
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $conditionTypes = ConditionType::orderBy('name')->paginate(10);
        return view('condition-types.index', compact('conditionTypes'));
    }

    /**
     * عرض نموذج إنشاء نوع شرط جديد
     *
     * @return \Illuminate\View\View
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
            'comparison' => ['=', '!=', '>', '>=', '<', '<=', 'in', 'not_in', 'between'],
            'boolean' => ['='],
            'text' => ['=', '!=', 'contains', 'starts_with', 'ends_with', 'in', 'not_in'],
            'date' => ['=', '!=', '>', '>=', '<', '<=', 'between']
        ];
        
        return view('condition-types.create', compact('dataTypes', 'operatorTypes', 'availableOperators'));
    }

    /**
     * تخزين نوع شرط جديد في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:condition_types',
            'name' => 'required|string|max:255',
            'data_type' => 'required|string|in:string,number,boolean,date,array',
            'operator_type' => 'required|string|in:comparison,boolean,text,date',
            'available_operators' => 'required|array',
'active' => 'nullable|accepted',
        ]);

        ConditionType::create([
            'code' => $request->code,
            'name' => $request->name,
            'data_type' => $request->data_type,
            'operator_type' => $request->operator_type,
            'available_operators' => json_encode($request->available_operators),
            'active' => $request->has('active'),
        ]);

        return redirect()->route('condition-types.index')
            ->with('success', 'تم إنشاء نوع الشرط بنجاح.');
    }

    /**
     * عرض تفاصيل نوع شرط محدد
     *
     * @param  \App\Models\ConditionType  $conditionType
     * @return \Illuminate\View\View
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
     * عرض نموذج تعديل نوع شرط محدد
     *
     * @param  \App\Models\ConditionType  $conditionType
     * @return \Illuminate\View\View
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
            'comparison' => ['=', '!=', '>', '>=', '<', '<=', 'in', 'not_in', 'between'],
            'boolean' => ['='],
            'text' => ['=', '!=', 'contains', 'starts_with', 'ends_with', 'in', 'not_in'],
            'date' => ['=', '!=', '>', '>=', '<', '<=', 'between']
        ];
        
        $selectedOperators = json_decode($conditionType->available_operators, true) ?? [];
        
        return view('condition-types.edit', compact('conditionType', 'dataTypes', 'operatorTypes', 'availableOperators', 'selectedOperators'));
    }

    /**
     * تحديث نوع شرط محدد في قاعدة البيانات
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\ConditionType  $conditionType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ConditionType $conditionType)
    {
        $request->validate([
            'code' => 'required|string|max:50|unique:condition_types,code,' . $conditionType->id,
            'name' => 'required|string|max:255',
            'data_type' => 'required|string|in:string,number,boolean,date,array',
            'operator_type' => 'required|string|in:comparison,boolean,text,date',
            'available_operators' => 'required|array',
'active' => 'nullable|accepted',
        ]);

        $conditionType->update([
            'code' => $request->code,
            'name' => $request->name,
            'data_type' => $request->data_type,
            'operator_type' => $request->operator_type,
            'available_operators' => json_encode($request->available_operators),
            'active' => $request->has('active'),
        ]);

        return redirect()->route('condition-types.index')
            ->with('success', 'تم تحديث نوع الشرط بنجاح.');
    }

    /**
     * حذف نوع شرط محدد من قاعدة البيانات
     *
     * @param  \App\Models\ConditionType  $conditionType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(ConditionType $conditionType)
    {
        // التحقق من عدم استخدام نوع الشرط في قواعد التسعير
        // هذا يتطلب إضافة علاقة بين نوع الشرط وقواعد التسعير
        // أو فحص استخدام الكود في شروط قواعد التسعير
        
        $conditionType->delete();

        return redirect()->route('condition-types.index')
            ->with('success', 'تم حذف نوع الشرط بنجاح.');
    }

    /**
     * تغيير حالة نشاط نوع الشرط
     *
     * @param  \App\Models\ConditionType  $conditionType
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toggleActive(ConditionType $conditionType)
    {
        $conditionType->update([
            'active' => !$conditionType->active
        ]);

        $status = $conditionType->active ? 'تفعيل' : 'تعطيل';
        return redirect()->route('condition-types.index')
            ->with('success', "تم {$status} نوع الشرط بنجاح.");
    }
}
