<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ConditionTypeRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'data_type' => 'required|string|in:string,number,boolean,date,array',
            'operator_type' => 'required|string|in:comparison,boolean,text,date',
            'available_operators' => 'required|array',
            'available_operators.*' => 'string',
            'active' => 'boolean',
        ];

        // إضافة قاعدة التفرد للكود، مع استثناء نوع الشرط الحالي عند التحديث
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['code'] .= '|unique:condition_types,code,' . $this->condition_type->id;
        } else {
            $rules['code'] .= '|unique:condition_types';
        }

        return $rules;
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'code' => 'كود نوع الشرط',
            'name' => 'اسم نوع الشرط',
            'data_type' => 'نوع البيانات',
            'operator_type' => 'نوع العمليات',
            'available_operators' => 'العمليات المتاحة',
            'available_operators.*' => 'العملية',
            'active' => 'حالة التفعيل',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'code.required' => 'يجب إدخال كود نوع الشرط',
            'code.unique' => 'كود نوع الشرط مستخدم بالفعل',
            'name.required' => 'يجب إدخال اسم نوع الشرط',
            'data_type.required' => 'يجب اختيار نوع البيانات',
            'data_type.in' => 'نوع البيانات المختار غير صالح',
            'operator_type.required' => 'يجب اختيار نوع العمليات',
            'operator_type.in' => 'نوع العمليات المختار غير صالح',
            'available_operators.required' => 'يجب اختيار العمليات المتاحة',
            'available_operators.array' => 'يجب أن تكون العمليات المتاحة مصفوفة',
        ];
    }
}
