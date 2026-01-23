<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceRequest;
use App\Http\Requests\Admin\UpdateInvoiceRequest;
use App\Models\BillTo;
use App\Models\HSCode;
use App\Models\Invoice;
use App\Models\ShipTo;
use App\Models\Shipper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function create()
    {
        $shippers = Shipper::orderBy('name')->get();
        $billTos = BillTo::orderBy('name')->get();
        $shipTos = ShipTo::orderBy('name')->get();
        $hsCodes = HSCode::orderBy('id')->get();
        
        return view('admin.invoices.create', compact('shippers', 'billTos', 'shipTos', 'hsCodes'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        try {
            DB::beginTransaction();

            $subtotal = 0;
            $totalBoxes = 0;
            $totalGw = 0;

            // Calculate totals from items
            foreach ($request->items as $item) {
                $subtotal += $item['amount'];
                $totalBoxes += $item['number_of_boxes'] ?? 0;
                $totalGw += $item['g_w'] ?? 0;
            }

            $shippingValue = $request->shipping_value ?? 0;
            $total = $subtotal + $shippingValue;

            // Create invoice
            $invoice = Invoice::create([
                'shipper_id' => $request->shipper_id,
                'bill_to_id' => $request->bill_to_id,
                'ship_to_id' => $request->ship_to_id,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'terms' => $request->terms,
                'subtotal' => $subtotal,
                'shipping_value' => $shippingValue,
                'total' => $total,
                'total_boxes' => $totalBoxes,
                'total_gw' => $totalGw,
            ]);

            // Create invoice items
            foreach ($request->items as $item) {
                $invoice->items()->create([
                    'hs_code_id' => $item['hs_code_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['amount'],
                    'number_of_boxes' => $item['number_of_boxes'] ?? 0,
                    'g_w' => $item['g_w'] ?? 0,
                    'dimensions' => $item['dimensions'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id);
        } catch (\Exception $e) {
            dd($e);
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['items.hsCode']);
        $shippers = Shipper::orderBy('name')->get();
        $billTos = BillTo::orderBy('name')->get();
        $shipTos = ShipTo::orderBy('name')->get();
        $hsCodes = HSCode::orderBy('id')->get();
        
        return view('admin.invoices.edit', compact('invoice', 'shippers', 'billTos', 'shipTos', 'hsCodes'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        try {
            DB::beginTransaction();

            $subtotal = 0;
            $totalBoxes = 0;
            $totalGw = 0;

            // Calculate totals from items
            foreach ($request->items as $item) {
                $subtotal += $item['amount'];
                $totalBoxes += $item['number_of_boxes'] ?? 0;
                $totalGw += $item['g_w'] ?? 0;
            }

            $shippingValue = $request->shipping_value ?? 0;
            $total = $subtotal + $shippingValue;

            // Update invoice
            $invoice->update([
                'shipper_id' => $request->shipper_id,
                'bill_to_id' => $request->bill_to_id,
                'ship_to_id' => $request->ship_to_id,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'terms' => $request->terms,
                'subtotal' => $subtotal,
                'shipping_value' => $shippingValue,
                'total' => $total,
                'total_boxes' => $totalBoxes,
                'total_gw' => $totalGw,
            ]);

            // Delete existing items
            $invoice->items()->delete();

            // Create new invoice items
            foreach ($request->items as $item) {
                $invoice->items()->create([
                    'hs_code_id' => $item['hs_code_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'],
                    'amount' => $item['amount'],
                    'number_of_boxes' => $item['number_of_boxes'] ?? 0,
                    'g_w' => $item['g_w'] ?? 0,
                    'dimensions' => $item['dimensions'] ?? null,
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to update invoice: ' . $e->getMessage()])->withInput();
        }
    }

    public function show(Invoice $invoice)
    {
        $invoice->load(['shipper', 'billTo', 'shipTo', 'items.hsCode']);
        
        return view('admin.invoices.show', compact('invoice'));
    }
}

