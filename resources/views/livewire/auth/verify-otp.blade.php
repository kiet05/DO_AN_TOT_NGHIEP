<div class="login-wrapper">
  <style>
    .login-wrapper {
      width: 100%;
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 20px;
      background: linear-gradient(135deg, #6a11cb, #2575fc);
      position: relative;
    }

    .login-card {
      width: 100%;
      max-width: 400px;
      background-color: rgba(255, 255, 255, 0.95);
      border-radius: 20px;
      padding: 40px 30px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
      animation: fadeIn 0.6s ease;
    }

    @keyframes fadeIn {
      from { 
        opacity: 0; 
        transform: translateY(20px); 
      }
      to { 
        opacity: 1; 
        transform: translateY(0); 
      }
    }

    .login-header {
      text-align: center;
      margin-bottom: 30px;
    }

    .login-header img {
      width: 70px;
      height: 70px;
      margin-bottom: 15px;
      border-radius: 50%;
    }

    .login-header h4 {
      font-weight: 700;
      color: #2575fc;
      margin-bottom: 8px;
      font-size: 1.5rem;
    }

    .login-header p {
      color: #6c757d;
      font-size: 14px;
      margin: 0;
    }

    .form-label {
      font-weight: 500;
      color: #495057;
      margin-bottom: 8px;
    }

    .form-control {
      border-radius: 10px;
      border: 1px solid #dee2e6;
      padding: 12px 15px;
      transition: all 0.3s ease;
      font-size: 1.2rem;
      text-align: center;
      letter-spacing: 0.5rem;
    }

    .form-control:focus {
      border-color: #2575fc;
      box-shadow: 0 0 0 0.2rem rgba(37, 117, 252, 0.25);
    }

    .btn-login {
      background: linear-gradient(45deg, #6a11cb, #2575fc);
      border: none;
      color: white;
      font-weight: 600;
      padding: 12px;
      border-radius: 10px;
      transition: all 0.3s ease;
      width: 100%;
    }

    .btn-login:hover {
      opacity: 0.9;
      transform: translateY(-2px);
      box-shadow: 0 5px 15px rgba(106, 17, 203, 0.3);
    }

    .btn-login:disabled {
      opacity: 0.7;
      cursor: not-allowed;
    }

    .text-primary-link {
      color: #2575fc !important;
      text-decoration: none;
      font-size: 14px;
      cursor: pointer;
      background: none;
      border: none;
      padding: 0;
    }

    .text-primary-link:hover {
      color: #1e5fd4 !important;
      text-decoration: underline;
    }

    .text-primary-link:disabled {
      opacity: 0.5;
      cursor: not-allowed;
    }

    .spinner-border-sm {
      width: 1rem;
      height: 1rem;
      border-width: 0.2em;
    }

    .otp-resend-section {
      text-align: center;
      margin-top: 20px;
    }

    .countdown-text {
      color: #6c757d;
      font-size: 13px;
    }

    .countdown-number {
      color: #2575fc;
      font-weight: 600;
    }
  </style>

  <div class="login-card">
    <!-- Header -->
    <div class="login-header">
      <img src="https://cdn-icons-png.flaticon.com/512/5087/5087579.png" alt="Logo">
      <h4>Xác minh mã OTP</h4>
      <p>Nhập mã 6 chữ số đã được gửi đến email của bạn 🔐</p>
    </div>

    <!-- Thông báo -->
    @if(session('status'))
      <div class="alert alert-success alert-dismissible fade show text-center mb-3" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    @if(session('error'))
      <div class="alert alert-danger alert-dismissible fade show text-center mb-3" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <!-- Form xác minh OTP -->
    <form wire:submit.prevent="verify">
      <div class="mb-3">
        <label for="otp_code" class="form-label">Mã OTP</label>
        <input 
          type="text" 
          wire:model="otp_code"
          id="otp_code" 
          class="form-control" 
          placeholder="000000" 
          maxlength="6"
          inputmode="numeric"
          pattern="[0-9]*"
          required 
          autofocus
        >
        @error('otp_code')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="btn btn-login" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="verify">Xác minh</span>
        <span wire:loading wire:target="verify">
          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          Đang xử lý...
        </span>
      </button>
    </form>

    <!-- Resend OTP Section -->
    <div class="otp-resend-section">
      @if($cooldown > 0)
        <p class="countdown-text mb-2">
          Bạn có thể gửi lại mã sau 
          <span class="countdown-number" id="otp-countdown" data-seconds="{{ $cooldown }}">{{ $cooldown }}</span>
          giây
        </p>
      @else
        <div class="d-flex align-items-center justify-content-center gap-2">
          <span class="text-muted small">Không nhận được mã?</span>
          <button 
            type="button"
            wire:click="resendOtp"
            wire:loading.attr="disabled"
            wire:target="resendOtp"
            class="text-primary-link"
          >
            <span wire:loading.remove wire:target="resendOtp">Gửi lại OTP</span>
            <span wire:loading wire:target="resendOtp">
              <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
              Đang gửi...
            </span>
          </button>
        </div>
      @endif
    </div>

    <div class="text-center mt-4">
      <a href="{{ route('login') }}" class="text-primary-link small">Quay lại đăng nhập</a>
    </div>
  </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => {
        const countdownEl = document.getElementById('otp-countdown');
        if (!countdownEl) return;

        let seconds = parseInt(countdownEl.dataset.seconds);
        if (isNaN(seconds) || seconds <= 0) return;

        const timer = setInterval(() => {
            seconds--;
            if (seconds <= 0) {
                clearInterval(timer);
                @this.call('refreshCooldown');
                countdownEl.parentElement.style.display = 'none';
                return;
            }
            countdownEl.textContent = seconds;
        }, 1000);
    });

    // Xử lý input OTP - chỉ cho phép số và tự động focus
    document.addEventListener('DOMContentLoaded', function() {
        const otpInput = document.getElementById('otp_code');
        if (otpInput) {
            otpInput.addEventListener('input', function(e) {
                // Chỉ cho phép số
                e.target.value = e.target.value.replace(/[^0-9]/g, '');
            });
            
            otpInput.addEventListener('keypress', function(e) {
                // Chỉ cho phép nhập số
                if (!/[0-9]/.test(e.key)) {
                    e.preventDefault();
                }
            });
        }
    });
</script>
@endpush