<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id'     => ['required', 'exists:categories,id'],
            'supplier_id'     => ['nullable', 'exists:suppliers,id'],
            'brand_id'        => ['nullable', 'exists:brands,id'],
            'name'            => ['required', 'string', 'max:255'],
            'sku'             => ['required', 'string', 'unique:products,sku'],
            'model_number'    => ['nullable', 'string', 'max:100', 'unique:products,model_number'],
            'price'           => ['required', 'numeric', 'gt:0'],
            'cost_price'      => ['required', 'numeric', 'min:0'],
            'warranty_months' => ['nullable', 'integer', 'min:0'],
            'serial_tracking' => ['nullable', 'boolean'],
            'stock_quantity'  => ['required', 'integer', 'min:0'],
            'reorder_level'   => ['required', 'integer', 'gt:0'],
            'image'           => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];
    }
}
