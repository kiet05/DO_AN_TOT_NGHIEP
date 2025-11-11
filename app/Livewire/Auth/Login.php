<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Carbon\Carbon;

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Xử lý đăng nhập bước 1 (kiểm tra tài khoản, gửi OTP qua email)
     */
    public function login(): void
    {
        $this->validate();
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

        // Gửi email chứa mã OTP (với try-catch để tránh lỗi nếu SMTP không cấu hình đúng)
        try {
            Mail::raw("Mã xác thực đăng nhập của bạn là: {$otp}\nMã có hiệu lực trong 5 phút.", function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Mã xác thực đăng nhập (OTP)');
            });
        } catch (\Exception $e) {
            \Log::error('Không thể gửi email OTP: ' . $e->getMessage());
            // Nếu ở chế độ debug, hiển thị OTP trong session để test
            if (config('app.debug')) {
                Session::flash('otp_debug', "Mã OTP (chỉ hiển thị trong debug): {$otp}");
            }
        }

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
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        return $user;
    }

    /**
     * Giới hạn số lần thử đăng nhập sai.
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

    public function render()
    {
        return view('livewire.auth.login');
    }
}
