<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->name('dashboard')
        ->middleware('permission:dashboard.view');

    Route::resource('staff', StaffController::class)->except(['show'])->middleware('admin');

    Route::resource('categories', CategoryController::class)->middleware('resource.permission:categories');
    Route::resource('products', ProductController::class)->middleware('resource.permission:products');
    Route::resource('services', ServiceController::class)->middleware('resource.permission:services');
    Route::resource('customers', CustomerController::class)->middleware('resource.permission:customers');
    Route::resource('suppliers', SupplierController::class)->middleware('resource.permission:suppliers');
    Route::resource('orders', OrderController::class)->except(['edit'])->middleware('resource.permission:orders');
    Route::get('orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt')->middleware('permission:orders.view');

    Route::patch('orders/{order}/update-status', [OrderController::class, 'updateStatus'])
        ->name('orders.update-status')
        ->middleware('permission:orders.status');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
