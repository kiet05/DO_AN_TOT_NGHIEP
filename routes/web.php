<?php

use Laravel\Fortify\Features;
use App\Http\Middleware\CheckRole;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ShopSettingController;
use App\Http\Controllers\Admin\PaymentController;


// ============================
// 🔹 TRANG CHỦ & DASHBOARD
// ============================
Route::get('/', fn() => view('welcome'))->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');


// ============================
// 🔹 CÀI ĐẶT NGƯỜI DÙNG
// ============================
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                []
            )
        )
        ->name('two-factor.show');
});


// ==================================================
// 🔹 KHU VỰC QUẢN TRỊ (ADMIN PANEL)
// ==================================================
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', CheckRole::class . ':admin'])
    ->group(function () {

        // Dashboard admin
        Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');

        // 🧱 Banners
        Route::resource('banners', BannerController::class)->except(['show']);
Route::post('banners/{id}/restore', [BannerController::class, 'restore'])->name('banners.restore');
Route::delete('banners/{id}/force', [BannerController::class, 'forceDelete'])->name('banners.force');

        // 📰 Posts
        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::post('/', [PostController::class, 'store'])->name('store');
            Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
            Route::put('/{post}', [PostController::class, 'update'])->name('update');
            Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
        });

        // 🛍️ Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/store', [ProductController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [ProductController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('destroy');
        });

        // 🗂️ Categories
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/store', [CategoryController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [CategoryController::class, 'destroy'])->name('destroy');
        });

        // 📦 Orders
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('/orders/{order}/invoice/pdf', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.pdf');

        //Customers
        Route::prefix('customers')->name('customers.')->group(function () {
        // LIST + CREATE/STORE + EDIT/UPDATE + DELETE
        Route::get('/',            [CustomerController::class, 'index'])->name('index');
        Route::get('/create',      [CustomerController::class, 'create'])->name('create');
        Route::post('/',           [CustomerController::class, 'store'])->name('store');
        Route::get('/{id}/edit',   [CustomerController::class, 'edit'])->name('edit')->whereNumber('id');
        Route::put('/{id}',        [CustomerController::class, 'update'])->name('update')->whereNumber('id');
        Route::delete('/{id}',     [CustomerController::class, 'destroy'])->name('destroy')->whereNumber('id');
        // SHOW + ACTIONS (đặt SAU cùng và ràng buộc id là số)
        Route::get('/{id}',                    [CustomerController::class, 'show'])->name('show')->whereNumber('id');
        Route::patch('/{id}/toggle-status',    [CustomerController::class, 'toggleStatus'])->name('toggleStatus')->whereNumber('id');
        Route::post('/{id}/reset-link',        [CustomerController::class, 'sendResetLink'])->name('resetLink')->whereNumber('id');
        Route::post('/{id}/force-reset',       [CustomerController::class, 'forceReset'])->name('forceReset')->whereNumber('id');
    });

        // 📊 Reports
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('/top-customers', [ReportController::class, 'topCustomers'])->name('topCustomers');
            Route::get('/top-products', [ReportController::class, 'topProducts'])->name('topProducts');
        });

        // 👥 Users
        Route::get('users', [UserController::class, 'index'])->name('users.index');
        Route::get('users/{id}', [UserController::class, 'show'])->name('users.show');
        Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

        // 🧑‍💼 Admin accounts
        Route::resource('accounts', AdminAccountController::class);
        Route::get('accounts', [AdminAccountController::class, 'index'])->name('accounts.index');
        Route::post('accounts/{id}/toggle-status', [AdminAccountController::class, 'toggleStatus'])->name('accounts.toggleStatus');

        // 🎟️ Vouchers
        Route::resource('vouchers', VoucherController::class);
        Route::get('vouchers/{voucher}/report', [VoucherController::class, 'report'])->name('vouchers.report');

        // 💳 Payments (Admin)
        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/query', [PaymentController::class, 'query'])->name('payments.query');
        Route::get('/payments/{payment}/logs', [PaymentController::class, 'logs'])->name('payments.logs');
        Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
        Route::post('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])
            ->name('payments.updateStatus');

        // Payment Methods
       // Route::resource('payment-methods', PaymentMethodController::class);
       // Route::post('payment-methods/{id}/toggle-status', [PaymentMethodController::class, 'toggleStatus'])
          //  ->name('payment-methods.toggle-status');

        // Shop Settings
        // Route::get('shop-settings/edit', [ShopSettingController::class, 'edit'])->name('shop-settings.edit');
        // Route::put('shop-settings', [ShopSettingController::class, 'update'])->name('shop-settings.update');
    });

// Payment routes (outside admin)
Route::prefix('payment')->name('payment.')->group(function () {
    Route::post('/process', [PaymentController::class, 'processPayment'])->name('process')->middleware('auth');
    Route::get('/vnpay/return', [PaymentController::class, 'vnpayReturn'])->name('vnpay.return');
    Route::get('/methods', [PaymentController::class, 'getPaymentMethods'])->name('methods');
});

require __DIR__ . '/auth.php';
