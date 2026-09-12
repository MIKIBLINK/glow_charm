<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Receipt - {{ $order->order_number }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @page { size: 80mm auto; margin: 0; }
        * { box-sizing: border-box; }
        body {
            font-family: 'Courier New', Courier, monospace;
            background: #f5f5f5;
            padding: 20px;
            font-size: 12px;
        }
        .receipt {
            background: #fff;
            max-width: 320px;
            margin: 0 auto;
            padding: 15px;
            border: 1px dashed #ccc;
        }
        .receipt .brand {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 5px;
        }
        .receipt .info {
            text-align: center;
            font-size: 11px;
            color: #555;
            margin-bottom: 10px;
        }
        .receipt .divider {
            border-top: 1px dashed #000;
            margin: 8px 0;
        }
        .receipt table {
            width: 100%;
            border-collapse: collapse;
        }
        .receipt table th {
            text-align: left;
            font-weight: bold;
            padding: 3px 0;
        }
        .receipt table td {
            padding: 3px 0;
            vertical-align: top;
        }
        .receipt .text-end { text-align: right; }
        .receipt .text-center { text-align: center; }
        .receipt .fw-bold { font-weight: bold; }
        .receipt .mt-2 { margin-top: 8px; }
        .receipt .mt-3 { margin-top: 12px; }
        .receipt .mb-0 { margin-bottom: 0; }
        .receipt .mb-2 { margin-bottom: 8px; }
        .receipt .d-flex { display: flex; }
        .receipt .justify-content-between { justify-content: space-between; }
        .no-print {
            text-align: center;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn btn-primary" onclick="window.print()">
            <i class="bi bi-printer me-2"></i>Print Receipt
        </button>
        <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-secondary ms-2">Back</a>
    </div>

    <div class="receipt" id="receiptArea">
        @if(file_exists(public_path('images/logo.png')))
            <div class="text-center mb-2">
                <img src="{{ asset('images/logo.png') }}" alt="Logo" style="height:60px;">
            </div>
        @endif
        <div class="brand">GLOW & CHARM STATION</div>
        <div class="info">
            Phnom Penh, Cambodia<br>
            Tel: +855 12 345 678
        </div>

        <div class="divider"></div>

        <div class="d-flex justify-content-between">
            <span>Invoice:</span>
            <span class="fw-bold">{{ $order->order_number }}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Date:</span>
            <span>{{ $order->order_date->format('d/m/Y H:i') }}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Cashier:</span>
            <span>{{ Auth::user()->name ?? 'Admin' }}</span>
        </div>

        <div class="divider"></div>

        <table>
            <thead>
                <tr>
                    <th>ITEM</th>
                    <th class="text-center">QTY</th>
                    <th class="text-end">AMOUNT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items as $item)
                    <tr>
                        <td>
                            {{ $item->item->name ?? 'Deleted' }}
                            @if($item->discount > 0)
                                <br><small>(Discount {{ $item->discount > 0 && $item->price > 0 ? number_format(($item->discount / ($item->price * $item->quantity)) * 100, 0) : '0' }}%)</small>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-end">${{ number_format($item->subtotal + $item->discount, 2) }}</td>
                    </tr>
                    @if($item->discount > 0)
                        <tr>
                            <td colspan="2" class="text-end text-muted">Discount:</td>
                            <td class="text-end text-muted">-${{ number_format($item->discount, 2) }}</td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>

        <div class="divider"></div>

        @php
            $subtotal = $order->items->sum(function ($i) { return $i->subtotal + $i->discount; });
            $totalDiscounts = $order->items->sum('discount');
        @endphp

        <div class="d-flex justify-content-between">
            <span>Subtotal:</span>
            <span>${{ number_format($subtotal, 2) }}</span>
        </div>
        <div class="d-flex justify-content-between">
            <span>Total Item Discounts:</span>
            <span>-${{ number_format($totalDiscounts, 2) }}</span>
        </div>

        <div class="divider"></div>

        <div class="d-flex justify-content-between fw-bold">
            <span>TOTAL:</span>
            <span>${{ number_format($order->total, 2) }}</span>
        </div>

        <div class="divider"></div>

        <div class="d-flex justify-content-between">
            <span>Payment Method:</span>
            <span>{{ $order->payments->first()->method ?? 'Cash' }}</span>
        </div>

        <div class="divider"></div>

        <div class="text-center mt-2">
            Thank you for visiting us!
        </div>
    </div>
</body>
</html>
