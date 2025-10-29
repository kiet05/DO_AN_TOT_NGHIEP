<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // 🧾 Hiển thị danh sách thông báo
    public function index()
    {
        $notifications = Notification::latest()->get();
        return view('admin.notifications.index', compact('notifications'));
    }

    // 📝 Hiển thị form tạo thông báo
    public function create()
    {
        return view('admin.notifications.create');
    }

    // 💾 Lưu thông báo mới
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'nullable|boolean',
        ]);

        Notification::create([
            'title'      => $validated['title'],
            'content'    => $validated['content'],
            'status'     => $validated['status'] ?? 1,
            'created_by' => Auth::id(),
        ]);

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Thêm thông báo thành công!');
    }

    // ✏️ Hiển thị form sửa thông báo
    public function edit(Notification $notification)
    {
        return view('admin.notifications.edit', compact('notification'));
    }

    // 🔁 Cập nhật thông báo
    public function update(Request $request, Notification $notification)
    {
        $validated = $request->validate([
            'title'   => 'required|string|max:255',
            'content' => 'required|string',
            'status'  => 'nullable|boolean',
        ]);

        $notification->update([
            'title'   => $validated['title'],
            'content' => $validated['content'],
            'status'  => $validated['status'] ?? 1,
        ]);

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Cập nhật thông báo thành công!');
    }

    // 🗑️ Xóa thông báo
    public function destroy(Notification $notification)
    {
        $notification->delete();

        return redirect()
            ->route('admin.notifications.index')
            ->with('success', 'Xóa thông báo thành công!');
    }
}
