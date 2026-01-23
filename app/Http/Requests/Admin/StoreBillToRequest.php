<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreBillToRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'location' => 'nullable|string|max:255',
            'name' => 'required|string|max:255',
            'address1' => 'required|string|max:255',
            'address2' => 'nullable|string|max:255',
            'city' => 'required|string|max:255',
            'vat_eori' => 'nullable|string|max:255',
            'vat_eori2' => 'nullable|string|max:255',
            'contact2' => 'nullable|string|max:50',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Name is required.',
            'address1.required' => 'Address is required.',
            'city.required' => 'City is required.',
        ];
    }
}

