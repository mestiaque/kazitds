<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use ME\Http\Middleware\LocaleMiddleware;
use ME\Kazitds\Http\Controllers\{
    PackController,
    RoleController,
    SaleController,
    UserController,
    BrandController,
    DataController,
    ReportController,
    ProductController,
    ProfileController,
    SettingController,
    PurchaseController,
    SaleReturnController,
    PurchaseReturnController,
    SupplierController,
    DashboardController,
    ProductVariantController,
    GuestController,
    CustomerController,
    DueController
};

Route::middleware(['auth', LocaleMiddleware::class, 'activityLog', 'web'])->group(function () {

    Route::get('/expired', fn() => view('kazitds::expired'))->name('expired');

    Route::middleware(['auth', LocaleMiddleware::class])->group(function () {

        Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
        // Due routes
        Route::get('/language/{locale?}', [DataController::class, 'changeLocale'])->name('language.change');
        Route::get('/api/sales-chart-data', [DashboardController::class, 'salesChartData']);
        Route::resources([
            'products' => ProductController::class,
            'brands' => BrandController::class,
        'packs' => PackController::class,
        'product-variants' => ProductVariantController::class,
        'purchases' => PurchaseController::class,
        'sales' => SaleController::class,
        'suppliers' => SupplierController::class,
        'customers' => CustomerController::class,
    ]);

    Route::get('dues', [DueController::class, 'index'])->name('due.index');
    Route::get('dues/{id}/payment', [DueController::class, 'show'])->name('due.payment');
    Route::post('dues/{id}/payment', [DueController::class, 'storePayment'])->name('due.payment.store');
    Route::post('dues/{id}/notify', [DueController::class, 'notifyCustomer'])->name('due.notify.send');

    // SMS recharge/management lives in metheme (me.sms-log.index); /sms-log is the read-only report
    Route::redirect('sms-account', '/me/sms-log')->name('sms_account.show');
    Route::get('sms-log', [ReportController::class, 'smsLog'])->name('sms_log.index');

    Route::get('stock-reports', [ReportController::class, 'stock'])->name('stock.reports');
    Route::get('stock-reports/print', [ReportController::class, 'printStockReport'])->name('stock.reports.print');
    Route::get('purchase-reports', [ReportController::class, 'purchases'])->name('purchases.reports');
    Route::get('purchase-reports/print', [ReportController::class, 'printPurchasesReport'])->name('purchases.reports.print');
    Route::get('sales-reports', [ReportController::class, 'sales'])->name('sales.reports');
    Route::get('sales-reports/print', [ReportController::class, 'printSalesReport'])->name('sales.reports.print');
    Route::get('product-variant-sales-reports', [ReportController::class, 'productVariantSales'])->name('product_variant_sales.reports');
    Route::get('product-variant-sales-reports/print', [ReportController::class, 'printProductVariantSalesReport'])->name('product_variant_sales.reports.print');
    Route::get('low-stock-reports', [ReportController::class, 'low_stock'])->name('low_stock.reports');

    Route::get('/sales/{sale}/print-invoice', [SaleController::class, 'printInvoice'])->name('sales.print-invoice');
    Route::get('/api/product-variants/{id}', [SaleController::class, 'getProductVariant'])->name('api.product-variants.show');


    // Return Systems
    Route::resources([
        'purchase-returns' => PurchaseReturnController::class,
        'sale-returns' => SaleReturnController::class,
    ]);

    Route::get('/sale-returns/get-sale-items/{saleId}', [SaleReturnController::class, 'getSaleItems'])->name('sale-returns.get-items');

    Route::get('/user-guideline', fn() => view('kazitds::user-guideline'))->name('user.guideline');

});

Route::get('sales-invoice/{invoice_number}', [GuestController::class, 'saleInvoice'])->name('saleInvoice');


});
