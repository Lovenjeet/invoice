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
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%) rotate(-45deg);
            font-size: 150px;
            color: rgba(0, 0, 0, 0.03);
            z-index: 0;
            font-weight: bold;
            pointer-events: none;
            white-space: nowrap;
        }

        /* Header Section */
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 12px;
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

        .print-button {
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

        .print-button:hover {
            background: #218838;
            color: white;
        }

        .button-group {
            margin-bottom: 20px;
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
    <div class="watermark">Page</div>
    
    <div class="invoice-container">
        <!-- Action Buttons -->
        <div class="button-group">
            <a href="{{ route('invoices.edit', $invoice->id) }}" class="back-button">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                    <path d="M10 12L6 8L10 4" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Edit Invoice
            </a>
            <button onclick="window.print()" class="print-button">
                <svg width="16" height="16" viewBox="0 0 16 16" fill="none" xmlns="http://www.w3.org/2000/svg" style="margin-right: 8px;">
                    <path d="M4 2H12V5H4V2Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                    <path d="M4 5H2C1.44772 5 1 5.44772 1 6V12C1 12.5523 1.44772 13 2 13H4M4 5V13M4 13H12M12 13H14C14.5523 13 15 12.5523 15 12V6C15 5.44772 14.5523 5 14 5H12M12 5V2M12 2H4M6 9H10" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                Print Invoice
            </button>
        </div>
        
        <!-- Header -->
        <div class="header-top">
            <div style="flex: 1;">
                <div class="logo-section">
                    <img src="{{ asset('particula.png') }}" alt="Logo" style="height: 70px;">
                </div>
                <div class="invoice-title">Commercial invoice</div>
            </div>
            <div class="invoice-details">
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
            <!-- Shipper (Left Top) -->
            <div class="info-box">
                <h4>SHIPPER</h4>
                <p> <u>{{ $invoice->shipper->name }}</u> </p>
                <p> <u>{{ $invoice->shipper->address1 }}</u> </p>
                @if($invoice->shipper->address2)
                <p>{{ $invoice->shipper->address2 }}</p>
                @endif
                <p>{{ $invoice->shipper->city }}</p>
                @if($invoice->shipper->contact1)
                <p>Contact: {{ $invoice->shipper->contact1 }}</p>
                @endif
                @if($invoice->shipper->contact2)
                <p> {{ $invoice->shipper->contact2 }}</p>
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
                    <th class="text-center">NUM. BOXES</th>
                    <th class="text-center">G.W. (KG)</th>
                    <th>DIMENSIONS</th>
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
                    <td class="text-center">{{ number_format($item->number_of_boxes, 2) }}</td>
                    <td class="text-center">{{ number_format($item->g_w, 2) }}</td>
                    <td>{{ $item->dimensions ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Summary -->
        <div class="summary-section">
            <div class="summary-box">
                <h4>Summary</h4>
                <p><strong>Number of Boxes:</strong> {{ $invoice->total_boxes }}</p>
                <p><strong>Total G.W.:</strong> {{ number_format($invoice->total_gw, 3) }} K/G</p>
            </div>
            <div class="summary-box financial-summary">
                <h4>Financial Summary</h4>
                <p><strong>SUBTOTAL:</strong> {{ number_format($invoice->subtotal, 2) }}</p>
                <p><strong>Shipping Value:</strong> {{ number_format($invoice->shipping_value, 2) }}</p>
                <p class="total-row">TOTAL: $ {{ number_format($invoice->total, 2) }}</p>
            </div>
        </div>
    </div>
</body>
</html>
