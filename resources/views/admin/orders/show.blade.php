@extends('layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Order #{{ $order->order_number }}</h5>
    <div>
        <a href="{{ route('orders.receipt', $order) }}" class="btn btn-outline-primary" target="_blank">
            <i class="bi bi-printer me-2"></i>Print Receipt
        </a>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary ms-2">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-list-ul me-2"></i>Order Items</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead class="table-light">
                            <tr>
                                <th width="80">Image</th>
                                <th>Item</th>
                                <th>Type</th>
                                <th>Qty</th>
                                <th>Price</th>
                                <th>Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($order->items as $item)
                            <tr>
                                <td>
                                    @php
                                        $image = null;
                                        if ($item->item && method_exists($item->item, 'image')) {
                                            $image = $item->item->image;
                                        }
                                    @endphp
                                    @if($image)
                                        <a href="{{ route('orders.receipt', $order) }}" target="_blank" class="text-decoration-none">
                                            <img src="{{ asset('storage/' . $image) }}" alt="{{ $item->item->name ?? 'Item' }}" style="width:60px;height:60px;object-fit:cover;border-radius:8px;cursor:pointer;">
                                        </a>
                                    @else
                                        <div style="width:60px;height:60px;background:#e9ecef;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                                            <i class="bi bi-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td class="fw-semibold">{{ $item->item->name ?? 'Deleted' }}</td>
                                <td><span class="badge bg-{{ $item->item_type === 'App\Models\Product' ? 'primary' : 'success' }}">{{ $item->item_type === 'App\Models\Product' ? 'Product' : 'Service' }}</span></td>
                                <td>{{ $item->quantity }}</td>
                                <td>${{ number_format($item->price, 2) }}</td>
                                <td>${{ number_format($item->subtotal, 2) }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-info-circle me-2"></i>Order Info</h6>
            </div>
            <div class="card-body">
                <p><strong>Customer:</strong> {{ $order->customer->name ?? 'Walk-in' }}</p>
                <p><strong>Type:</strong> <span class="badge bg-{{ $order->type === 'service' ? 'success' : 'primary' }}">{{ ucfirst($order->type) }}</span></p>
                <p><strong>Date:</strong> {{ $order->order_date->format('M d, Y H:i') }}</p>
                <p><strong>Status:</strong>
                    <span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : ($order->status === 'cancelled' ? 'danger' : 'info')) }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </p>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal:</span>
                    <span>${{ number_format($order->subtotal, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Tax:</span>
                    <span>${{ number_format($order->tax, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Discount:</span>
                    <span>-${{ number_format($order->discount, 2) }}</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span class="fw-bold">Total:</span>
                    <span class="fw-bold">${{ number_format($order->total, 2) }}</span>
                </div>
                @if($order->notes)
                    <hr>
                    <p class="text-muted"><strong>Notes:</strong> {{ $order->notes }}</p>
                @endif
            </div>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h6 class="mb-0"><i class="bi bi-arrow-repeat me-2"></i>Update Status</h6>
            </div>
            <div class="card-body">
                <form action="{{ route('orders.update-status', $order) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <select name="status" class="form-select mb-3">
                        <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status === 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
