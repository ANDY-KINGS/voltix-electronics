<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $brandId = $this->route('brand') ? $this->route('brand')->id : null;

        return [
            'name'              => 'required|string|max:100|unique:brands,name,' . $brandId,
            'country_of_origin' => 'nullable|string|max:80',
            'logo'              => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ];
    }
}
