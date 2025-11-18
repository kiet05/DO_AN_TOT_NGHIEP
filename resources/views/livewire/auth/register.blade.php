<div>
    <h2 class="auth-title text-center">Đăng ký</h2>
    <p class="auth-subtitle text-center">Tạo tài khoản mới để bắt đầu</p>

    <!-- Session Status -->
    @if (session('status'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('status') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form method="POST" wire:submit="register">
        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">
                <i class="fas fa-user me-2"></i>Họ và tên
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-user"></i>
                </span>
                <input 
                    type="text" 
                    class="form-control @error('name') is-invalid @enderror" 
                    id="name"
                    wire:model="name"
                    placeholder="Nhập họ và tên"
                    required
                    autofocus
                    autocomplete="name"
                >
                @error('name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Email Address -->
        <div class="mb-3">
            <label for="email" class="form-label">
                <i class="fas fa-envelope me-2"></i>Email
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-envelope"></i>
                </span>
                <input 
                    type="email" 
                    class="form-control @error('email') is-invalid @enderror" 
                    id="email"
                    wire:model="email"
                    placeholder="Nhập email của bạn"
                    required
                    autocomplete="email"
                >
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Password -->
        <div class="mb-3">
            <label for="password" class="form-label">
                <i class="fas fa-lock me-2"></i>Mật khẩu
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>
                <input 
                    type="password" 
                    class="form-control @error('password') is-invalid @enderror" 
                    id="password"
                    wire:model="password"
                    placeholder="Nhập mật khẩu"
                    required
                    autocomplete="new-password"
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Confirm Password -->
        <div class="mb-4">
            <label for="password_confirmation" class="form-label">
                <i class="fas fa-lock me-2"></i>Xác nhận mật khẩu
            </label>
            <div class="input-group">
                <span class="input-group-text">
                    <i class="fas fa-lock"></i>
                </span>
                <input 
                    type="password" 
                    class="form-control @error('password_confirmation') is-invalid @enderror" 
                    id="password_confirmation"
                    wire:model="password_confirmation"
                    placeholder="Nhập lại mật khẩu"
                    required
                    autocomplete="new-password"
                >
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100 mb-3">
            <i class="fas fa-user-plus me-2"></i>Tạo tài khoản
        </button>
    </form>

    <div class="text-center">
        <span style="color: #718096; font-size: 14px;">Đã có tài khoản? </span>
        <a href="{{ route('login') }}" class="auth-link" wire:navigate>Đăng nhập</a>
    </div>
</div>
