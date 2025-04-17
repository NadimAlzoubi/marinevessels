<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'description' => 'nullable|string',
            'active' => 'boolean',
        ];

        // إضافة قاعدة التفرد للكود، مع استثناء الخدمة الحالية عند التحديث
        if ($this->isMethod('put') || $this->isMethod('patch')) {
            $rules['code'] .= '|unique:services,code,' . $this->service->id;
        } else {
            $rules['code'] .= '|unique:services';
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
            'code' => 'كود الخدمة',
            'name' => 'اسم الخدمة',
            'description' => 'وصف الخدمة',
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
            'code.required' => 'يجب إدخال كود الخدمة',
            'code.unique' => 'كود الخدمة مستخدم بالفعل',
            'name.required' => 'يجب إدخال اسم الخدمة',
        ];
    }
}
