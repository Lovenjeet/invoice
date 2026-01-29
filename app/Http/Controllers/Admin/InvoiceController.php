<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreInvoiceRequest;
use App\Http\Requests\Admin\UpdateInvoiceRequest;
use App\Models\BillTo;
use App\Models\HSCode;
use App\Models\Invoice;
use App\Models\ShipTo;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        // Only admins can access invoice list
        if (!Auth::user()->isAdmin()) {
            abort(403, 'Unauthorized access.');
        }
        
        if ($request->ajax()) {
            return $this->getInvoicesData($request);
        }
        
        $suppliers = Supplier::orderBy('name')->get();
        $billTos = BillTo::orderBy('name')->get();
        $shipTos = ShipTo::orderBy('name')->get();
        
        return view('admin.invoices.index', compact('suppliers', 'billTos', 'shipTos'));
    }

    private function getInvoicesData(Request $request)
    {
        $draw = $request->get('draw', 1);
        $start = $request->get('start', 0);
        $length = $request->get('length', 25);
        $search = $request->get('search')['value'] ?? '';
        
        $columns = ['unc_number', 'approval_email', 'supplier_id', 'bill_to_id', 'ship_to_id', 'total', 'status'];
        $orderColumn = $request->get('order')[0]['column'] ?? 0;
        $orderDir = $request->get('order')[0]['dir'] ?? 'desc';
        $orderBy = isset($columns[$orderColumn]) ? $columns[$orderColumn] : 'id';
        
        $query = Invoice::with(['supplier', 'billTo', 'shipTo']);
        
        // Search functionality
        if (!empty($search)) {
            $query->where(function($q) use ($search) {
                $q->where('invoice_no', 'like', "%{$search}%")
                  ->orWhere('unc_number', 'like', "%{$search}%")
                  ->orWhere('approval_email', 'like', "%{$search}%")
                  ->orWhereHas('supplier', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('billTo', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('shipTo', function($q) use ($search) {
                      $q->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        // Status filter
        if ($request->has('status_filter') && !empty($request->status_filter)) {
            $query->where('status', $request->status_filter);
        }
        
        // Supplier filter
        if ($request->has('supplier_filter') && !empty($request->supplier_filter)) {
            $query->where('supplier_id', $request->supplier_filter);
        }
        
        // Bill To filter
        if ($request->has('bill_to_filter') && !empty($request->bill_to_filter)) {
            $query->where('bill_to_id', $request->bill_to_filter);
        }
        
        // Ship To filter
        if ($request->has('ship_to_filter') && !empty($request->ship_to_filter)) {
            $query->where('ship_to_id', $request->ship_to_filter);
        }
        
        // Get total records before filtering
        $totalRecords = Invoice::count();
        
        // Get filtered count
        $filteredRecords = $query->count();
        
        // Order and paginate
        $invoices = $query->orderBy($orderBy, $orderDir)
                        ->skip($start)
                        ->take($length)
                        ->get();
        
        $data = [];
        foreach ($invoices as $invoice) {
            $statusBadge = $invoice->status === 'approved' 
                ? '<span class="badge bg-success bg-opacity-10 text-success rounded-pill">Approved</span>'
                : '<span class="badge bg-danger bg-opacity-10 text-danger rounded-pill">Draft</span>';
            
            $supplierName = e($invoice->supplier->name ?? 'N/A');
            $billToName = e($invoice->billTo->name ?? 'N/A');
            $shipToName = e($invoice->shipTo->name ?? 'N/A');
            
            $data[] = [
                'unc_number' => '<span>' . e($invoice->unc_number ?? 'N/A') . '</span>',
                'approval_email' => '<span>' . e($invoice->approval_email ?? 'N/A') . '</span>',
                'supplier' => '<span class="text-truncate-cell" title="' . $supplierName . '">' . $supplierName . '</span>',
                'bill_to' => '<span class="text-truncate-cell" title="' . $billToName . '">' . $billToName . '</span>',
                'ship_to' => '<span class="text-truncate-cell" title="' . $shipToName . '">' . $shipToName . '</span>',
                'total' => '<span class="fw-medium">$ ' . number_format($invoice->total, 2) . '</span>',
                'status' => $statusBadge,
                'actions' => view('admin.invoices.partials.action-buttons', compact('invoice'))->render(),
            ];
        }
        
        return response()->json([
            'draw' => intval($draw),
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $billTos = BillTo::orderBy('name')->get();
        $shipTos = ShipTo::orderBy('name')->get();
        $hsCodes = HSCode::orderBy('id')->get();
        
        return view('admin.invoices.create', compact('suppliers', 'billTos', 'shipTos', 'hsCodes'));
    }

    public function store(StoreInvoiceRequest $request)
    {
        try {
            DB::beginTransaction();

            $subtotal = 0;
            $totalGw = 0;

            // Calculate totals from items
            foreach ($request->items as $item) {
                $subtotal += $item['amount'];
                $totalGw += $item['g_w'] ?? 0;
            }

            $shippingValue = $request->shipping_value ?? 0;
            $total = $subtotal + $shippingValue;

            // Create invoice
            $invoice = Invoice::create([
                'supplier_id' => $request->supplier_id,
                'bill_to_id' => $request->bill_to_id,
                'ship_to_id' => $request->ship_to_id,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'terms' => $request->terms,
                'remarks' => $request->remarks,
                'status' => 'draft',
                'subtotal' => $subtotal,
                'shipping_value' => $shippingValue,
                'total' => $total,
                'total_gw' => $totalGw,
            ]);

            // Create invoice items
            foreach ($request->items as $item) {
                $invoice->items()->create([
                    'hs_code_id' => $item['hs_code_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'] ?? 0,
                    'amount' => $item['amount'],
                    'g_w' => $item['g_w'] ?? 0,
                ]);
            }

            DB::commit();

            return redirect()->route('invoices.show', $invoice->id);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Failed to create invoice: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Invoice $invoice)
    {
        $invoice->load(['items.hsCode']);
        $suppliers = Supplier::orderBy('name')->get();
        $billTos = BillTo::orderBy('name')->get();
        $shipTos = ShipTo::orderBy('name')->get();
        $hsCodes = HSCode::orderBy('id')->get();
        
        return view('admin.invoices.edit', compact('invoice', 'suppliers', 'billTos', 'shipTos', 'hsCodes'));
    }

    public function update(UpdateInvoiceRequest $request, Invoice $invoice)
    {
        try {
            DB::beginTransaction();

            $subtotal = 0;
            $totalGw = 0;

            // Calculate totals from items
            foreach ($request->items as $item) {
                $subtotal += $item['amount'];
                $totalGw += $item['g_w'] ?? 0;
            }

            $shippingValue = $request->shipping_value ?? 0;
            $total = $subtotal + $shippingValue;

            // Update invoice
            $invoice->update([
                'supplier_id' => $request->supplier_id,
                'bill_to_id' => $request->bill_to_id,
                'ship_to_id' => $request->ship_to_id,
                'invoice_no' => $request->invoice_no,
                'invoice_date' => $request->invoice_date,
                'terms' => $request->terms,
                'remarks' => $request->remarks,
                'subtotal' => $subtotal,
                'shipping_value' => $shippingValue,
                'total' => $total,
                'total_gw' => $totalGw,
            ]);

            // Delete existing items
            $invoice->items()->delete();

            // Create new invoice items
            foreach ($request->items as $item) {
                $invoice->items()->create([
                    'hs_code_id' => $item['hs_code_id'],
                    'qty' => $item['qty'],
                    'unit_price' => $item['unit_price'] ?? 0,
                    'amount' => $item['amount'],
                    'g_w' => $item['g_w'] ?? 0,
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
        $invoice->load(['supplier', 'billTo', 'shipTo', 'items.hsCode']);
        
        return view('admin.invoices.show', compact('invoice'));
    }

    public function approve(Request $request, Invoice $invoice)
    {
   
        $request->validate([
            'unc_number' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        try {
            DB::beginTransaction();

            // Update invoice with approval details
            $invoice->update([
                'unc_number' => $request->unc_number,
                'approval_email' => $request->email,
                'status' => 'approved',
            ]);

            // Generate PDF
            $invoice->load(['supplier', 'billTo', 'shipTo', 'items.hsCode']);
            $pdf = Pdf::loadView('admin.invoices.pdf', compact('invoice'));
            // return view('admin.invoices.pdf', compact('invoice'));
            $pdf->setPaper('a4', 'portrait');
            $pdfContent = $pdf->output();

            // Save PDF to storage
            $pdfPath = 'invoices/' . $invoice->id . '_' . time() . '.pdf';
            Storage::disk('public')->put($pdfPath, $pdfContent);
            
            // Send email with PDF attachment via Postmark
            $this->sendInvoiceApprovalEmail($request->email, $invoice->invoice_no, $pdfContent);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Invoice approved and sent successfully!',
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to approve invoice: ' . $e->getMessage(),
            ], 500);
        }
    }
    
    /**
     * Send invoice approval email via Postmark
     */
    protected function sendInvoiceApprovalEmail(string $toEmail, string $invoiceNo, string $pdfContent): void
    {
        $htmlBody = "
            <!DOCTYPE html>
            <html>
            <head>
                <meta charset='utf-8'>
                <meta name='viewport' content='width=device-width, initial-scale=1.0'>
            </head>
            <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;'>
                <div style='background-color: #f8f9fa; padding: 30px; border-radius: 8px;'>
                    <h1 style='color: #333; margin-top: 0;'>Hello!</h1>
                    <p style='font-size: 16px; margin-bottom: 20px;'>Your invoice has been approved and is attached to this email.</p>
                    <div style='background-color: #fff; border: 1px solid #ddd; border-radius: 6px; padding: 15px; margin: 20px 0;'>
                        <p style='font-size: 14px; margin: 0;'><strong>Invoice Number:</strong> {$invoiceNo}</p>
                    </div>
                    <p style='font-size: 14px; color: #666;'>Please find the invoice PDF attached.</p>
                    <hr style='border: none; border-top: 1px solid #ddd; margin: 30px 0;'>
                    <p style='font-size: 14px; color: #666; margin-bottom: 0;'>Thank you!</p>
                </div>
            </body>
            </html>
        ";
        
        // Encode PDF content as base64 for Postmark attachment
        $pdfBase64 = base64_encode($pdfContent);
        
        Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'X-Postmark-Server-Token' => env('POSTMARK_API_KEY'),
        ])->post('https://api.postmarkapp.com/email', [
            'From' => env('POSTMARK_FROM_ADDRESS'),
            'To' => $toEmail,
            'Subject' => 'Invoice Approved - ' . $invoiceNo,
            'HtmlBody' => $htmlBody,
            'MessageStream' => 'outbound',
            'Attachments' => [
                [
                    'Name' => 'Invoice_' . $invoiceNo . '.pdf',
                    'Content' => $pdfBase64,
                    'ContentType' => 'application/pdf',
                ],
            ],
        ]);
    }
}

