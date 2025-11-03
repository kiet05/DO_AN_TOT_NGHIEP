<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);
        
        // Lấy role "customer" (khách hàng) bằng slug thay vì hardcode ID
        $customerRole = \App\Models\Role::where('slug', 'customer')->first();
        if (!$customerRole) {
            // Nếu không tìm thấy, tạo role customer mặc định
            $customerRole = \App\Models\Role::create([
                'name' => 'Khách hàng',
                'slug' => 'customer',
                'description' => 'Người dùng bình thường'
            ]);
        }
        $validated['role_id'] = $customerRole->id;
        
        // Tạo slug từ name, đảm bảo unique
        $baseSlug = \Illuminate\Support\Str::slug($validated['name']);
        $slug = $baseSlug;
        $counter = 1;
        
        // Kiểm tra và tạo slug unique nếu trùng
        while (\App\Models\User::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }
        $validated['slug'] = $slug;

        event(new Registered(($user = User::create($validated))));

        Auth::login($user);

        Session::regenerate();

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
