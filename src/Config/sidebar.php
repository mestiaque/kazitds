<?php

return [
    [
        'title'      => 'Dashboard',
        'icon'       => 'fas fa-tachometer-alt',
        'route'      => 'dashboard',
        'icon_color' => 'text-encodex-secondary',
    ],
    [
        'title'      => 'Master Data',
        'icon'       => 'fas fa-database',
        'icon_color' => 'text-primary',
        'children'   => [
            [
               'icon'       => 'fas fa-box',
               'title'      => 'Products',
               'route'      => 'products.index',
               'permit'     => 'product.view',
               'icon_color' => 'text-primary',
            ],
            [
               'icon'       => 'fas fa-tags',
               'title'      => 'Brands',
               'route'      => 'brands.index',
               'icon_color' => 'text-primary',
               'permit'     => 'brand.view',
            ],
            [
               'icon'       => 'fas fa-box-open',
               'title'      => 'Packs',
               'route'      => 'packs.index',
               'permit'     => 'pack.view',
               'icon_color' => 'text-primary',
            ],
            [
               'icon'       => 'fas fa-cubes',
               'title'      => 'Product Variants',
               'route'      => 'product-variants.index',
               'icon_color' => 'text-primary',
               'permit'     => 'product_variant.view',
            ],
            [
               'icon'       => 'fas fa-user-tie',
               'title'      => 'Suppliers',
               'route'      => 'suppliers.index',
               'permit'     => 'supplier.view',
               'icon_color' => 'text-primary',
            ],
            [
               'icon'       => 'fas fa-users',
               'title'      => 'Customers',
               'route'      => 'customers.index',
               'permit'     => 'customer.view',
               'icon_color' => 'text-primary',
            ],
        ]
    ],
    [
        'title'      => 'Transactions',
        'icon'       => 'fas fa-exchange-alt',
        'icon_color' => 'text-warning',
          // 'active'  => true,
        'children' => [
            [
               'icon'       => 'fas fa-shopping-cart',
               'title'      => 'Purchases',
               'route'      => 'purchases.index',
               'permit'     => 'purchase.view',
               'icon_color' => 'text-warning',
            ],
            [
               'icon'       => 'fas fa-cash-register',
               'title'      => 'Sales',
               'route'      => 'sales.index',
               'permit'     => 'sale.view',
               'icon_color' => 'text-warning',
            ],
            [
               'icon'       => 'fas fa-money-bill-wave',
               'title'      => 'Due Collection',
               'route'      => 'due.index',
               'permit'     => 'due.view',
               'icon_color' => 'text-warning',
            ],
        ]
    ],
    [
        'title'      => 'Reports',
        'icon'       => 'fas fa-chart-bar',
        'icon_color' => 'text-info',
        'children'   => [
            [
               'icon'       => 'fas fa-warehouse',
               'title'      => 'Stock Report',
               'route'      => 'stock.reports',
               'permit'     => 'stock_report.view',
               'icon_color' => 'text-info',
            ],
            [
               'icon'       => 'fas fa-shopping-cart',
               'title'      => 'Purchase Report',
               'route'      => 'purchases.reports',
               'permit'     => 'purchase_report.view',
               'icon_color' => 'text-info',
            ],
            [
               'icon'       => 'fas fa-cash-register',
               'title'      => 'Sales Report',
               'route'      => 'sales.reports',
               'permit'     => 'sale_report.view',
               'icon_color' => 'text-info',
            ],
            [
               'icon'       => 'fas fa-file-invoice',
               'title'      => 'I.W Sales Report',
               'route'      => 'product_variant_sales.reports',
               'permit'     => 'sale_report.view',
               'icon_color' => 'text-info',
            ],
            [
               'icon'       => 'fas fa-exclamation-triangle',
               'title'      => 'Low Stock',
               'route'      => 'low_stock.reports',
               'permit'     => 'low_stock.view',
               'icon_color' => 'text-info',
            ],
            [
               'icon'       => 'fas fa-comments',
               'title'      => 'SMS Log',
               'route'      => 'sms_log.index',
               'permit'     => 'sms_report.view',
               'icon_color' => 'text-info',
            ],
        ]
    ],
    [
        'title'      => 'Admin',
        'icon'       => 'fas fa-cog',
        'icon_color' => 'text-danger',
        'children'   => [
            [
                'permit'     => 'setting.edit',
                'title'      => 'Settings',
                'icon'       => 'fas fa-cog',
                'route'      => 'settings.edit',
                'icon_color' => 'text-danger',
            ],
            // [
            //     'permit'     => 'setting.configurations',
            //     'title'      => 'Configurations',
            //     'icon'       => 'fas fa-wrench',
            //     'route'      => 'configurations.edit',
            //     'icon_color' => 'text-danger',
            // ],
            // [
            //    'icon'       => 'fas fa-trash-alt',
            //    'title'      => 'Clear Data',
            //    'route'      => 'data.clear.form',
            //    'permit'     => 'admin.view',
            //    'icon_color' => 'text-danger',
            // ],
        ]
    ],
    [
        'title'      => 'User Management',
        'icon'       => 'fas fa-users-cog',
        'icon_color' => 'text-success',
        'children'   => [
            [
            'icon'       => 'fas fa-users',
            'title'      => 'Users',
            'route'      => 'users.index',
            'permit'     => 'user.view',
            'icon_color' => 'text-success',
            ],
            [
            'icon'       => 'fas fa-user-shield',
            'title'      => 'Roles',
            'route'      => 'roles.index',
            'permit'     => 'role.view',
            'icon_color' => 'text-success',
            ]
        ]
    ],
];
