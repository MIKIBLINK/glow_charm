<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Service;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@glowcharm.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'status' => true,
        ]);

        $productCategories = ['Skincare', 'Haircare', 'Makeup', 'Fragrance'];
        foreach ($productCategories as $cat) {
            Category::create([
                'name' => $cat,
                'slug' => \Illuminate\Support\Str::slug($cat),
                'type' => 'product',
                'status' => true,
            ]);
        }

        $serviceCategories = ['Facial Treatments', 'Massage', 'Hair Services', 'Nail Care'];
        foreach ($serviceCategories as $cat) {
            Category::create([
                'name' => $cat,
                'slug' => \Illuminate\Support\Str::slug($cat),
                'type' => 'service',
                'status' => true,
            ]);
        }

        $products = [
            ['name' => 'Hydrating Face Cream', 'sku' => 'SKN-001', 'price' => 45.00, 'cost' => 20.00, 'stock' => 50, 'category_id' => 1],
            ['name' => 'Vitamin C Serum', 'sku' => 'SKN-002', 'price' => 65.00, 'cost' => 30.00, 'stock' => 30, 'category_id' => 1],
            ['name' => 'Repair Shampoo', 'sku' => 'HRC-001', 'price' => 28.00, 'cost' => 12.00, 'stock' => 100, 'category_id' => 2],
            ['name' => 'Matte Lipstick', 'sku' => 'MKP-001', 'price' => 22.00, 'cost' => 8.00, 'stock' => 75, 'category_id' => 3],
            ['name' => 'Rose Perfume', 'sku' => 'FRG-001', 'price' => 85.00, 'cost' => 40.00, 'stock' => 25, 'category_id' => 4],
        ];
        foreach ($products as $product) {
            Product::create(array_merge($product, [
                'description' => 'Premium quality ' . strtolower($product['name']),
                'status' => true,
            ]));
        }

        $services = [
            ['name' => 'Classic Facial', 'price' => 80.00, 'duration' => 60, 'category_id' => 5],
            ['name' => 'Anti-Aging Facial', 'price' => 120.00, 'duration' => 90, 'category_id' => 5],
            ['name' => 'Relaxing Massage', 'price' => 70.00, 'duration' => 60, 'category_id' => 6],
            ['name' => 'Hair Cut & Style', 'price' => 50.00, 'duration' => 45, 'category_id' => 7],
            ['name' => 'Manicure & Pedicure', 'price' => 45.00, 'duration' => 60, 'category_id' => 8],
        ];
        foreach ($services as $service) {
            Service::create(array_merge($service, [
                'slug' => \Illuminate\Support\Str::slug($service['name']),
                'description' => 'Professional ' . strtolower($service['name']) . ' service',
                'status' => true,
            ]));
        }

        Customer::create([
            'name' => 'Sarah Johnson',
            'email' => 'sarah@example.com',
            'phone' => '555-0101',
            'city' => 'New York',
            'gender' => 'female',
        ]);

        Customer::create([
            'name' => 'Emily Davis',
            'email' => 'emily@example.com',
            'phone' => '555-0102',
            'city' => 'Los Angeles',
            'gender' => 'female',
        ]);

        Supplier::create([
            'name' => 'Beauty Supply Co.',
            'contact_person' => 'John Smith',
            'email' => 'john@beautysupply.com',
            'phone' => '555-0201',
            'city' => 'Chicago',
            'status' => true,
        ]);

        Supplier::create([
            'name' => 'Luxury Fragrances Ltd.',
            'contact_person' => 'Maria Garcia',
            'email' => 'maria@luxuryfrag.com',
            'phone' => '555-0202',
            'city' => 'Miami',
            'status' => true,
        ]);
    }
}
