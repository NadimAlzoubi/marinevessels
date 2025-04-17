<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TariffCategoryRequest extends FormRequest
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
            'service_id' => 'required|exists:services,id',
            'code' => 'required|string|max:50',
            'name' => 'required|string|max:255',
            'unit_of_measurement' => 'required|string|max:100',
            'description' => 'nullable|string',
            'active' => 'boolean',
        ];

        // إضافة قاعدة التفرد للكود، مع استثناء فئة التعريفة الحالية عند التحديث
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['code'] .= '|unique:tariff_categories,code,' . $this->tariff_category->id;
        } else {
            $rules['code'] .= '|unique:tariff_categories';
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
            'service_id' => 'الخدمة',
            'code' => 'كود فئة التعريفة',
            'name' => 'اسم فئة التعريفة',
            'unit_of_measurement' => 'وحدة القياس',
            'description' => 'الوصف',
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
            'service_id.required' => 'يجب اختيار الخدمة',
            'service_id.exists' => 'الخدمة المختارة غير موجودة',
            'code.required' => 'يجب إدخال كود فئة التعريفة',
            'code.unique' => 'كود فئة التعريفة مستخدم بالفعل',
            'name.required' => 'يجب إدخال اسم فئة التعريفة',
            'unit_of_measurement.required' => 'يجب إدخال وحدة القياس',
        ];
    }
}
