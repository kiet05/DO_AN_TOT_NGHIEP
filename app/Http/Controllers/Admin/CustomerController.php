<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;


class CustomerController extends Controller
{
    /**
     * Danh sách khách hàng (role_id = 3) + tìm kiếm + lọc trạng thái
     */
    public function index(Request $request)
    {
        $search = (string) $request->input('search');
        $status = $request->input('status'); // 'active'|'inactive'|''

        $customers = User::query()
            ->where('role_id', 3)
            ->withCount('orders')
            ->when($search, function ($q) use ($search) {
                $q->where(function ($x) use ($search) {
                    $x->where('name', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->when($status === 'active',   fn($q) => $q->where('status', 1))
            ->when($status === 'inactive', fn($q) => $q->where('status', 0))
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.index', compact('customers'));
    }

    /**
     * Form thêm khách hàng
     */
    public function create()
    {
        return view('admin.customers.create');
    }

    /**
     * Lưu khách hàng mới
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'     => 'required|string|max:120',
            'email'    => 'required|email|unique:users,email',
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'status'   => 'required|in:0,1',
            'password' => 'required|min:6|confirmed',
        ]);

        $data['role_id'] = 3; // luôn là khách hàng

        $customer = User::create($data);

        $this->log($customer->id, 'created customer', ['fields' => $data]);

        return redirect()->route('admin.customers.index')->with('success', 'Tạo khách hàng thành công');
    }

    /**
     * Chi tiết khách hàng + lịch sử đơn
     */
public function show($id)
{
    $customer = User::where('role_id', 3)->findOrFail($id);

    $orders = $customer->orders()
        ->with('orderItems')
        ->orderByDesc('id')
        ->paginate(10)
        ->withQueryString();

    // ---- Logs từ bảng notifications (nếu tồn tại) ----
    $perPage = 10;
    $page = request()->input('page_logs', 1); // tách page cho logs nếu muốn
    $logs = new LengthAwarePaginator([], 0, $perPage, $page);

    if (Schema::hasTable('notifications')) {
        $query = DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $customer->id)
            // lọc theo type riêng cho activity (xem log() bên dưới)
            ->where('type', 'activity');

        $total = (clone $query)->count();

        $rows = $query->orderByDesc('created_at')
            ->forPage($page, $perPage)
            ->get()
            ->map(function ($row) {
                $data = is_string($row->data ?? null) ? json_decode($row->data, true) : ($row->data ?? []);
                return (object) [
                    'at'   => $row->created_at,
                    'data' => $data, // ['action','ip','payload',...]
                ];
            });

        $logs = new LengthAwarePaginator($rows, $total, $perPage, $page, [
            'path' => request()->url(),
            'pageName' => 'page_logs',
        ]);
    }

    return view('admin.customers.show', compact('customer', 'orders', 'logs'));
}



    /**
     * Form sửa thông tin khách hàng
     */
    public function edit($id)
    {
        $customer = User::where('role_id', 3)->findOrFail($id);
        return view('admin.customers.edit', compact('customer'));
    }

    /**
     * Cập nhật thông tin khách hàng
     */
    public function update(Request $request, $id)
    {
        $customer = User::where('role_id', 3)->findOrFail($id);

        $data = $request->validate([
            'name'     => 'required|string|max:120',
            'email'    => 'required|email|unique:users,email,' . $customer->id,
            'phone'    => 'nullable|string|max:20',
            'address'  => 'nullable|string|max:255',
            'status'   => 'required|in:0,1',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $customer->fill($data);
        if (empty($data['password'])) {
            unset($customer->password); // không đổi mật khẩu nếu để trống
        }
        $customer->save();

        $this->log($customer->id, 'updated customer', ['fields' => $data]);

        return redirect()->route('admin.customers.index')->with('success', 'Cập nhật khách hàng thành công');
    }

    /**
     * Xóa khách hàng
     */
    public function destroy($id)
    {
        $customer = User::where('role_id', 3)->findOrFail($id);
        $customer->delete();

        $this->log($id, 'deleted customer');

        return back()->with('success', 'Đã xóa khách hàng');
    }

    /**
     * Lịch sử hoạt động của khách hàng
     */
    public function activity($id)
    {
        $customer = User::where('role_id', 3)->findOrFail($id);
        $logs = UserActivity::where('user_id', $id)->latest()->paginate(20);

        return view('admin.customers.activity', compact('customer', 'logs'));
    }

    /**
     * Khóa / Mở tài khoản khách hàng
     */
    public function toggleStatus($id)
    {
        $customer = User::where('role_id', 3)->findOrFail($id);
        $customer->status = (int)!$customer->status;
        $customer->save();

        $this->log($customer->id, $customer->status ? 'unlocked customer' : 'locked customer');

        return back()->with('success', $customer->status ? 'Đã mở khóa tài khoản' : 'Đã khóa tài khoản');
    }

    /**
     * Gửi link reset mật khẩu (qua email)
     */
    public function sendResetLink($id)
    {
        $customer = User::where('role_id', 3)->findOrFail($id);
        $status = Password::sendResetLink(['email' => $customer->email]);

        $this->log($customer->id, 'sent customer reset link', ['status' => $status]);

        return back()->with('success', __($status));
    }

    /**
     * Đặt lại mật khẩu trực tiếp (admin cưỡng bức)
     */
    public function forceReset(Request $request, $id)
    {
        $data = $request->validate([
            'password' => 'required|min:6|confirmed'
        ]);

        $customer = User::where('role_id', 3)->findOrFail($id);
        $customer->password = $data['password']; // hashed do casts
        $customer->save();

        $this->log($customer->id, 'forced customer password reset');

        return back()->with('success', 'Đã đặt lại mật khẩu');
    }

private function log(int $userId, string $action, array $payload = []): void
{
    if (!Schema::hasTable('notifications')) {
        // Không có bảng -> bỏ qua, không gây crash
        return;
    }

    $data = [
        'action'     => $action,
        'causer_id'  => Auth::id(),
        'ip'         => request()->ip(),
        'user_agent' => substr(request()->userAgent() ?? '', 0, 255),
        'payload'    => $payload ?: null,
    ];

    DB::table('notifications')->insert([
        'id'              => (string) Str::uuid(),
        'type'            => 'activity',      // đánh dấu loại activity
        'notifiable_type' => User::class,
        'notifiable_id'   => $userId,
        'data'            => json_encode($data, JSON_UNESCAPED_UNICODE),
        'read_at'         => null,
        'created_at'      => now(),
        'updated_at'      => now(),
    ]);
}


}