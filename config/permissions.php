<?php

return [
    /*
     * Permission map used to control what staff members can do.
     * Admin users bypass these checks (User::canDo() always returns true for admins).
     * Each module has separate add / edit / delete "locks" the admin toggles per staff member.
     * Keys are referenced from routes/middleware and the staff management UI.
     */
    'groups' => [
        'Dashboard' => [
            'dashboard.view' => 'View dashboard',
        ],
        'Categories' => [
            'categories.view' => 'View categories',
            'categories.add' => 'Add categories',
            'categories.edit' => 'Edit categories',
            'categories.delete' => 'Delete categories',
        ],
        'Products' => [
            'products.view' => 'View products',
            'products.add' => 'Add products',
            'products.edit' => 'Edit products',
            'products.delete' => 'Delete products',
        ],
        'Services' => [
            'services.view' => 'View services',
            'services.add' => 'Add services',
            'services.edit' => 'Edit services',
            'services.delete' => 'Delete services',
        ],
        'Customers' => [
            'customers.view' => 'View customers',
            'customers.add' => 'Add customers',
            'customers.edit' => 'Edit customers',
            'customers.delete' => 'Delete customers',
        ],
        'Suppliers' => [
            'suppliers.view' => 'View suppliers',
            'suppliers.add' => 'Add suppliers',
            'suppliers.edit' => 'Edit suppliers',
            'suppliers.delete' => 'Delete suppliers',
        ],
        'Orders' => [
            'orders.view' => 'View orders',
            'orders.add' => 'Create orders',
            'orders.delete' => 'Delete orders',
            'orders.status' => 'Update order status',
        ],
        'Staff' => [
            'staff.manage' => 'Manage staff (admin only)',
        ],
    ],
];
