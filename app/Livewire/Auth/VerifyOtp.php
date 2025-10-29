<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;

#[Layout('components.layouts.auth')]
class VerifyOtp extends Component
{
    public $otp_code;
    public $cooldown = 0;  

    public function mount()
    {
        if (!session('2fa:user:id')) {
            return redirect()->route('login');
        }
    }

    public function verify()
    {
        $userId = session('2fa:user:id');
        $user = User::find($userId);

        if (!$user) {
            return redirect()->route('login');
        }

        $record = UserOtp::where('user_id', $userId)
            ->where('otp_code', $this->otp_code)
            ->latest()
            ->first();

        if (!$record) {
            $this->addError('otp_code', 'Mã xác thực không đúng.');
            return;
        }

        if (Carbon::now()->greaterThan($record->expires_at)) {
            $this->addError('otp_code', 'Mã xác thực đã hết hạn.');
            return;
        }

        // Đăng nhập thành công
        Auth::login($user, session('2fa:user:remember'));

        // Xóa OTP đã dùng
        $record->delete();

        session()->forget(['2fa:user:id', '2fa:user:remember']);

        // ✅ Kiểm tra vai trò và điều hướng
        if ($user->role && $user->role->slug === 'admin') {
        return redirect()->route('admin.dashboard')->with('success', 'Đăng nhập thành công với tư cách Admin!');
        }

        return redirect()->route('dashboard')->with('success', 'Đăng nhập thành công!');

        
    }

    public function resendOtp()
    {
    if ($this->cooldown > 0) return;

    $userId = session('2fa:user:id');
    $user = User::find($userId);

    if (!$user) {
        return redirect()->route('login');
    }

    $otp = rand(100000, 999999);

    UserOtp::create([
        'user_id' => $user->id,
        'otp_code' => $otp,
        'expires_at' => now()->addMinutes(5),
    ]);

    Mail::raw("Mã xác thực đăng nhập mới của bạn là: {$otp}\nMã có hiệu lực trong 5 phút.", function ($message) use ($user) {
        $message->to($user->email)
                ->subject('Mã xác thực đăng nhập mới (OTP)');
    });

    $this->cooldown = 60; // 60 giây

    $this->dispatch('toast', type: 'success', message: 'Mã OTP mới đã được gửi!');
    
    }

// Lắng nghe sự kiện JS khi hết thời gian
    #[On('refreshCooldown')]
    public function refreshCooldown()
    {
        $this->cooldown = 0;
    }

    public function render()
    {
        return view('livewire.auth.verify-otp');
    }

}
