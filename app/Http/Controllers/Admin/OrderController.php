<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index()
    {
        $orders = Order::with('customer')->latest()->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    public function create()
    {
        $customers = Customer::all();
        $products = Product::where('status', true)->get();
        $services = Service::where('status', true)->get();
        return view('admin.orders.create', compact('customers', 'products', 'services'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'nullable|exists:customers,id',
            'type' => 'required|in:product,service,mixed',
            'items' => 'required|array|min:1',
            'items.*.id' => 'required|integer',
            'items.*.type' => 'required|in:product,service',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.discount' => 'nullable|numeric|min:0',
            'tax' => 'nullable|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
        ]);

        $subtotal = 0;
        $orderItems = [];

        foreach ($request->items as $item) {
            if ($item['type'] === 'product') {
                $product = Product::findOrFail($item['id']);
                $price = $product->price;
                $itemModel = $product;
            } else {
                $service = Service::findOrFail($item['id']);
                $price = $service->price;
                $itemModel = $service;
            }

            $itemDiscount = $item['discount'] ?? 0;
            $itemSubtotal = ($price * $item['quantity']) - $itemDiscount;
            $subtotal += ($price * $item['quantity']);

            $orderItems[] = [
                'item_type' => $item['type'] === 'product' ? Product::class : Service::class,
                'item_id' => $itemModel->id,
                'quantity' => $item['quantity'],
                'price' => $price,
                'subtotal' => max(0, $itemSubtotal),
                'discount' => $itemDiscount,
            ];
        }

        $tax = $request->tax ?? 0;
        $discount = $request->discount ?? 0;
        $total = $subtotal + $tax - $discount;

        $order = Order::create([
            'order_number' => 'ORD-' . strtoupper(Str::random(8)),
            'customer_id' => $request->customer_id,
            'type' => $request->type,
            'status' => 'pending',
            'subtotal' => $subtotal,
            'tax' => $tax,
            'discount' => $discount,
            'total' => $total,
            'notes' => $request->notes,
            'order_date' => now(),
        ]);

        foreach ($orderItems as $item) {
            $order->items()->create($item);
        }

        return redirect()->route('orders.index')->with('success', 'Order created successfully.');
    }

    public function show(Order $order)
    {
        $order->load(['customer', 'items.item', 'payments']);
        return view('admin.orders.show', compact('order'));
    }

    public function receipt(Order $order)
    {
        $order->load(['customer', 'items.item', 'payments']);
        return view('admin.orders.receipt', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,processing,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);

        return back()->with('success', 'Order status updated.');
    }

    public function destroy(Order $order)
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted successfully.');
    }
}
