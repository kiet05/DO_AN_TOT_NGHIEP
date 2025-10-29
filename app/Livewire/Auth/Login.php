<?php

namespace App\Livewire\Auth;

use App\Models\User;
<<<<<<< HEAD
use App\Models\UserOtp;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
=======
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
>>>>>>> origin/feature/orders
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
<<<<<<< HEAD
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Carbon\Carbon;
=======
use Laravel\Fortify\Features;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
>>>>>>> origin/feature/orders

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
<<<<<<< HEAD
     * Xử lý đăng nhập bước 1 (kiểm tra tài khoản, gửi OTP qua email)
=======
     * Handle an incoming authentication request.
>>>>>>> origin/feature/orders
     */
    public function login(): void
    {
        $this->validate();
<<<<<<< HEAD
        $this->ensureIsNotRateLimited();

        // Kiểm tra thông tin đăng nhập
        $user = $this->validateCredentials();

        // ✅ Nếu đúng, tạo mã OTP và gửi email
        $otp = rand(100000, 999999);

        // Lưu vào bảng user_otps
        UserOtp::create([
            'user_id' => $user->id,
            'otp_code' => $otp,
            'expires_at' => Carbon::now()->addMinutes(5),
        ]);

        // Gửi email chứa mã OTP
        Mail::raw("Mã xác thực đăng nhập của bạn là: {$otp}\nMã có hiệu lực trong 5 phút.", function ($message) use ($user) {
            $message->to($user->email)
                    ->subject('Mã xác thực đăng nhập (OTP)');
        });

        // Lưu thông tin tạm thời để xác minh OTP sau
        Session::put('2fa:user:id', $user->id);
        Session::put('2fa:user:remember', $this->remember);

        // Chuyển sang bước xác minh OTP
        $this->redirect(route('verify-otp'), navigate: true);
    }

    /**
     * Kiểm tra thông tin tài khoản hợp lệ.
     */
    protected function validateCredentials(): User
    {
        $user = Auth::getProvider()->retrieveByCredentials([
            'email' => $this->email,
            'password' => $this->password,
        ]);

        if (!$user || !Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
=======

        $this->ensureIsNotRateLimited();

        $user = $this->validateCredentials();

        if (Features::canManageTwoFactorAuthentication() && $user->hasEnabledTwoFactorAuthentication()) {
            Session::put([
                'login.id' => $user->getKey(),
                'login.remember' => $this->remember,
            ]);

            $this->redirect(route('two-factor.login'), navigate: true);

            return;
        }

        Auth::login($user, $this->remember);

        RateLimiter::clear($this->throttleKey());
        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);
    }

    /**
     * Validate the user's credentials.
     */
    protected function validateCredentials(): User
    {
        $user = Auth::getProvider()->retrieveByCredentials(['email' => $this->email, 'password' => $this->password]);

        if (! $user || ! Auth::getProvider()->validateCredentials($user, ['password' => $this->password])) {
>>>>>>> origin/feature/orders
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return $user;
    }

    /**
<<<<<<< HEAD
     * Giới hạn số lần thử đăng nhập sai.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (!RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
=======
     * Ensure the authentication request is not rate limited.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
>>>>>>> origin/feature/orders
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

<<<<<<< HEAD
    protected function throttleKey(): string
    {
        return Str::lower($this->email) . '|' . request()->ip();
    }

    public function render()
    {
        return view('livewire.auth.login');
=======
    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
>>>>>>> origin/feature/orders
    }
}
