<?php

use App\Livewire\Auth\Login;
use Laravel\Fortify\Features;
use App\Livewire\Auth\Register;
use App\Livewire\Actions\Logout;
use App\Livewire\Auth\VerifyOtp;
use App\Http\Middleware\CheckRole;
use App\Livewire\Auth\VerifyEmail;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\Password;
use App\Livewire\Auth\ResetPassword;
use App\Livewire\Settings\TwoFactor;
use App\Livewire\Auth\ForgotPassword;
use App\Livewire\Settings\Appearance;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Frontend\BlogController;
use App\Http\Controllers\Frontend\CartController;
use App\Http\Controllers\Admin\ReviewController;
use App\Http\Controllers\Frontend\OrderController as FrontendOrderController;
use App\Http\Controllers\Frontend\PaymentController as FrontendPaymentController;

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\CustomerController;

use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Admin\ShopSettingController;
use App\Http\Controllers\Frontend\CheckoutController;
use App\Http\Controllers\Admin\AdminAccountController;
use App\Http\Controllers\Admin\PaymentMethodController;
use App\Http\Controllers\Admin\ReturnRequestController;

use App\Http\Controllers\Frontend\ProductController as FrontendProductController;
use App\Http\Controllers\Frontend\UserController;
use App\Http\Controllers\Frontend\ContactController as FrontendContactController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Frontend\ReviewController as FrontendReviewController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/products', [FrontendProductController::class, 'index'])->name('products.index');
Route::get('/products/{id}', [FrontendProductController::class, 'show'])->name('products.show');
Route::post('/products/{id}/reviews', [FrontendReviewController::class, 'store'])
    ->middleware('auth')
    ->name('products.reviews.store');

Route::get('/lien-he', [FrontendContactController::class, 'index'])->name('contact.index');
Route::post('/lien-he', [FrontendContactController::class, 'store'])->name('contact.store');

Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{id}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{id}/comments', [BlogController::class, 'storeComment'])
    ->middleware('auth')
    ->name('blog.comments.store');

Route::prefix('cart')->name('cart.')->group(function () {
    Route::get('/count', [CartController::class, 'getCount'])->name('count');
    Route::get('/sidebar', [CartController::class, 'sidebar'])->name('sidebar');
    Route::post('/add', [CartController::class, 'add'])->name('add');

    Route::middleware('auth')->group(function () {
        Route::get('/', [CartController::class, 'index'])->name('index');
        Route::put('/{id}/update', [CartController::class, 'update'])->name('update');
        Route::delete('/{id}/remove', [CartController::class, 'remove'])->name('remove');
        Route::post('/apply-voucher', [CartController::class, 'applyVoucher'])->name('apply-voucher');
        Route::post('/remove-voucher', [CartController::class, 'removeVoucher'])->name('remove-voucher');
        Route::get('/suggest-vouchers', [CartController::class, 'suggestVouchers'])->name('suggest-vouchers');
    });
});

Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
    Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
    Route::get('/checkout/failed', [CheckoutController::class, 'failed'])->name('checkout.failed');
});

Route::middleware('guest')->group(function () {
    Route::get('login', Login::class)->name('login');
    Route::get('verify-otp', VerifyOtp::class)->name('verify-otp');
    Route::get('register', Register::class)->name('register');
    Route::get('forgot-password', ForgotPassword::class)->name('password.request');
    Route::get('reset-password/{token}', ResetPassword::class)->name('password.reset');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', VerifyEmail::class)->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
});

Route::post('logout', Logout::class)
    ->middleware('auth')
    ->name('logout');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::post('/ai/chat', [\App\Http\Controllers\AiChatController::class, 'chat'])->name('ai.chat');

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

    Route::get('/profile', [UserController::class, 'index'])->name('profile.edit');
    Route::put('/profile', [UserController::class, 'update'])->name('profile.update');
    Route::post('/profile/update-avatar', [UserController::class, 'updateAvatar'])->name('profile.avatar.update');
    Route::put('/profile/password', [UserController::class, 'updatePassword'])->name('profile.password.update');

    Route::post('/profile/addresses', [UserController::class, 'storeAddress'])->name('profile.addresses.store');
    Route::put('/profile/addresses/{address}/default', [UserController::class, 'setDefaultAddress'])->name('profile.addresses.set-default');
    Route::delete('/profile/addresses/{address}', [UserController::class, 'destroyAddress'])->name('profile.addresses.destroy');
    Route::get('/profile/addresses/{address}/edit', [UserController::class, 'editAddress'])->name('profile.addresses.edit');
    Route::put('/profile/addresses/{address}', [UserController::class, 'updateAddress'])->name('profile.addresses.update');

    Route::get('/order', [FrontendOrderController::class, 'index'])->name('order.index');
    Route::get('/order/{order}', [FrontendOrderController::class, 'show'])->name('order.show');

    Route::get('/my-orders/{order}/cancel', [FrontendOrderController::class, 'showCancelForm'])->name('order.cancel.form');
    Route::post('/my-orders/{order}/cancel', [FrontendOrderController::class, 'cancel'])->name('order.cancel');

    Route::post('/my-orders/{order}/received', [FrontendOrderController::class, 'received'])->name('order.received');

    Route::get('/my-orders/{order}/return', [FrontendOrderController::class, 'showReturnForm'])->name('order.return.form');
    Route::post('/my-orders/{order}/return', [FrontendOrderController::class, 'submitReturn'])->name('order.return');

    Route::post('/my-orders/{order}/reorder', [FrontendOrderController::class, 'reorder'])->name('order.reorder');

    Route::get('payment/vnpay', [FrontendPaymentController::class, 'createPayment'])->name('vnpay.creaate');
    Route::get('/payment/vnpay-return', [FrontendPaymentController::class, 'vnpayReturn'])->name('vnpay.return');
    Route::get('/vnpay/return', [FrontendPaymentController::class, 'vnpayReturn'])->name('vnpay.return.callback');

    Route::post('/my-orders/returns/{return}/confirm-received', [FrontendOrderController::class, 'confirmRefundReceived'])
        ->name('order.return.confirmReceived');
});

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', CheckRole::class . ':admin'])
    ->group(function () {

        Route::get('/', fn() => view('admin.dashboard'))->name('dashboard');
        Route::resource('orders', OrderController::class);

        Route::get('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [App\Http\Controllers\Admin\ProfileController::class, 'update'])->name('profile.update');

        Route::resource('banners', BannerController::class)->except(['show']);
        Route::post('banners/{id}/restore', [BannerController::class, 'restore'])->name('banners.restore');
        Route::delete('banners/{id}/force', [BannerController::class, 'forceDelete'])->name('banners.force');

        Route::prefix('posts')->name('posts.')->group(function () {
            Route::get('/', [PostController::class, 'index'])->name('index');
            Route::get('/create', [PostController::class, 'create'])->name('create');
            Route::post('/', [PostController::class, 'store'])->name('store');
            Route::get('/{post}/edit', [PostController::class, 'edit'])->name('edit');
            Route::put('/{post}', [PostController::class, 'update'])->name('update');
            Route::delete('/{post}', [PostController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/store', [ProductController::class, 'store'])->name('store');
            Route::get('/{id}/show', [ProductController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [ProductController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('destroy');
        });

        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/store', [CategoryController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [CategoryController::class, 'destroy'])->name('destroy');
        });

        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::get('/orders/{order}/invoice', [OrderController::class, 'invoice'])->name('orders.invoice');
        Route::get('/orders/{order}/invoice/pdf', [OrderController::class, 'downloadInvoice'])->name('orders.invoice.pdf');

        Route::prefix('customers')->name('customers.')->group(function () {
            Route::get('/', [CustomerController::class, 'index'])->name('index');
            Route::get('/create', [CustomerController::class, 'create'])->name('create');
            Route::post('/', [CustomerController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [CustomerController::class, 'edit'])->name('edit')->whereNumber('id');
            Route::put('/{id}', [CustomerController::class, 'update'])->name('update')->whereNumber('id');
            Route::delete('/{id}', [CustomerController::class, 'destroy'])->name('destroy')->whereNumber('id');
            Route::get('/{id}', [CustomerController::class, 'show'])->name('show')->whereNumber('id');
            Route::patch('/{id}/toggle-status', [CustomerController::class, 'toggleStatus'])->name('toggleStatus')->whereNumber('id');
            Route::post('/{id}/reset-link', [CustomerController::class, 'sendResetLink'])->name('resetLink')->whereNumber('id');
            Route::post('/{id}/force-reset', [CustomerController::class, 'forceReset'])->name('forceReset')->whereNumber('id');
        });

        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [ReportController::class, 'index'])->name('index');
            Route::get('/revenue', [ReportController::class, 'revenue'])->name('revenue');
            Route::get('/top-customers', [ReportController::class, 'topCustomers'])->name('topCustomers');
            Route::get('/top-products', [ReportController::class, 'topProducts'])->name('topProducts');
        });

        Route::prefix('reviews')->name('reviews.')->group(function () {
            Route::get('/', [ReviewController::class, 'index'])->name('index');
            Route::get('/{id}', [ReviewController::class, 'show'])->name('show');
            Route::post('/{id}/approve', [ReviewController::class, 'approve'])->name('approve');
            Route::post('/{id}/reject', [ReviewController::class, 'reject'])->name('reject');
            Route::post('/{id}/unhide', [ReviewController::class, 'showReview'])->name('unhide');
            Route::delete('/{id}', [ReviewController::class, 'destroy'])->name('destroy');
        });

        Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
        Route::get('users/{id}', [AdminUserController::class, 'show'])->name('admin.users.show');
        Route::delete('users/{id}', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');

        Route::resource('accounts', AdminAccountController::class);
        Route::get('accounts', [AdminAccountController::class, 'index'])->name('accounts.index');
        Route::post('accounts/{id}/toggle-status', [AdminAccountController::class, 'toggleStatus'])->name('accounts.toggleStatus');

        Route::resource('vouchers', VoucherController::class);
        Route::get('vouchers/{voucher}/report', [VoucherController::class, 'report'])->name('vouchers.report');

        Route::get('/payments', [PaymentController::class, 'index'])->name('payments.index');
        Route::get('/payments/{payment}', [PaymentController::class, 'show'])->name('payments.show');
        Route::post('/payments/{payment}/query', [PaymentController::class, 'query'])->name('payments.query');
        Route::get('/payments/{payment}/logs', [PaymentController::class, 'logs'])->name('payments.logs');
        Route::post('/payments/{payment}/refund', [PaymentController::class, 'refund'])->name('payments.refund');
        Route::post('/payments/{payment}/status', [PaymentController::class, 'updateStatus'])->name('payments.updateStatus');

        Route::resource('payment-methods', PaymentMethodController::class);
        Route::post('payment-methods/{id}/toggle-status', [PaymentMethodController::class, 'toggleStatus'])
            ->name('payment-methods.toggle-status');

        Route::get('returns', [ReturnRequestController::class, 'index'])->name('returns.index');
        Route::get('returns/{id}', [ReturnRequestController::class, 'show'])->name('returns.show');
        Route::post('returns/{id}/approve', [ReturnRequestController::class, 'approve'])->name('returns.approve');
        Route::post('returns/{id}/reject', [ReturnRequestController::class, 'reject'])->name('returns.reject');

        Route::post('returns/{id}/refunding', [ReturnRequestController::class, 'setRefunding'])->name('returns.refunding');
        Route::post('returns/{id}/refund-auto', [ReturnRequestController::class, 'refundAuto'])->name('returns.refundAuto');
        Route::post('returns/{id}/refund-manual', [ReturnRequestController::class, 'refundManual'])->name('returns.refundManual');

        Route::get('shop-settings/edit', [ShopSettingController::class, 'edit'])->name('shop-settings.edit');
        Route::put('shop-settings', [ShopSettingController::class, 'update'])->name('shop-settings.update');

        Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
        Route::get('contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
    });
