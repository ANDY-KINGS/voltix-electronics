<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WarrantyClaimRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'order_item_id'     => 'required|exists:order_items,id',
            'customer_id'       => 'required|exists:customers,id',
            'issue_description' => 'required|string|min:10',
        ];
    }

    public function messages(): array
    {
        return [
            'order_item_id.required'     => 'Please select an order item.',
            'customer_id.required'       => 'Please select a customer.',
            'issue_description.required' => 'Please describe the issue.',
            'issue_description.min'      => 'Issue description must be at least 10 characters.',
        ];
    }
}
