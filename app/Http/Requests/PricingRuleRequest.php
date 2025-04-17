<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PricingRuleRequest extends FormRequest
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
        return [
            'tariff_category_id' => 'required|exists:tariff_categories,id',
            'name' => 'required|string|max:255',
            'rate' => 'required|numeric|min:0',
            'conditions' => 'required|json',
            'priority' => 'required|integer|min:0',
            'effective_from' => 'nullable|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
            'active' => 'boolean',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'tariff_category_id' => 'فئة التعريفة',
            'name' => 'اسم القاعدة',
            'rate' => 'السعر الأساسي',
            'conditions' => 'شروط التطبيق',
            'priority' => 'الأولوية',
            'effective_from' => 'تاريخ بدء السريان',
            'effective_to' => 'تاريخ انتهاء السريان',
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
            'tariff_category_id.required' => 'يجب اختيار فئة التعريفة',
            'tariff_category_id.exists' => 'فئة التعريفة المختارة غير موجودة',
            'name.required' => 'يجب إدخال اسم القاعدة',
            'rate.required' => 'يجب إدخال السعر الأساسي',
            'rate.numeric' => 'يجب أن يكون السعر الأساسي رقمًا',
            'rate.min' => 'يجب أن يكون السعر الأساسي أكبر من أو يساوي صفر',
            'conditions.required' => 'يجب تحديد شروط التطبيق',
            'conditions.json' => 'يجب أن تكون شروط التطبيق بتنسيق JSON صحيح',
            'priority.required' => 'يجب إدخال الأولوية',
            'priority.integer' => 'يجب أن تكون الأولوية رقمًا صحيحًا',
            'priority.min' => 'يجب أن تكون الأولوية أكبر من أو تساوي صفر',
            'effective_from.date' => 'يجب أن يكون تاريخ بدء السريان تاريخًا صحيحًا',
            'effective_to.date' => 'يجب أن يكون تاريخ انتهاء السريان تاريخًا صحيحًا',
            'effective_to.after_or_equal' => 'يجب أن يكون تاريخ انتهاء السريان بعد أو يساوي تاريخ بدء السريان',
        ];
    }
}
