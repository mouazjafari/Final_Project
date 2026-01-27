<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>

    <style>
        body {
            font-family: 'DejaVuSans', sans-serif;
            font-size: 14px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #2c3e50;
            padding-bottom: 15px;
        }

        .header h2 {
            margin: 0;
            color: #2c3e50;
            font-size: 28px;
            font-weight: bold;
        }

        .header p {
            margin: 5px 0;
            color: #555;
            font-size: 12px;
        }

        .info-table,
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 10px;
            border: 1px solid #ddd;
            background-color: #f9f9f9;
        }

        .info-table td strong {
            color: #2c3e50;
        }

        .details-table th,
        .details-table td {
            border: 1px solid #2c3e50;
            padding: 10px;
            text-align: center;
        }

        .details-table th {
            background-color: #2c3e50;
            color: white;
            font-weight: bold;
        }

        .details-table tbody tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        .section-title {
            font-weight: bold;
            margin: 20px 0 10px;
            color: #2c3e50;
            font-size: 18px;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 5px;
        }

        .total-section {
            margin-top: 20px;
            border-top: 2px solid #2c3e50;
            padding-top: 15px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            font-size: 16px;
        }

        .total-row.grand-total {
            font-size: 20px;
            font-weight: bold;
            color: #2c3e50;
            border-top: 2px solid #2c3e50;
            margin-top: 10px;
            padding-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #2c3e50;
            font-size: 11px;
            color: #777;
        }

        .option-badge {
            display: inline-block;
            background-color: #e8f4f8;
            color: #2c3e50;
            padding: 4px 10px;
            margin: 2px;
            border-radius: 4px;
            font-size: 11px;
        }

        .discount-row {
            color: #27ae60;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h2>Kandura Store Invoice</h2>
        <p>Invoice Number: {{ $invoice->invoice_number }}</p>
        <p>Date: {{ $invoice->created_at->format('Y-m-d H:i') }}</p>
    </div>

    {{-- Invoice Info --}}
    <table class="info-table">
        <tr>
            <td style="width: 50%;"><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</td>
            <td style="width: 50%;"><strong>Issue Date:</strong> {{ $invoice->created_at->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td><strong>Order ID:</strong> #{{ $invoice->order->id }}</td>
            <td><strong>Order Status:</strong>
                @if($invoice->order->status === 'pending')
                    Pending
                @elseif($invoice->order->status === 'processing')
                    Processing
                @elseif($invoice->order->status === 'completed')
                    Completed
                @elseif($invoice->order->status === 'cancelled')
                    Cancelled
                @else
                    {{ ucfirst($invoice->order->status) }}
                @endif
            </td>
        </tr>
    </table>

    {{-- Customer Information --}}
    <div class="section-title">Customer Information</div>
    <table class="info-table">
        <tr>
            <td style="width: 50%;"><strong>Name:</strong> {{ $invoice->order->user->name }}</td>
            <td style="width: 50%;"><strong>Email:</strong> {{ $invoice->order->user->email }}</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong> {{ $invoice->order->user->phone_number ?? '-' }}</td>
            <td><strong>Payment Method:</strong> {{ ucfirst($invoice->order->payments->first()->payment_method ?? '-') }}</td>
        </tr>
    </table>

    {{-- Shipping Address --}}
    <div class="section-title">Shipping Address</div>
    <table class="info-table">
        <tr>
            <td style="width: 33%;"><strong>City:</strong>
                {{ $invoice->order->address->city->getTranslation('name', 'en') ?? $invoice->order->address->city->name }}
            </td>
            <td style="width: 33%;"><strong>Area:</strong> {{ $invoice->order->address->area }}</td>
            <td style="width: 34%;"><strong>Street:</strong> {{ $invoice->order->address->street }}</td>
        </tr>
        @if($invoice->order->address->Langitude && $invoice->order->address->Longitude)
        <tr>
            <td colspan="3">
                <strong>Coordinates:</strong>
                Lat: {{ $invoice->order->address->Langitude }},
                Long: {{ $invoice->order->address->Longitude }}
            </td>
        </tr>
        @endif
        @if($invoice->order->address->notes)
        <tr>
            <td colspan="3"><strong>Address Notes:</strong> {{ $invoice->order->address->notes }}</td>
        </tr>
        @endif
    </table>

    {{-- Order Details --}}
    <div class="section-title">Order Details</div>
    <table class="details-table">
        <thead>
            <tr>
                <th style="width: 5%;">#</th>
                <th style="width: 25%;">Design</th>
                <th style="width: 15%;">Size</th>
                <th style="width: 20%;">Options</th>
                <th style="width: 10%;">Qty</th>
                <th style="width: 12%;">Unit Price</th>
                <th style="width: 13%;">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->order->designOrders as $index => $designOrder)
                @php
                    // Process design name
                    $designName = 'N/A';
                    if ($designOrder->design && $designOrder->design->name) {
                        $nameData = is_string($designOrder->design->name)
                            ? json_decode($designOrder->design->name, true)
                            : $designOrder->design->name;

                        $designName = is_array($nameData)
                            ? ($nameData['en'] ?? $nameData['ar'] ?? 'N/A')
                            : $designOrder->design->name;
                    }

                    // Process size name
                    $sizeName = '-';
                    if ($designOrder->size) {
                        $sizeData = is_string($designOrder->size->name)
                            ? json_decode($designOrder->size->name, true)
                            : $designOrder->size->name;

                        $sizeName = is_array($sizeData)
                            ? ($sizeData['en'] ?? $sizeData['ar'] ?? $designOrder->size->name)
                            : $designOrder->size->name;
                    }

                    // Calculate item total
                    $itemTotal = $designOrder->unit_price * $designOrder->quantity;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $designName }}</td>
                    <td>{{ $sizeName }}</td>
                    <td>
                        @if($designOrder->options && $designOrder->options->count() > 0)
                            @foreach($designOrder->options as $option)
                                @php
                                    $optionName = 'Option';
                                    if ($option->name) {
                                        if (is_array($option->name)) {
                                            $optionName = $option->name['en'] ?? $option->name['ar'] ?? 'Option';
                                        } elseif (is_string($option->name) && str_starts_with($option->name, '{')) {
                                            $decoded = json_decode($option->name, true);
                                            $optionName = $decoded['en'] ?? $decoded['ar'] ?? 'Option';
                                        } else {
                                            $optionName = $option->name;
                                        }
                                    }
                                @endphp
                                <span class="option-badge">{{ $optionName }}</span>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                    <td>{{ $designOrder->quantity }}</td>
                    <td>${{ number_format($designOrder->unit_price, 2) }}</td>
                    <td>${{ number_format($itemTotal, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Order Notes --}}
    @if($invoice->order->notes)
    <div class="section-title">Order Notes</div>
    <table class="info-table">
        <tr>
            <td>{{ $invoice->order->notes }}</td>
        </tr>
    </table>
    @endif

    {{-- Total Section --}}
    <div class="total-section">
        <div class="total-row">
            <span><strong>Subtotal:</strong></span>
            <span>${{ number_format($invoice->order->total_price + ($invoice->order->discount_amount ?? 0), 2) }}</span>
        </div>

        @if($invoice->order->coupon_id && $invoice->order->discount_amount > 0)
        <div class="total-row discount-row">
            <span><strong>Discount ({{ $invoice->order->coupon->code }}):</strong></span>
            <span>- ${{ number_format($invoice->order->discount_amount, 2) }}</span>
        </div>
        @endif

        <div class="total-row grand-total">
            <span>Grand Total:</span>
            <span>${{ number_format($invoice->total, 2) }}</span>
        </div>
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p><strong>Thank you for your business!</strong></p>
        <p>This invoice was generated electronically</p>
        <p>For inquiries, please contact us via email or phone</p>
        <p style="margin-top: 10px; color: #2c3e50;">
            &copy; {{ now()->year }} Kandura Store - All Rights Reserved
        </p>
    </div>

</body>
</html>
