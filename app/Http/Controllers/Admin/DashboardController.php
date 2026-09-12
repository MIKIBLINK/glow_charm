<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'products' => Product::count(),
            'services' => Service::count(),
            'customers' => Customer::count(),
            'orders' => Order::count(),
            'revenue' => Order::where('status', 'completed')->sum('total'),
            'low_stock' => Product::where('stock', '<', 10)->count(),
        ];

        $recentOrders = Order::with('customer')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentOrders'));
    }
}
