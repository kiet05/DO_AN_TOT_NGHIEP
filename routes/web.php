<?php

use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryController;
use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use App\Models\Product;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\PageController;

// Trang chủ
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Trang dashboard (yêu cầu đăng nhập và xác minh email)
Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

/**
 * Khu vực quản trị /admin
 * Ví dụ tên: admin.banners.index, admin.reports.index, admin.posts.index
 */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        // Dashboard admin
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Banners
        Route::resource('banners', BannerController::class)->except(['show']);
        Route::post('banners/{id}/restore', [BannerController::class, 'restore'])->name('banners.restore');
        Route::delete('banners/{id}/force', [BannerController::class, 'forceDelete'])->name('banners.force');

        // Posts
        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('posts', [PostController::class, 'store'])->name('posts.store');
        Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

        // Pages
        Route::resource('pages', PageController::class);
        Route::get('pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('pages/{key}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{key}', [PageController::class, 'update'])->name('pages.update');

        // Reports
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
        Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('reports/top-products', [ReportController::class, 'topProducts'])->name('reports.topProducts');
        Route::get('reports/top-customers', [ReportController::class, 'topCustomers'])->name('reports.topCustomers');

        // Products
        Route::prefix('products')->name('products.')->group(function () {
            Route::get('/', [ProductController::class, 'index'])->name('index');
            Route::get('/create', [ProductController::class, 'create'])->name('create');
            Route::post('/store', [ProductController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [ProductController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [ProductController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [ProductController::class, 'destroy'])->name('destroy');
        });

        // Categories
        Route::prefix('categories')->name('categories.')->group(function () {
            Route::get('/', [CategoryController::class, 'index'])->name('index');
            Route::get('/create', [CategoryController::class, 'create'])->name('create');
            Route::post('/store', [CategoryController::class, 'store'])->name('store');
            Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('edit');
            Route::put('/{id}/update', [CategoryController::class, 'update'])->name('update');
            Route::delete('/{id}/delete', [CategoryController::class, 'destroy'])->name('destroy');
        });
    });

/**
 * Cài đặt người dùng (settings)
 */
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
                [],
            ),
        )
        ->name('two-factor.show');
});

// Bao gồm các tuyến đường xác thực từ Fortify
require __DIR__ . '/auth.php';
