<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
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
        Route::get('reports', [ReportController::class, 'index'])->name('reports.index');

        // Banners
        Route::resource('banners', BannerController::class)->except(['show']);
        // Thùng rác
        Route::post('banners/{id}/restore', [BannerController::class, 'restore'])
            ->name('banners.restore');     // POST

        Route::delete('banners/{id}/force', [BannerController::class, 'forceDelete'])
            ->name('banners.force');       // DELETE


        // Posts (đặt tên đúng và KHÔNG lặp 'admin.')
        Route::get('posts', [PostController::class, 'index'])->name('posts.index');
        Route::get('posts/create', [PostController::class, 'create'])->name('posts.create');
        Route::post('posts', [PostController::class, 'store'])->name('posts.store');
        Route::get('posts/{post}/edit', [PostController::class, 'edit'])->name('posts.edit');
        Route::put('posts/{post}', [PostController::class, 'update'])->name('posts.update');
        Route::delete('posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');
        // Hoặc dùng resource nếu cần CRUD:
        // Route::resource('posts', PostController::class)->except(['show']);
        Route::resource('pages', PageController::class);

        Route::get('pages', [PageController::class, 'index'])->name('pages.index');
        Route::get('pages/{key}/edit', [PageController::class, 'edit'])->name('pages.edit');
        Route::put('pages/{key}', [PageController::class, 'update'])->name('pages.update');
        // Reports chi tiết
        Route::get('reports/revenue', [ReportController::class, 'revenue'])->name('reports.revenue');
        Route::get('reports/top-products', [ReportController::class, 'topProducts'])->name('reports.topProducts');
        Route::get('reports/top-customers', [ReportController::class, 'topCustomers'])->name('reports.topCustomers');
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
