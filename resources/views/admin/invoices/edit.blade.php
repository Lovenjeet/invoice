@extends('layouts.admin')

@section('title', 'Edit Invoice')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}" class="text-decoration-none">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('invoices.show', $invoice->id) }}" class="text-decoration-none">Invoice</a></li>
                    <li class="breadcrumb-item active">Edit Invoice</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1 fw-semibold">Edit Invoice</h1>
            <p class="text-muted mb-0">Update the invoice details below</p>
        </div>
    </div>
    
    <div class="card border-0 shadow-sm">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('invoices.update', $invoice->id) }}" id="invoiceForm">
                @csrf
                @method('PUT')
                
                <!-- Basic Information -->
                <div class="row g-4 mb-4">
                    <div class="col-md-6">
                        <x-form.select 
                            name="supplier_id" 
                            label="Supplier" 
                            :options="$suppliers->pluck('name', 'id')->toArray()" 
                            :value="old('supplier_id', $invoice->supplier_id)" 
                            placeholder="Select supplier" 
                            required 
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form.select 
                            name="bill_to_id" 
                            label="Bill To" 
                            :options="$billTos->pluck('name', 'id')->toArray()" 
                            :value="old('bill_to_id', $invoice->bill_to_id)" 
                            placeholder="Select bill to" 
                            required 
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form.select 
                            name="ship_to_id" 
                            label="Ship To" 
                            :options="$shipTos->pluck('name', 'id')->toArray()" 
                            :value="old('ship_to_id', $invoice->ship_to_id)" 
                            placeholder="Select ship to" 
                            required 
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form.input 
                            name="invoice_no" 
                            label="Invoice No." 
                            :value="old('invoice_no', $invoice->invoice_no)" 
                            placeholder="Enter invoice number" 
                            required 
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form.input 
                            name="invoice_date" 
                            label="Invoice Date" 
                            type="date" 
                            :value="old('invoice_date', $invoice->invoice_date->format('Y-m-d'))" 
                            required 
                        />
                    </div>
                    <div class="col-md-6">
                        <x-form.input 
                            name="terms" 
                            label="Terms" 
                            :value="old('terms', $invoice->terms)" 
                            placeholder="e.g., DDP" 
                            required 
                        />
                    </div>
                </div>

                <hr class="my-4">

                <!-- Invoice Items -->
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-semibold">Invoice Items</h5>
                        <button type="button" class="btn btn-primary btn-sm" id="addItemBtn">
                            <i class="bi bi-plus-lg me-2"></i>Add Item
                        </button>
                    </div>

                    <div id="itemsContainer">
                        <!-- Items will be added here dynamically -->
                    </div>
                </div>

                <hr class="my-4">

                <!-- Remarks -->
                <div class="mb-4">
                    <label for="remarks" class="form-label fw-semibold">Remarks</label>
                    <textarea 
                        name="remarks" 
                        id="remarks" 
                        class="form-control @error('remarks') is-invalid @enderror" 
                        rows="4" 
                        placeholder="Enter any additional remarks or notes"
                    >{{ old('remarks', $invoice->remarks) }}</textarea>
                    @error('remarks')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-4 border-top">
                    <a href="{{ route('invoices.show', $invoice->id) }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left me-2"></i>Cancel
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-2"></i>Update Invoice
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('scripts')
<script>
    // HS Codes data for dropdown
    const hsCodes = {!! json_encode($hsCodes->map(function($code) {
        return [
            'id' => $code->id,
            'hs_code' => $code->hs_code ?? '',
            'display' => $code->sku
        ];
    })->values()) !!};

    // Existing invoice items
    const existingItems = {!! json_encode($invoice->items->map(function($item) {
        return [
            'hs_code_id' => $item->hs_code_id,
            'qty' => $item->qty,
            'unit_price' => $item->unit_price,
            'amount' => $item->amount,
            'g_w' => $item->g_w ?? 0
        ];
    })->values()) !!};

    document.addEventListener('DOMContentLoaded', function() {
        let itemIndex = 0;
        const itemsContainer = document.getElementById('itemsContainer');
        const addItemBtn = document.getElementById('addItemBtn');

        // Add existing items first
        if (existingItems.length > 0) {
            existingItems.forEach(function(item) {
                addItem(item);
            });
        } else {
            // Add first item by default if no existing items
            addItem();
        }

        // Add item button click
        addItemBtn.addEventListener('click', function() {
            addItem();
        });

        // Remove item handler (delegated)
        itemsContainer.addEventListener('click', function(e) {
            if (e.target.closest('.remove-item-btn')) {
                e.target.closest('.item-row').remove();
                updateItemIndexes();
            }
        });

        // Calculate amount on qty or unit price change (delegated)
        itemsContainer.addEventListener('input', function(e) {
            if (e.target.classList.contains('item-qty') || e.target.classList.contains('item-unit-price')) {
                calculateAmount(e.target.closest('.item-row'));
            }
        });

        function addItem(itemData = null) {
            const itemRow = document.createElement('div');
            itemRow.className = 'item-row card border mb-3';
            itemRow.setAttribute('data-index', itemIndex);
            
            const hsCodeId = itemData ? itemData.hs_code_id : '';
            const qty = itemData ? itemData.qty : '';
            const unitPrice = itemData ? itemData.unit_price : '';
            const amount = itemData ? parseFloat(itemData.amount).toFixed(2) : '0.00';
            const gw = itemData ? itemData.g_w : '';
            
            itemRow.innerHTML = `
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="mb-0 fw-semibold">Item #${itemIndex + 1}</h6>
                        <button type="button" class="btn btn-sm btn-outline-danger remove-item-btn">
                            <i class="bi bi-trash me-1"></i>Remove
                        </button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">HSCode <span class="text-danger">*</span></label>
                            <select name="items[${itemIndex}][hs_code_id]" class="form-select item-hs-code" required>
                                <option value="">Select HSCode</option>
                                ${hsCodes.map(code => `
                                    <option value="${code.id}" ${code.id == hsCodeId ? 'selected' : ''}>${code.display}</option>
                                `).join('')}
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">QTY <span class="text-danger">*</span></label>
                            <input type="number" name="items[${itemIndex}][qty]" class="form-control item-qty" step="0.01" min="0" placeholder="0" value="${qty}" required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Unit Price ($)</label>
                            <input type="number" name="items[${itemIndex}][unit_price]" class="form-control item-unit-price" step="0.01" min="0" placeholder="0.00" value="${unitPrice}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Amount ($)</label>
                            <input type="text" name="items[${itemIndex}][amount]" class="form-control item-amount" readonly value="${amount}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">G.W. (KG)</label>
                            <input type="number" name="items[${itemIndex}][g_w]" class="form-control" step="0.01" min="0" placeholder="0.00" value="${gw}">
                        </div>
                    </div>
                </div>
            `;
            
            itemsContainer.appendChild(itemRow);
            itemIndex++;
        }

        function calculateAmount(itemRow) {
            const qty = parseFloat(itemRow.querySelector('.item-qty').value) || 0;
            const unitPrice = parseFloat(itemRow.querySelector('.item-unit-price').value) || 0;
            const amount = (qty * unitPrice).toFixed(2);
            itemRow.querySelector('.item-amount').value = amount;
        }

        function updateItemIndexes() {
            const rows = itemsContainer.querySelectorAll('.item-row');
            rows.forEach((row, index) => {
                row.setAttribute('data-index', index);
                row.querySelector('h6').textContent = `Item #${index + 1}`;
                
                // Update all input names
                const inputs = row.querySelectorAll('input, select');
                inputs.forEach(input => {
                    if (input.name) {
                        input.name = input.name.replace(/items\[\d+\]/, `items[${index}]`);
                    }
                });
            });
        }

        // Form submission - allow normal submission
    });
</script>
@endpush
@endsection

