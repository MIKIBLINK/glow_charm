@extends('layouts.app')

@section('title', 'Home')

@section('content')
<div class="py-5">
    @auth
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">Welcome back, {{ Auth::user()->name }} 👋</h4>
                <p class="text-muted mb-0">Here's your quick access to the system.</p>
            </div>
        </div>

        <div class="row g-4">
            @if(auth()->user()->canDo('products.view'))
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('products.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 card-hover">
                        <div class="card-body">
                            <div class="icon bg-primary bg-opacity-10 text-primary mb-3"><i class="bi bi-box-seam"></i></div>
                            <h6 class="fw-semibold text-dark">Products</h6>
                            <small class="text-muted">Browse & manage</small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(auth()->user()->canDo('services.view'))
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('services.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 card-hover">
                        <div class="card-body">
                            <div class="icon bg-success bg-opacity-10 text-success mb-3"><i class="bi bi-gem"></i></div>
                            <h6 class="fw-semibold text-dark">Services</h6>
                            <small class="text-muted">Browse & manage</small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(auth()->user()->canDo('customers.view'))
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('customers.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 card-hover">
                        <div class="card-body">
                            <div class="icon bg-info bg-opacity-10 text-info mb-3"><i class="bi bi-people"></i></div>
                            <h6 class="fw-semibold text-dark">Customers</h6>
                            <small class="text-muted">Browse & manage</small>
                        </div>
                    </div>
                </a>
            </div>
            @endif

            @if(auth()->user()->canDo('orders.view'))
            <div class="col-sm-6 col-lg-3">
                <a href="{{ route('orders.index') }}" class="text-decoration-none">
                    <div class="card border-0 shadow-sm h-100 card-hover">
                        <div class="card-body">
                            <div class="icon bg-warning bg-opacity-10 text-warning mb-3"><i class="bi bi-cart3"></i></div>
                            <h6 class="fw-semibold text-dark">Orders</h6>
                            <small class="text-muted">Browse & manage</small>
                        </div>
                    </div>
                </a>
            </div>
            @endif
        </div>

        @if(auth()->user()->isAdmin())
        <div class="mt-4">
            <a href="{{ route('dashboard') }}" class="btn btn-primary">
                <i class="bi bi-speedometer2 me-2"></i>Go to Dashboard
            </a>
        </div>
        @endif
    @else
        <div class="text-center py-5">
            <div class="mb-4">
                <i class="bi bi-stars" style="font-size: 4rem; background: linear-gradient(135deg, #ff7eb3, #9b6bff); -webkit-background-clip: text; background-clip: text; -webkit-text-fill-color: transparent;"></i>
            </div>
            <h1 class="fw-bold mb-3">Glow &amp; Charm Station</h1>
            <p class="text-muted mb-4">Service &amp; Product Management System</p>
            <div class="d-flex justify-content-center gap-3">
                <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-4">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg rounded-pill px-4">
                    <i class="bi bi-person-plus me-2"></i>Register
                </a>
            </div>
        </div>
    @endauth
</div>
@endsection
