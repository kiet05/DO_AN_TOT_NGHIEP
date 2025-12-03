<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    /** Trang hồ sơ */
    public function index()
    {
        $user = Auth::user();

        // Lấy danh sách địa chỉ của user, địa chỉ mặc định lên trước
        $addresses = Address::where('user_id', $user->id)
            ->orderByDesc('is_default')
            ->latest()
            ->get();

        return view('frontend.profile.index', compact('user', 'addresses'));
    }

    /** Cập nhật thông tin cơ bản */
    public function update(Request $request)
    {
        $request->validate([
            'name'  => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|regex:/^0[0-9]{9,15}$/',
        ], [
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
        ]);
        /** @var \App\Models\User $user */

        $user = Auth::user();
        $user->update($request->only('name', 'phone'));

        return back()->with('success', 'Cập nhật thông tin thành công!');
    }

    /** Đổi mật khẩu */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => 'required',
            'password'         => 'required|min:8|confirmed',
        ], [
            'password.confirmed' => 'Xác nhận mật khẩu không khớp.',
            'password.min'       => 'Mật khẩu phải ít nhất 8 ký tự.',
        ]);

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->with('error', 'Mật khẩu hiện tại không đúng!');
        }
        /** @var \App\Models\User $user */

        $user->password = Hash::make($request->password);
        $user->save();

        return back()->with('success', 'Đổi mật khẩu thành công!');
    }

    /** Lưu địa chỉ mới */
    public function storeAddress(Request $request)
    {
        $user = Auth::user();

        // KHỚP TÊN FIELD VỚI FORM Ở PROFILE
        $request->validate([
            'receiver_name' => 'required|string|max:255',
            'phone'         => 'required|string|max:20|regex:/^0[0-9]{9,15}$/',
            'address_line'  => 'required|string|max:255',
            'district'      => 'required|string|max:255',
            'province'      => 'required|string|max:255',
            'is_default'    => 'nullable|boolean',
        ], [
            'phone.regex' => 'Số điện thoại không đúng định dạng.',
        ]);

        // Nếu chọn làm mặc định thì bỏ cờ mặc định ở địa chỉ khác
        if ($request->boolean('is_default')) {
            Address::where('user_id', $user->id)->update(['is_default' => false]);
        }

        // DÙNG CÁC ALIAS receiver_* ĐỂ MODEL TỰ MAP SANG phone/address_line/district/province
        Address::create([
            'user_id'                => $user->id,
            'receiver_name'          => $request->receiver_name,
            'receiver_phone'         => $request->phone,          // -> phone
            'receiver_address_detail' => $request->address_line,   // -> address_line
            'receiver_district'      => $request->district,       // -> district
            'receiver_city'          => $request->province,       // -> province
            'is_default'             => $request->boolean('is_default'),
        ]);

        return back()->with('success', 'Đã thêm địa chỉ giao hàng.');
    }



    /** Đặt một địa chỉ làm mặc định */
    public function setDefaultAddress(Address $address)
    {
        $user = Auth::user();

        // Chỉ cho phép chỉnh địa chỉ của chính user
        if ($address->user_id !== $user->id) {
            abort(403);
        }

        Address::where('user_id', $user->id)->update(['is_default' => false]);

        $address->is_default = true;
        $address->save();

        return back()->with('success', 'Đã đặt địa chỉ mặc định.');
    }

    /** Xoá địa chỉ */
    public function destroyAddress(Address $address)
    {
        $user = Auth::user();

        if ($address->user_id !== $user->id) {
            abort(403);
        }

        $address->delete();

        return back()->with('success', 'Đã xoá địa chỉ.');
    }
    /** Form sửa địa chỉ */
    public function editAddress(Address $address)
    {
        $user = Auth::user();

        // Chỉ được sửa địa chỉ của chính mình
        if ($address->user_id !== $user->id) {
            abort(403);
        }

        return view('frontend.profile.address-edit', compact('user', 'address'));
    }

    /** Cập nhật địa chỉ */
    public function updateAddress(Request $request, Address $address)
    {
        $user = Auth::user();

        // Chỉ cho sửa địa chỉ của chính mình
        if ($address->user_id !== $user->id) {
            abort(403);
        }

        $request->validate([
            'receiver_name'           => 'required|string|max:255',
            'receiver_phone'          => 'required|string|max:20|regex:/^0[0-9]{9,15}$/',
            'receiver_address_detail' => 'required|string|max:255',
            'receiver_district'       => 'required|string|max:255',
            'receiver_city'           => 'required|string|max:255',
            // is_default gửi lên dạng checkbox (on / null) nên không cần validate boolean nữa
        ], [
            'receiver_phone.regex' => 'Số điện thoại không đúng định dạng.',
        ]);

        // ✅ Xử lý checkbox: chỉ cần kiểm tra có key hay không
        $isDefault = $request->has('is_default');

        // Nếu tick làm mặc định thì clear các địa chỉ khác
        if ($isDefault) {
            Address::where('user_id', $user->id)
                ->where('id', '!=', $address->id)
                ->update(['is_default' => false]);
        }

        // Cập nhật thủ công từng field (để chắc chắn dùng mutator trong model Address)
        $address->receiver_name           = $request->receiver_name;
        $address->receiver_phone          = $request->receiver_phone;
        $address->receiver_address_detail = $request->receiver_address_detail;
        $address->receiver_district       = $request->receiver_district;
        $address->receiver_city           = $request->receiver_city;
        $address->is_default              = $isDefault;
        $address->save();

        return redirect()
            ->route('profile.edit')
            ->with('success', 'Cập nhật địa chỉ thành công.');
    }
}
