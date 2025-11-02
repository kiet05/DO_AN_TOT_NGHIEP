<?php

namespace App\Livewire\Auth;

use App\Models\User;
use App\Models\UserOtp;
use Carbon\Carbon;
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

#[Layout('components.layouts.auth')]
class Login extends Component
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    public bool $remember = false;

    /**
     * Bước 1: kiểm tra tài khoản, tạo & gửi OTP
     */
    public function login(): void
    {
        $this->validate();
        $this->ensureIsNotRateLimited();

        // 1) Xác thực email + password
        $user = $this->validateCredentials();

        // 2) Tạo OTP (6 số) + hạn dùng 5 phút
        $otp = (string) random_int(100000, 999999);

        // 3) Lưu/ghi đè OTP cho user (mỗi user giữ 1 bản ghi cho gọn)
        UserOtp::updateOrCreate(
            ['user_id' => $user->id],
            [
                'otp_code'   => $otp,
                'expires_at' => Carbon::now()->addMinutes(5),
                'used_at'    => null,
            ]
        );

        // 4) Gửi email OTP (không làm app crash nếu mail chưa cấu hình)
        try {
            Mail::raw(
                "Mã xác thực đăng nhập của bạn là: {$otp}\nMã có hiệu lực trong 5 phút.",
                function ($message) use ($user) {
                    $message->to($user->email)
                        ->subject('Mã xác thực đăng nhập (OTP)');
                }
            );
        } catch (\Throwable $e) {
            // Ghi log và cung cấp OTP cho dev ở môi trường local
            logger()->warning('Gửi OTP thất bại', [
                'email' => $user->email,
                'otp'   => $otp,
                'error' => $e->getMessage(),
            ]);

            if (app()->environment('local')) {
                session()->flash('dev_otp', $otp);
            }

            report($e);
        }

        // 5) Lưu context cho bước nhập OTP
        Session::put('2fa:user:id', $user->id);
        Session::put('2fa:user:remember', $this->remember);

        // 6) Điều hướng sang trang nhập OTP
        $this->redirect(route('verify-otp'), navigate: true);
    }

    /**
     * Lấy user & kiểm tra mật khẩu.
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
     * Chống brute force.
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
