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
            display: inline-block;
        }
        
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }
        body {
            font-family: Arial, sans-serif;
            background: #fff;
            padding: 20px;
            color: #000;
        }

        .invoice-container {
            max-width: 210mm;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
        }

        /* Header Section */
        .header-top {
            width: 100%;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .header-left {
            float: left;
            width: 50%;
        }

        .header-right {
            float: right;
            width: 50%;
            text-align: right;
        }

        .logo-section {
            margin-bottom: 10px;
        }

        .logo-section img {
            height: 70px;
            width: auto;
            max-width: 300px;
            display: block;
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
            width: 100%;
            margin-bottom: 25px;
        }

        .info-row {
            width: 100%;
            margin-bottom: 20px;
            overflow: hidden;
        }

        .info-box {
            background: #f8f9fa;
            padding: 12px 15px;
            border-left: 4px solid #0066cc;
            float: left;
            width: 48%;
            margin-right: 2%;
        }

        .info-box:last-child {
            margin-right: 0;
        }

        .info-box.full-width {
            width: 100%;
            margin-right: 0;
            clear: both;
            margin-top: 0;
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
            width: 100%;
            margin-top: 15px;
            overflow: hidden;
        }

        .summary-box {
            padding: 0;
            float: left;
            width: 48%;
            margin-right: 2%;
        }
        
        .summary-box:last-child {
            margin-right: 0;
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
    </style>
</head>
<body>
    <div class="invoice-container">
        <!-- Header -->
        <div class="header-top clearfix">
            <div class="header-left">
                <div class="logo-section">
                    @php
                        $logoPath = null;
                        if($invoice->billTo->logo && Storage::disk('public')->exists($invoice->billTo->logo)) {
                            $logoPath = storage_path('app/public/' . $invoice->billTo->logo);
                        } else {
                            $logoPath = public_path('particula.png');
                        }
                        if($logoPath && file_exists($logoPath)) {
                            $logoData = base64_encode(file_get_contents($logoPath));
                            $logoMime = mime_content_type($logoPath);
                            $logoBase64 = 'data:' . $logoMime . ';base64,' . $logoData;
                        }
                    @endphp
                    @if(isset($logoBase64))
                        <img src="{{ $logoBase64 }}" alt="Logo">
                    @endif
                </div>
                <div class="invoice-title">Commercial invoice</div>
            </div>
            <div class="header-right invoice-details">
                <div>
                    <h3 style="display: inline;">INVOICE NO.: &nbsp;</h3>
                    <p style="display: inline; margin-top: 1px;"><strong>{{ $invoice->invoice_no }}</strong></p>
                </div>
                <div>
                    <h3 style="display: inline;">DATE.: &nbsp;</h3>
                    <p style="display: inline; margin-top: 1px;"><strong>{{ $invoice->invoice_date->format('d/m/Y') }}</strong></p>
                </div>  
                <div>
                    <h3 style="display: inline;">TERMS.: &nbsp;</h3>
                    <p style="display: inline; margin-top: 1px;"><strong>{{ $invoice->terms }}</strong></p>
                </div>  
                <br><br>
                <div>
                    <h4>MADE IN CHINA</h4>
                </div>
            </div>
        </div>

        <div class="header-divider"></div>

        <!-- Information Sections -->
        <div class="info-section">
            <!-- First Row: Supplier and Bill To -->
            <div class="info-row clearfix">
                <!-- Supplier (Left) -->
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

                <!-- Bill To (Right) -->
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
            </div>

            <!-- Second Row: Ship To (Full Width) -->
            <div class="info-row clearfix">
                <div class="info-box full-width">
                    <h4>SHIP TO</h4>
                    <p>{{ $invoice->shipTo->name }}</p>
                    <p>{{ $invoice->shipTo->address }}</p>
                </div>
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
        <div class="summary-section clearfix">
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
</body>
</html>

