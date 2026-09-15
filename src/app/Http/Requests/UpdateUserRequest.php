<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateUserRequest extends FormRequest
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
            'user_name' => ['required', 'string', 'max:32'],
            'email' => ['required', 'email', 'max:225', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'max:128', 'regex:/\A(?=.*[A-Za-z])(?=.*[0-9])(?=.*[^A-Za-z0-9])[!-~]+\z/'],
            'role_id' => ['required', 'integer', 'exists:roles,role_id'],
        ];
    }
}
