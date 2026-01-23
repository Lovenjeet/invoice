<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'shipper_id' => 'required|exists:shippers,id',
            'bill_to_id' => 'required|exists:bill_to,id',
            'ship_to_id' => 'required|exists:ship_to,id',
            'invoice_no' => 'required|string|max:255',
            'invoice_date' => 'required|date',
            'terms' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.hs_code_id' => 'required|exists:hs_codes,id',
            'items.*.qty' => 'required|numeric|min:0',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.amount' => 'required|numeric|min:0',
            'items.*.number_of_boxes' => 'nullable|numeric|min:0',
            'items.*.g_w' => 'nullable|numeric|min:0',
            'items.*.dimensions' => 'nullable|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'shipper_id.required' => 'Shipper is required.',
            'bill_to_id.required' => 'Bill To is required.',
            'ship_to_id.required' => 'Ship To is required.',
            'invoice_no.required' => 'Invoice number is required.',
            'invoice_date.required' => 'Invoice date is required.',
            'terms.required' => 'Terms is required.',
            'items.required' => 'At least one item is required.',
            'items.*.hs_code_id.required' => 'HSCode is required for all items.',
            'items.*.qty.required' => 'Quantity is required for all items.',
            'items.*.unit_price.required' => 'Unit price is required for all items.',
        ];
    }
}

