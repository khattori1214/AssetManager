<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest
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
            'asset_type' => ['required', 'in:loan,consumable'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'min_stock' => ['nullable', 'integer', 'min:0'],
            'unit' => ['required', 'string', 'max:50'],
            'max_request_quantity' => ['nullable', 'integer', 'min:1'],
            'monthly_request_limit' => ['nullable', 'integer', 'min:1'],
        ];
    }

    public function attributes(): array
    {
        return [
            'asset_name' => '資産名',
            'category_id' => 'カテゴリID',
            'asset_type' => '資産種別',
            'stock' => '在庫数',
            'min_stock' => '最低キープ数',
            'unit' => '単位',
            'max_request_quantity' => '最大申請数',
            'monthly_request_limit' => '申請頻度',
        ];
    }
}
