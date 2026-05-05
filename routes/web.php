<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\SerialNumberController;
use App\Http\Controllers\WarrantyClaimController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/dashboard');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Shared Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Admin Only
    Route::middleware(['role:admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::resource('users', UserController::class);
        Route::post('users/{user}/toggle-active', [UserController::class, 'toggleActive'])->name('users.toggleActive');
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('products', ProductController::class);

        // Brands (admin only)
        Route::resource('brands', BrandController::class);

        // Serial Numbers (admin only)
        Route::resource('serial-numbers', SerialNumberController::class)->only(['index', 'store', 'destroy']);
        Route::get('serial-numbers/product/{product}', [SerialNumberController::class, 'byProduct'])->name('serial-numbers.by-product');
    });

    // Admin + Cashier
    Route::middleware(['role:admin|cashier'])->group(function () {
        Route::get('/pos', [PosController::class, 'index'])->name('pos.index');
        Route::get('/pos/cart', [PosController::class, 'cart'])->name('pos.cart');
        Route::post('/pos/add-item', [PosController::class, 'addItem'])->name('pos.addItem');
        Route::delete('/pos/remove-item', [PosController::class, 'removeItem'])->name('pos.removeItem');
        Route::post('/pos/empty-cart', [PosController::class, 'emptyCart'])->name('pos.emptyCart');
        Route::post('/pos/checkout', [PosController::class, 'checkout'])->name('pos.checkout');
        Route::post('/pos/mpesa-push', [PosController::class, 'initiateMpesa'])->name('pos.mpesaPush');
        Route::post('/pos/mpesa-manual-confirm', [PosController::class, 'manualConfirmMpesa'])->name('pos.mpesaManualConfirm');
        Route::get('/pos/receipt/{order}', [PosController::class, 'receipt'])->name('pos.receipt');

        Route::resource('customers', CustomerController::class);

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');

        // Warranty Claims (admin + cashier can create/view; admin can update status)
        Route::resource('warranty-claims', WarrantyClaimController::class)->except(['edit']);
        Route::patch('warranty-claims/{warrantyClaim}/status', [WarrantyClaimController::class, 'updateStatus'])
            ->name('warranty-claims.update-status')
            ->middleware('role:admin');
    });

    // Reports (all authenticated users)
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
    Route::get('/reports/export/{type}', [ReportController::class, 'export'])->name('reports.export');
    Route::get('/reports/warranty', [ReportController::class, 'warrantyReport'])->name('reports.warranty');
    Route::get('/reports/export/warranty/{format}', [ReportController::class, 'warrantyExport'])->name('reports.warranty.export');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
