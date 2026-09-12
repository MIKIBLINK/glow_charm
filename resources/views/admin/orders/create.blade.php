@extends('layouts.app')

@section('title', 'New Order')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="mb-0">Create New Order</h5>
    <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back
    </a>
</div>

<form action="{{ route('orders.store') }}" method="POST" id="orderForm">
    @csrf
    <div class="row">
        <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white">
                        <h6 class="mb-0"><i class="bi bi-cart3 me-2"></i>Order Items</h6>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Add Product</label>
                                <input type="text" class="form-control mb-2" id="productSearch" placeholder="Search products..." autocomplete="off">
                                <select class="form-select" id="productSelect" size="6">
                                    <option value="">Search above...</option>
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}" data-price="{{ $product->price }}" data-name="{{ $product->name }}" data-type="product">
                                            {{ $product->name }} - ${{ number_format($product->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Add Service</label>
                                <input type="text" class="form-control mb-2" id="serviceSearch" placeholder="Search services..." autocomplete="off">
                                <select class="form-select" id="serviceSelect" size="6">
                                    <option value="">Search above...</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}" data-price="{{ $service->price }}" data-name="{{ $service->name }}" data-type="service">
                                            {{ $service->name }} - ${{ number_format($service->price, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                    <table class="table table-bordered" id="itemsTable">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Type</th>
                                <th width="100">Qty</th>
                                <th width="120">Price</th>
                                <th width="120">Discount</th>
                                <th width="100">Subtotal</th>
                                <th width="50"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <tr id="emptyRow">
                                <td colspan="7" class="text-center text-muted py-4">No items added yet</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Order Summary</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label for="customer_id" class="form-label">Customer</label>
                        <select class="form-select" id="customer_id" name="customer_id">
                            <option value="">Walk-in Customer</option>
                            @foreach($customers as $customer)
                                <option value="{{ $customer->id }}">{{ $customer->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">Order Type</label>
                        <select class="form-select" id="orderType" name="type" required>
                            <option value="product">Product</option>
                            <option value="service">Service</option>
                            <option value="mixed">Mixed</option>
                        </select>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Subtotal:</span>
                        <span id="subtotalDisplay">$0.00</span>
                    </div>
                    <div class="mb-3">
                        <label for="tax" class="form-label">Tax</label>
                        <input type="number" step="0.01" class="form-control" id="tax" name="tax" value="0" min="0">
                    </div>
                    <div class="mb-3">
                        <label for="discount" class="form-label">Discount</label>
                        <input type="number" step="0.01" class="form-control" id="discount" name="discount" value="0" min="0">
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total:</span>
                        <span class="fw-bold" id="totalDisplay">$0.00</span>
                    </div>
                    <div class="mb-3">
                        <label for="notes" class="form-label">Notes</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="bi bi-check-circle me-2"></i>Create Order
            </button>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
    let itemIndex = 0;
    let items = [];

    function setupSearch(selectId, searchId) {
        const select = document.getElementById(selectId);
        const search = document.getElementById(searchId);
        const options = select.querySelectorAll('option');

        if (!select || !search) return;

        search.addEventListener('input', function() {
            const term = this.value.toLowerCase();
            let firstVisible = null;

            options.forEach(option => {
                if (!option.value) return;
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(term) ? '' : 'none';
                if (firstVisible === null && option.style.display !== 'none') {
                    firstVisible = option;
                }
            });

            if (firstVisible) {
                select.value = firstVisible.value;
            }
        });

        select.addEventListener('change', function() {
            if (this.value) {
                const option = this.options[this.selectedIndex];
                addItem({
                    id: this.value,
                    name: option.dataset.name,
                    price: parseFloat(option.dataset.price),
                    type: option.dataset.type
                });
                this.value = '';
                search.value = '';
                options.forEach(option => {
                    if (option.value) option.style.display = '';
                });
            }
        });
    }

    setupSearch('productSelect', 'productSearch');
    setupSearch('serviceSelect', 'serviceSearch');

    function addItem(item) {
        const existing = items.find(i => i.id === item.id && i.type === item.type);
        if (existing) {
            existing.quantity++;
        } else {
            items.push({ ...item, quantity: 1, discount: 0 });
        }
        renderItems();
    }

    function removeItem(index) {
        items.splice(index, 1);
        renderItems();
    }

    function updateQuantity(index, value) {
        items[index].quantity = parseInt(value) || 1;
        renderItems();
    }

    function updateDiscount(index, value) {
        items[index].discount = parseFloat(value) || 0;
        renderItems();
    }

    function renderItems() {
        const tbody = document.getElementById('itemsBody');
        tbody.innerHTML = '';

        if (items.length === 0) {
            tbody.innerHTML = '<tr id="emptyRow"><td colspan="7" class="text-center text-muted py-4">No items added yet</td></tr>';
            updateTypeSelect();
            calculateTotals();
            return;
        }

        items.forEach((item, index) => {
            const subtotal = item.price * item.quantity;
            const discount = item.discount || 0;
            const finalSubtotal = Math.max(0, subtotal - discount);
            tbody.innerHTML += `
                <tr>
                    <td class="fw-semibold">${item.name}</td>
                    <td><span class="badge bg-${item.type === 'product' ? 'primary' : 'success'}">${item.type === 'product' ? 'Product' : 'Service'}</span></td>
                    <td><input type="number" class="form-control form-control-sm" value="${item.quantity}" min="1" onchange="updateQuantity(${index}, this.value)"></td>
                    <td>$${item.price.toFixed(2)}</td>
                    <td><input type="number" class="form-control form-control-sm" value="${discount}" min="0" step="0.01" onchange="updateDiscount(${index}, this.value)"></td>
                    <td>$${finalSubtotal.toFixed(2)}</td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeItem(${index})"><i class="bi bi-trash"></i></button></td>
                </tr>
                <input type="hidden" name="items[${index}][id]" value="${item.id}">
                <input type="hidden" name="items[${index}][type]" value="${item.type}">
                <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}" class="qty-input" data-index="${index}">
                <input type="hidden" name="items[${index}][discount]" value="${discount}" class="discount-input" data-index="${index}">
            `;
        });

        updateTypeSelect();
        calculateTotals();
    }

    function updateTypeSelect() {
        const types = items.map(i => i.type);
        const uniqueTypes = [...new Set(types)];
        const typeSelect = document.getElementById('orderType');

        if (uniqueTypes.length === 1) {
            typeSelect.value = uniqueTypes[0];
        } else if (uniqueTypes.length > 1) {
            typeSelect.value = 'mixed';
        }
    }

    function calculateTotals() {
        let subtotal = 0;
        let totalDiscount = 0;
        items.forEach(item => {
            const lineTotal = item.price * item.quantity;
            const discount = item.discount || 0;
            subtotal += lineTotal;
            totalDiscount += discount;
        });

        const tax = parseFloat(document.getElementById('tax').value) || 0;
        const orderDiscount = parseFloat(document.getElementById('discount').value) || 0;
        const total = subtotal - totalDiscount + tax - orderDiscount;

        document.getElementById('subtotalDisplay').textContent = '$' + subtotal.toFixed(2);
        document.getElementById('totalDisplay').textContent = '$' + total.toFixed(2);
    }

    document.getElementById('tax').addEventListener('input', calculateTotals);
    document.getElementById('discount').addEventListener('input', calculateTotals);
</script>
@endpush
