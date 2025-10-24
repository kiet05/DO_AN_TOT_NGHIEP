<?php

use App\Livewire\Settings\Appearance;
use App\Livewire\Settings\Password;
use App\Livewire\Settings\Profile;
use App\Livewire\Settings\TwoFactor;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
<<<<<<< HEAD
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\NotificationController;
=======
>>>>>>> 6e27f9aa04493d2bfa9f40b2fca490bdbb0905cb

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

<<<<<<< HEAD
/**
 * Khu vực quản trị /admin
 * Tên route sẽ là: admin.banners.* và admin.notifications.*
 */
Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified'])
    ->group(function () {
        Route::resource('banners', BannerController::class)->except(['show']);
        Route::resource('notifications', NotificationController::class)->except(['show']);
    });

/**
 * Settings của user
 */
=======
>>>>>>> 6e27f9aa04493d2bfa9f40b2fca490bdbb0905cb
Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Route::get('settings/profile', Profile::class)->name('settings.profile');
    Route::get('settings/password', Password::class)->name('settings.password');
    Route::get('settings/appearance', Appearance::class)->name('settings.appearance');

    Route::get('settings/two-factor', TwoFactor::class)
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
<<<<<<< HEAD
                && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
=======
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
>>>>>>> 6e27f9aa04493d2bfa9f40b2fca490bdbb0905cb
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});

<<<<<<< HEAD
require __DIR__ . '/auth.php';
=======
require __DIR__.'/auth.php';
>>>>>>> 6e27f9aa04493d2bfa9f40b2fca490bdbb0905cb
