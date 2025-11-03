<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class BannerController extends Controller
{
    /**
     * Danh sách banner + lọc theo status: active | all | trash
     */
    public function index(Request $request)
    {
        // status: all | active | inactive | trash
        $status = $request->query('status', 'active');

        $base = Banner::query(); // mặc định đã loại thùng rác
        if ($status === 'trash') {
            $query = Banner::onlyTrashed();
        } elseif ($status === 'active') {
            $query = (clone $base)->where('status', 1);
        } elseif ($status === 'inactive') {
            $query = (clone $base)->where('status', 0);
        } else { // 'all'
            $query = $base;
        }

        // ⬇️ Sắp xếp ID tăng dần (nhỏ -> lớn)
        $banners = $query->orderBy('id', 'asc')->get();

        // Đếm để hiển thị badge trên các nút
        $countAll      = Banner::count();                 // không gồm rác
        $countActive   = Banner::where('status', 1)->count();
        $countInactive = Banner::where('status', 0)->count();
        $countTrash    = Banner::onlyTrashed()->count();

        return view('admin.banners.index', compact(
            'banners',
            'status',
            'countAll',
            'countActive',
            'countInactive',
            'countTrash'
        ));
    }

    public function create(): View
    {
        return view('admin.banners.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'title'  => 'required|string|max:255',
            'image'  => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
            'status' => 'nullable',
        ]);

        $data = [
            'title'  => $request->input('title'),
            'status' => $request->boolean('status') ? 1 : 0, // ép 0/1 chắc chắn
            'image'  => $request->file('image')->store('banners', 'public'),
        ];

        Banner::create($data);

        return redirect()->route('admin.banners.index', ['status' => 'active'])
            ->with('success', 'Thêm banner thành công!');
    }

    public function edit(Banner $banner): View
    {
        return view('admin.banners.edit', compact('banner'));
    }

    // UPDATE
    public function update(Request $request, Banner $banner)
    {
        $request->validate([
            'title'  => 'required|string|max:255',
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:4096',
            'status' => 'nullable',
        ]);

        $data = [
            'title'  => $request->input('title'),
            'status' => $request->boolean('status') ? 1 : 0,
        ];

        if ($request->hasFile('image')) {
            $new = $request->file('image')->store('banners', 'public');
            if (!empty($banner->image) && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }
            $data['image'] = $new;
        }

        $banner->update($data);

        return redirect()->route('admin.banners.index', ['status' => 'active'])
            ->with('success', 'Cập nhật banner thành công!');
    }

    /**
     * Xóa mềm (chuyển vào thùng rác)
     */
    public function destroy($id): RedirectResponse
    {
        $banner = Banner::findOrFail($id);
        $banner->delete();

        return back()->with('success', 'Đã chuyển banner vào thùng rác.');
    }

    /**
     * Khôi phục từ thùng rác
     */
    public function restore($id)
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);
        $banner->restore();
        return back()->with('success', 'Đã khôi phục banner.');
    }

    public function forceDelete($id)
    {
        $banner = Banner::onlyTrashed()->findOrFail($id);
        if (!empty($banner->image) && Storage::disk('public')->exists($banner->image)) {
            Storage::disk('public')->delete($banner->image);
        }
        $banner->forceDelete();
        return back()->with('success', 'Đã xóa vĩnh viễn banner.');
    }
}
