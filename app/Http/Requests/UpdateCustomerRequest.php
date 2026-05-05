<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCustomerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'      => ['required', 'string', 'max:255'],
            'phone'     => ['nullable', 'string', 'max:20', 'unique:customers,phone,' . $this->route('customer')->id],
            'email'     => ['nullable', 'email', 'max:255'],
            'id_number' => ['nullable', 'string', 'max:20'],
        ];
    }
}
