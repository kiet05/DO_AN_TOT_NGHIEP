<div>
    <h2 class="auth-title text-center">Đăng nhập</h2>
    <p class="auth-subtitle text-center">Nhập email và mật khẩu để đăng nhập</p>

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

    <form method="POST" wire:submit="login">
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
                    autofocus
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
                    autocomplete="current-password"
                >
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            @if (Route::has('password.request'))
                <div class="text-end mt-2">
                    <a href="{{ route('password.request') }}" class="auth-link" style="font-size: 13px;">
                        Quên mật khẩu?
                    </a>
                </div>
            @endif
        </div>

        <!-- Remember Me -->
        <div class="mb-4">
            <div class="form-check">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    id="remember"
                    wire:model="remember"
                >
                <label class="form-check-label" for="remember">
                    Ghi nhớ đăng nhập
                </label>
            </div>
        </div>

        <!-- Submit Button -->
        <button type="submit" class="btn btn-primary w-100 mb-3" data-test="login-button">
            <i class="fas fa-sign-in-alt me-2"></i>Đăng nhập
        </button>
    </form>

    @if (Route::has('register'))
        <div class="text-center">
            <span style="color: #718096; font-size: 14px;">Chưa có tài khoản? </span>
            <a href="{{ route('register') }}" class="auth-link" wire:navigate>Đăng ký ngay</a>
        </div>
    @endif
</div>
