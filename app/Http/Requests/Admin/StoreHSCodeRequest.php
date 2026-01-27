<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreHSCodeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'model' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'hs_code' => 'required|string|max:255',
            'dimensions' => 'nullable|string|max:255',
            'number_of_units' => 'required|numeric',
            'weight' => 'required|numeric|between:0,999999.99',
            'temp_selected' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'desc1' => 'nullable|string|max:255',
            'desc2' => 'nullable|string|max:255',
            'desc3' => 'nullable|string|max:255',
            'dg' => 'nullable|in:Yes,No',
        ];
    }

    public function messages(): array
    {
        return [
            'model.required' => 'Model is required.',
            'sku.required' => 'SKU is required.',
            'hs_code.required' => 'HS Code is required.',
            'number_of_units.required' => 'Number of units is required.',
            'number_of_units.numeric' => 'Number of units must be a number.',
            'weight.required' => 'Weight is required.',
            'weight.numeric' => 'Weight must be a number.',
            'weight.between' => 'Weight must be between 0 and 999999.99.',
            'dg.in' => 'DG must be either Yes or No.',
        ];
    }
}

