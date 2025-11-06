<div class="flex flex-col gap-6">
    <x-auth-header
        :title="__('Xác minh mã OTP')"
        :description="__('Nhập mã OTP đã được gửi đến email của bạn để hoàn tất đăng nhập.')"
    />

    <!-- Thông báo -->
    @if (session('status'))
        <div class="p-3 text-sm text-green-600 bg-green-100 rounded-md">
            {{ session('status') }}
        </div>
    @endif

    @if (session('error'))
        <div class="p-3 text-sm text-red-600 bg-red-100 rounded-md">
            {{ session('error') }}
        </div>
    @endif

    <form wire:submit.prevent="verify" class="flex flex-col gap-6">
        <flux:input
            wire:model="otp_code"
            :label="__('Mã OTP')"
            type="text"
            maxlength="6"
            inputmode="numeric"
            placeholder="Nhập 6 chữ số"
            required
            autofocus
        />

        @error('otp_code')
            <div class="text-sm text-red-500">{{ $message }}</div>
        @enderror

        <div class="flex flex-col gap-4">
            <flux:button type="submit" variant="primary" class="w-full">
                {{ __('Xác minh') }}
            </flux:button>

            <div class="flex items-center justify-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                <span wire:loading.remove wire:target="resendOtp">
                    {{ __('Không nhận được mã?') }}
                </span>

                <flux:button
                    variant="ghost"
                    type="button"
                    wire:click="resendOtp"
                    wire:loading.attr="disabled"
                    wire:target="resendOtp"
                    class="p-0 text-blue-600 hover:underline"
                >
                    <span wire:loading.remove wire:target="resendOtp">{{ __('Gửi lại OTP') }}</span>
                    <span wire:loading wire:target="resendOtp">{{ __('Đang gửi...') }}</span>
                </flux:button>
            </div>

            @if ($cooldown > 0)
    <p class="text-center text-xs text-gray-500">
        {{ __('Bạn có thể gửi lại mã sau') }}
        <strong id="otp-countdown" data-seconds="{{ $cooldown }}">{{ $cooldown }}</strong>
        {{ __('giây.') }}
    </p>
@endif

        </div>
    </form>
</div>
@push('scripts')
<script>
    document.addEventListener('livewire:navigated', () => {
        const countdownEl = document.getElementById('otp-countdown');
        if (!countdownEl) return;

        let seconds = parseInt(countdownEl.dataset.seconds);
        if (isNaN(seconds)) return;

        const timer = setInterval(() => {
            seconds--;
            if (seconds <= 0) {
                clearInterval(timer);
                Livewire.dispatch('refreshCooldown');
                countdownEl.textContent = '';
                return;
            }
            countdownEl.textContent = seconds;
        }, 1000);
    });
</script>
@endpush
