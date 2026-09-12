@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="row g-4 mb-4">
    <div class="col-md-4 col-lg-2">
        <div class="card card-stat h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon bg-primary bg-opacity-10 text-primary me-3">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Products</h6>
                    <h4 class="mb-0">{{ $stats['products'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card card-stat h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon bg-success bg-opacity-10 text-success me-3">
                    <i class="bi bi-gem"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Services</h6>
                    <h4 class="mb-0">{{ $stats['services'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card card-stat h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon bg-info bg-opacity-10 text-info me-3">
                    <i class="bi bi-people"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Customers</h6>
                    <h4 class="mb-0">{{ $stats['customers'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card card-stat h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon bg-warning bg-opacity-10 text-warning me-3">
                    <i class="bi bi-cart3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Orders</h6>
                    <h4 class="mb-0">{{ $stats['orders'] }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card card-stat h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon bg-danger bg-opacity-10 text-danger me-3">
                    <i class="bi bi-currency-dollar"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Revenue</h6>
                    <h4 class="mb-0">${{ number_format($stats['revenue'], 0) }}</h4>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 col-lg-2">
        <div class="card card-stat h-100">
            <div class="card-body d-flex align-items-center">
                <div class="icon bg-secondary bg-opacity-10 text-secondary me-3">
                    <i class="bi bi-exclamation-triangle"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1">Low Stock</h6>
                    <h4 class="mb-0">{{ $stats['low_stock'] }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>Recent Orders</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Order #</th>
                                <th>Customer</th>
                                <th>Type</th>
                                <th>Total</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->order_number }}</td>
                                <td>{{ $order->customer->name ?? 'Walk-in' }}</td>
                                <td><span class="badge bg-{{ $order->type === 'service' ? 'success' : 'primary' }}">{{ ucfirst($order->type) }}</span></td>
                                <td>${{ number_format($order->total, 2) }}</td>
                                <td><span class="badge bg-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">{{ ucfirst($order->status) }}</span></td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No orders yet</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="bi bi-lightning me-2"></i>Quick Actions</h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('products.create') }}" class="btn btn-outline-primary">
                        <i class="bi bi-plus-circle me-2"></i>Add Product
                    </a>
                    <a href="{{ route('services.create') }}" class="btn btn-outline-success">
                        <i class="bi bi-plus-circle me-2"></i>Add Service
                    </a>
                    <a href="{{ route('customers.create') }}" class="btn btn-outline-info">
                        <i class="bi bi-plus-circle me-2"></i>Add Customer
                    </a>
                    <a href="{{ route('orders.create') }}" class="btn btn-outline-warning">
                        <i class="bi bi-plus-circle me-2"></i>New Order
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
