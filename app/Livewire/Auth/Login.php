<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Xử lý đăng nhập (KHÔNG OTP)
     */
    public function login(): void
    {
        $this->validate();
        $this->ensureIsNotRateLimited();

        $user = $this->validateCredentials();
        $user->loadMissing('role');

        $this->loginAndRedirect($user);
    }

    /**
     * Kiểm tra thông tin đăng nhập
     */
    protected function validateCredentials(): User
    {
        $user = Auth::getProvider()->retrieveByCredentials([
            'email' => $this->email,
        ]);

        if (
            !$user ||
            !Auth::getProvider()->validateCredentials($user, ['password' => $this->password])
        ) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return $user;
    }

    /**
     * Giới hạn số lần đăng nhập sai
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', [
                'seconds' => $seconds,
                'minutes' => ceil($seconds / 60),
            ]),
        ]);
    }

    protected function throttleKey(): string
    {
        return Str::lower($this->email) . '|' . request()->ip();
    }

    /**
     * Đăng nhập & điều hướng theo role
     */
    protected function loginAndRedirect(User $user): void
    {
        Auth::login($user, $this->remember);
        Session::regenerate();
        RateLimiter::clear($this->throttleKey());

        // Admin + Staff + Editor vào admin
        if (in_array(optional($user->role)->slug, ['admin', 'staff','editor'])) {
            $this->redirect(route('admin.dashboard'), navigate: true);
            return;
        }

        // User thường
        $this->redirect(route('home'), navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
