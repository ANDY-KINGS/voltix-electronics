<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PosCheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'customer_id' => ['nullable', 'exists:customers,id'],
            'payment_method' => ['required', 'in:cash,mpesa'],
            'phone_number' => ['required_if:payment_method,mpesa', 'nullable', 'string', 'regex:/^254[0-9]{9}$/'],
            'discount' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
