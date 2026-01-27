<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>

    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #000;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
        }

        .header h2 {
            margin: 0;
        }

        .info-table,
        .details-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        .info-table td {
            padding: 5px;
        }

        .details-table th,
        .details-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }

        .details-table th {
            background-color: #f2f2f2;
        }

        .section-title {
            font-weight: bold;
            margin: 15px 0 5px;
        }

        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 11px;
        }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h2>Kandura Invoice</h2>
        <p>Invoice {{ $invoice->invoice_number }}</p>
    </div>

    {{-- Invoice Info --}}
    <table class="info-table">
        <tr>
            <td><strong>Invoice Number:</strong> {{ $invoice->invoice_number }}</td>
            <td><strong>Date:</strong> {{ now()->format('Y-m-d') }}</td>
        </tr>
        <tr>
            <td><strong>Order ID:</strong> #{{ $invoice->order->id }}</td>
            <td><strong>Status:</strong> {{ $invoice->order->status }}</td>
        </tr>
    </table>

    {{-- Customer Information --}}
    <div class="section-title">Customer Information</div>
    <table class="info-table">
        <tr>
            <td><strong>Name:</strong> {{ $invoice->order->user->name }}</td>
            <td><strong>Email:</strong> {{ $invoice->order->user->email }}</td>
        </tr>
        <tr>
            <td><strong>Phone:</strong> {{ $invoice->order->phone ?? '-' }}</td>
            <td><strong>Payment Method:</strong> {{ $invoice->order->payment_method }}</td>
        </tr>
    </table>

    {{-- Shipping Address --}}
    <div class="section-title">Shipping Address</div>
    <table class="info-table">
        <tr>
            <td><strong>City:</strong> {{ $invoice->order->address->city->name }}</td>
            <td><strong>Area:</strong> {{ $invoice->order->address->area }}</td>
            <td><strong>Street:</strong> {{ $invoice->order->address->street }}</td>
            <td><strong>Langtude - Longitude:</strong> {{ $invoice->order->address->Langitude - $invoice->order->address->Longitude}}</td>
        </tr>
    </table>

    {{-- Order Details --}}
    <div class="section-title">Order Details</div>
    <table class="details-table">
        <thead>
            <tr>
                <th>Design</th>
                <th>Measurement</th>
                <th>Quantity</th>
                <th>Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->order->design as $design)
                <tr>
                    <td>{{ $design->design_name }}</td>
                    <td>{{ $design->measurement }}</td>
                    <td>{{ $design->quantity }}</td>
                    <td>{{ number_format($design->price, 2) }}</td>
                    <td>{{ number_format($design->price * $design->quantity, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Grand Total --}}
    <div class="total">
        Grand Total: {{ number_format($invoice->total, 2) }}
    </div>

    {{-- Footer --}}
    <div class="footer">
        <p>Thank you for your business</p>
        <p>This invoice was generated electronically</p>
    </div>

</body>
</html>
