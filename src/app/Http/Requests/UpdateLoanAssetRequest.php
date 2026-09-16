<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateLoanAssetRequest extends FormRequest
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
            'asset_name' => ['required', 'string', 'max:255'],
            'category_id' => ['integer'],
        ];
    }

    public function attributes(): array
    {
        return [
            'asset_name' => '資産名',
            'category_id' => 'カテゴリID',
        ];
    }

    
}
