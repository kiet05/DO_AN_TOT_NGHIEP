<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ShopSettingController extends Controller
{
    /**
     * Show the form for editing shop settings
     */
    public function edit()
    {
        $setting = ShopSetting::first();
        
        // Nếu chưa có setting, tạo mới
        if (!$setting) {
            $setting = ShopSetting::create([
                'logo' => null,
                'hotline' => null,
                'email' => null,
                'address' => null,
                'facebook' => null,
                'instagram' => null,
                'zalo' => null,
                'tiktok' => null,
                'youtube' => null,
                'twitter' => null,
            ]);
        }

        return view('admin.shop-settings.edit', compact('setting'));
    }

    /**
     * Update shop settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'hotline' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'address' => 'nullable|string',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'zalo' => 'nullable|string|max:255',
            'tiktok' => 'nullable|url|max:255',
            'youtube' => 'nullable|url|max:255',
            'twitter' => 'nullable|url|max:255',
        ]);

        $setting = ShopSetting::first();
        
        if (!$setting) {
            $setting = new ShopSetting();
        }

        $data = [
            'hotline' => $request->input('hotline'),
            'email' => $request->input('email'),
            'address' => $request->input('address'),
            'facebook' => $request->input('facebook'),
            'instagram' => $request->input('instagram'),
            'zalo' => $request->input('zalo'),
            'tiktok' => $request->input('tiktok'),
            'youtube' => $request->input('youtube'),
            'twitter' => $request->input('twitter'),
        ];

        // Xử lý upload logo
        if ($request->hasFile('logo')) {
            // Xóa logo cũ nếu có
            if ($setting->logo && Storage::disk('public')->exists($setting->logo)) {
                Storage::disk('public')->delete($setting->logo);
            }
            
            // Lưu logo mới
            $data['logo'] = $request->file('logo')->store('shop', 'public');
        }

        $setting->fill($data);
        $setting->save();

        return redirect()
            ->route('admin.shop-settings.edit')
            ->with('success', 'Cập nhật thông tin shop thành công!');
    }

}

