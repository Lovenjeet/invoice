<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Commercial Invoice - {{ $invoice->invoice_no }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        .d-flex {
            display: flex;
        }
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            padding: 20px;
            color: #000;
            position: relative;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        .watermark {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 180px;
            color: rgba(0, 0, 0, 0.1);
            z-index: 0;
            font-weight: bold;
            pointer-events: none;
            white-space: nowrap;
            width: 100%;
            text-align: center;
        }

        /* Header Section */
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .header-left {
            flex: 0 0 auto;
        }

        .header-right {
            flex: 0 0 auto;
            text-align: right;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .logo-section img {
            height: 70px;
            width: auto;
            object-fit: contain;
        }

        .logo {
            width: 70px;
            height: 70px;
            background: #0066cc;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 32px;
            flex-shrink: 0;
        }

        .logo-text {
            font-size: 20px;
            font-weight: bold;
            color: #000;
        }

        .invoice-title {
            font-size: 18px;
            font-weight: bold;
            color: #0066cc;
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .header-divider {
            height: 3px;
            background: #0066cc;
            margin-bottom: 25px;
        }

        .invoice-details {
            text-align: right;
        }

        .invoice-details h3 {
            color: #0066cc;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 3px;
            margin-top: 8px;
        }

        .invoice-details h3:first-child {
            margin-top: 0;
        }

        .invoice-details p {
            font-size: 12px;
            margin: 0;
            color: #000;
        }

        /* Information Sections */
        .info-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 25px;
        }

        .info-box {
            background: #f8f9fa;
            padding: 12px 15px;
            border-left: 4px solid #0066cc;
        }

        .info-box.full-width {
            grid-column: 1 / -1;
        }

        .info-box h4 {
            color: #0066cc;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .info-box p {
            font-size: 12px;
            margin: 2px 0;
            line-height: 1.4;
            color: #000;
        }

        .info-box strong {
            color: #000;
            font-weight: normal;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .items-table thead {
            background: #0066cc;
            color: white;
        }

        .items-table th {
            padding: 10px 8px;
            text-align: left;
            font-size: 11px;
            font-weight: bold;
            border: 1px solid #0052a3;
        }

        .items-table th:first-child {
            width: 25%;
            max-width: 25%;
        }

        .items-table td:first-child {
            width: 25%;
            max-width: 25%;
            word-wrap: break-word;
        }

        .items-table th:last-child {
            width: 12%;
            min-width: 100px;
        }

        .items-table td:last-child {
            width: 12%;
            min-width: 100px;
            white-space: nowrap;
        }

        .items-table th.text-center {
            text-align: center;
        }

        .items-table th.text-right {
            text-align: right;
        }

        .items-table td {
            padding: 8px;
            font-size: 11px;
            border: 1px solid #ddd;
            color: #000;
        }

        .items-table tbody tr {
            background: #fff;
        }

        .items-table tbody tr:nth-child(even) {
            background: #f8f9fa;
        }

        .items-table .text-right {
            text-align: right;
        }

        .items-table .text-center {
            text-align: center;
        }

        /* Summary Section */
        .summary-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }

        .summary-box {
            padding: 0;
        }

        .summary-box h4 {
            color: #000;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .summary-box p {
            font-size: 12px;
            margin: 4px 0;
            color: #000;
        }

        .summary-box strong {
            font-weight: bold;
        }

        .financial-summary {
            text-align: right;
            border-top: 1px solid #0066cc;
            padding-top: 10px;
        }

        .financial-summary h4 {
            color: #0066cc;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 8px;
        }

        .financial-summary .total-row {
            font-size: 14px;
            font-weight: bold;
            color: #0066cc;
            margin-top: 8px;
            padding-top: 8px;
        }

        .back-button {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: #0066cc;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }

        .back-button:hover {
            background: #0052a3;
            color: white;
        }

        .print-button,.btn-submit {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: #28a745;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }

        .print-button:hover, .btn-submit:hover {
            background: #218838;
            color: white;
        }

        .approve-button {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: #06CEA8;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: bold;
            transition: background-color 0.3s ease;
            border: none;
            cursor: pointer;
            margin-left: 10px;
        }

        .approve-button:hover {
            background: #05b896;
            color: white;
        }

        .button-group {
            margin-bottom: 20px;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 10000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 15% auto;
            padding: 30px;
            border: 1px solid #888;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h3 {
            color: #0066cc;
            font-size: 20px;
            font-weight: bold;
            margin: 0;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: #000;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-group input:focus {
            outline: none;
            border-color: #06CEA8;
        }

        .modal-actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
            margin-top: 20px;
        }

        .btn-cancel {
            padding: 10px 20px;
            background: #6c757d;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }

        .btn-cancel:hover {
            background: #5a6268;
        }


        @media print {
            .button-group {
                display: none;
            }
        }

        @media print {
            body {
                padding: 0;
            }
            .invoice-container {
                max-width: 100%;
                padding: 15px;
            }
            .watermark {
                display: block;
            }
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        @if($invoice->status !== 'approved')
        <div class="watermark">DRAFT</div>
        @endif
        <!-- Action Buttons -->
        <div class="button-group">
            <a href="{{ route('invoices.edit', $invoice->id) }}" class="back-button">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Edit Invoice
            </a>
            @if($invoice->status !== 'approved')
            <button onclick="openApproveModal()" class="print-button">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                    <path d="M13.5 4L6 11.5L2.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Approve and Send
            </button>
            @endif
        </div>
        
        <!-- Header -->
        <div class="header-top">
            <div class="header-left">
                <div class="logo-section">
                    @if($invoice->billTo->logo && Storage::disk('public')->exists($invoice->billTo->logo))
                        <img src="{{ asset('storage/' . $invoice->billTo->logo) }}" alt="Logo">
                    @else
                        <img src="{{ asset('particula.png') }}" alt="Logo">
                    @endif
                </div>
                <div class="invoice-title">Commercial invoice</div>
            </div>
            <div class="header-right invoice-details">
                <div class="d-flex">
                    <h3>INVOICE NO.: &nbsp; </h3>
                    <p style="margin-top: 1px;"><strong>{{ $invoice->invoice_no }}</strong></p>
                </div>
                <div class="d-flex">
                    <h3>DATE.: &nbsp; </h3>
                    <p style="margin-top: 1px;"><strong>{{ $invoice->invoice_date->format('d/m/Y') }}</strong></p>
                </div>  
                <div class="d-flex">
                    <h3>TERMS.: &nbsp; </h3>
                    <p style="margin-top: 1px;"><strong>{{ $invoice->terms }}</strong></p>
                </div>  
                <br><br>
                <div class="d-flex">
                    <h4>MADE IN CHINA</h4>
                </div>
            </div>
        </div>

        <div class="header-divider"></div>

        <!-- Information Sections -->
        <div class="info-section">
            <!-- Supplier (Left Top) -->
            <div class="info-box">
                <h4>SUPPLIER</h4>
                <p> <u>{{ $invoice->supplier->name }}</u> </p>
                <p> <u>{{ $invoice->supplier->address1 }}</u> </p>
                @if($invoice->supplier->address2)
                <p>{{ $invoice->supplier->address2 }}</p>
                @endif
                <p>{{ $invoice->supplier->city }}</p>
                @if($invoice->supplier->contact1)
                <p>Contact: {{ $invoice->supplier->contact1 }}</p>
                @endif
                @if($invoice->supplier->contact2)
                <p> {{ $invoice->supplier->contact2 }}</p>
                @endif
            </div>

            <!-- Bill To (Right Top) -->
            <div class="info-box">
                <h4>BILL TO</h4>
                <p> <b>{{ $invoice->billTo->name }}</b> </p>
                <p>{{ $invoice->billTo->address1 }}</p>
                @if($invoice->billTo->address2)
                <p>{{ $invoice->billTo->address2 }}</p>
                @endif
                <p>{{ $invoice->billTo->city }}</p>
                @if($invoice->billTo->vat_eori)
                <p>{{ $invoice->billTo->vat_eori }}</p>
                @endif
                @if($invoice->billTo->vat_eori2)
                <p>{{ $invoice->billTo->vat_eori2 }}</p>
                @endif
                @if($invoice->billTo->contact2)
                <p>Contact email: {{ $invoice->billTo->contact2 }}</p>
                @endif
            </div>

            <!-- Ship To (Full Width) -->
            <div class="info-box full-width">
                <h4>SHIP TO</h4>
                <p>{{ $invoice->shipTo->name }}</p>
                <p>{{ $invoice->shipTo->address }}</p>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th>DESCRIPTION AND HS CODE</th>
                    <th class="text-center">QTY</th>
                    <th class="text-center">UNIT PRICE ($)</th>
                    <th class="text-center">AMOUNT ($)</th>
                    <th class="text-center">G.W. (KG)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($invoice->items as $item)
                <tr>
                    <td>
                        @if($item->hsCode->sku)
                            {{ $item->hsCode->sku }} <br> 
                        @endif
                        @if($item->hsCode->desc1)
                            {{ $item->hsCode->desc1 }} <br> 
                        @endif
                        @if($item->hsCode->desc2)
                            {{ $item->hsCode->desc2 }} <br> 
                        @endif
                        @if($item->hsCode->hs_code)
                         <b>HS Code:</b>   {{ $item->hsCode->hs_code }} <br> 
                        @endif
                    </td>
                    <td class="text-center">{{ number_format($item->qty, 2) }}</td>
                    <td class="text-center">{{ number_format($item->unit_price, 2) }}</td>
                    <td class="text-center">{{ number_format($item->amount, 2) }}</td>
                    <td class="text-center">{{ number_format($item->g_w, 2) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-section">
            <div class="summary-box">
                @if($invoice->remarks)
                <div style="margin-top: 15px;">
                    <h4>Remarks</h4>
                    <p style="white-space: pre-wrap;">{{ $invoice->remarks }}</p>
                </div>
                @endif
            </div>
            <div class="summary-box financial-summary">
                <h4>Financial Summary</h4>
                <p class="total-row">TOTAL: $ {{ number_format($invoice->total, 2) }}</p>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div id="approveModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <span class="close" onclick="closeApproveModal()">&times;</span>
                <h3>Approve and Send Invoice</h3>
            </div>
            <form id="approveForm" onsubmit="submitApproval(event)">
                @csrf
                <div class="form-group">
                    <label for="unc_number">UNC Number <span class="text-danger">*</span></label>
                    <input type="text" id="unc_number" name="unc_number" required placeholder="Enter UNC number">
                </div>
                <div class="form-group">
                    <label for="email">Email Address <span class="text-danger">*</span></label>
                    <input type="email" id="email" name="email" required placeholder="Enter email address">
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn-cancel" onclick="closeApproveModal()">Cancel</button>
                    <button type="submit" class="btn-submit">
                        <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px; display: inline-block; vertical-align: middle;">
                            <path d="M13.5 4L6 11.5L2.5 8" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        Approve and Send
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openApproveModal() {
            document.getElementById('approveModal').style.display = 'block';
        }

        function closeApproveModal() {
            document.getElementById('approveModal').style.display = 'none';
            document.getElementById('approveForm').reset();
        }

        window.onclick = function(event) {
            const modal = document.getElementById('approveModal');
            if (event.target == modal) {
                closeApproveModal();
            }
        }

        function submitApproval(event) {
            event.preventDefault();
            
            const form = event.target;
            const formData = new FormData(form);
            const submitButton = form.querySelector('button[type="submit"]');
            const originalText = submitButton.innerHTML;
            
            // Disable button and show loading
            submitButton.disabled = true;
            submitButton.innerHTML = '<span>Processing...</span>';
            
            fetch('{{ route("invoices.approve", $invoice->id) }}', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Invoice approved and sent successfully!');
                    closeApproveModal();
                    location.reload();
                } else {
                    alert(data.message || 'An error occurred. Please try again.');
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                submitButton.disabled = false;
                submitButton.innerHTML = originalText;
            });
        }
    </script>
</body>
</html>
