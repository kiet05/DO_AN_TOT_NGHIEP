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

    .form-check-label {
      color: #495057;
      font-size: 14px;
    }

    .text-primary-link {
      color: #2575fc !important;
      text-decoration: none;
      font-size: 14px;
    }

    .text-primary-link:hover {
      color: #1e5fd4 !important;
      text-decoration: underline;
    }

    .spinner-border-sm {
      width: 1rem;
      height: 1rem;
      border-width: 0.2em;
    }
  </style>

  <div class="login-card">
    <!-- Header -->
    <div class="login-header">
      <img src="https://cdn-icons-png.flaticon.com/512/5087/5087579.png" alt="Logo">
      <h4>Đăng ký tài khoản</h4>
      <p>Tạo tài khoản mới của bạn 🎉</p>
    </div>

    <!-- Thông báo -->
    @if(session('status'))
      <div class="alert alert-success alert-dismissible fade show text-center mb-3" role="alert">
        {{ session('status') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
      </div>
    @endif

    <!-- Form đăng ký -->
    <form wire:submit="register">
      <div class="mb-3">
        <label for="name" class="form-label">Họ và tên</label>
        <input 
          type="text" 
          wire:model="name"
          id="name" 
          class="form-control" 
          placeholder="Nhập họ và tên của bạn" 
          required 
          autofocus
        >
        @error('name')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="email" class="form-label">Email của bạn</label>
        <input 
          type="email" 
          wire:model="email"
          id="email" 
          class="form-control" 
          placeholder="email@example.com" 
          required
        >
        @error('email')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="password" class="form-label">Mật khẩu</label>
        <input 
          type="password" 
          wire:model="password"
          id="password" 
          class="form-control" 
          placeholder="••••••••" 
          required
        >
        @error('password')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <div class="mb-3">
        <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
        <input 
          type="password" 
          wire:model="password_confirmation"
          id="password_confirmation" 
          class="form-control" 
          placeholder="••••••••" 
          required
        >
        @error('password_confirmation')
          <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
      </div>

      <button type="submit" class="btn btn-login" wire:loading.attr="disabled">
        <span wire:loading.remove wire:target="register">Đăng ký ngay</span>
        <span wire:loading wire:target="register">
          <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
          Đang xử lý...
        </span>
      </button>
    </form>

    <div class="text-center mt-4">
      <p class="text-muted small mb-0">
        Đã có tài khoản?
        <a href="{{ route('login') }}" class="text-primary-link fw-semibold">Đăng nhập ngay</a>
      </p>
    </div>
  </div>
</div>