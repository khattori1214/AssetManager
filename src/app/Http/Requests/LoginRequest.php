<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'employee_no' => [
                'required',
                'string',
                'max:32',
                'regex:/\A[A-Za-z0-9]+\z/',
            ],
            'password' => [
                'required',
                'string',
                'min:8',
                'max:128',
                'regex:/\A(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9])[!-~]+\z/',
            ],
        ];
    }
    public function messages(): array
    {
        return [
            'employee_no.required' => '社員番号IDを入力してください',
            'employee_no.max' => '社員番号IDは半角英数字32文字以内で入力してください。',
            'employee_no.regex' => '社員番号IDは半角英数字32文字以内で入力してください。',
            'password.required' => 'パスワードを入力してください',
            'password.min' => 'パスワードは8文字以上128文字以内で、半角英字・半角数字・記号をそれぞれ1文字以上含めてください。',
            'password.max' => 'パスワードは8文字以上128文字以内で、半角英字・半角数字・記号をそれぞれ1文字以上含めてください。',
            'password.regex' => 'パスワードは8文字以上128文字以内で、半角英字・半角数字・記号をそれぞれ1文字以上含めてください。',
        ];
    }

}
