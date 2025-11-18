<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Faq;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    // Hiển thị trang Liên hệ & Hỗ trợ
    public function index()
    {
        $faqs = Faq::where('is_active', true)
            ->orderBy('category')
            ->orderBy('sort_order')
            ->get();

        return view('frontend.contact.contact', compact('faqs'));
    }

    // Xử lý form gửi yêu cầu hỗ trợ
    public function store(Request $request)
    {
        $data = $request->validate(
            [
                'name'    => 'required|string|max:255',
                'email'   => 'required|email|max:255',
                'phone'   => 'nullable|string|max:20',
                'subject' => 'nullable|string|max:255',
                'message' => 'required|string|min:10',
            ],
            [
                'name.required'    => 'Vui lòng nhập họ tên.',
                'email.required'   => 'Vui lòng nhập email.',
                'email.email'      => 'Email không hợp lệ.',
                'message.required' => 'Vui lòng nhập nội dung liên hệ.',
                'message.min'      => 'Nội dung liên hệ tối thiểu 10 ký tự.',
            ]
        );

        Contact::create($data);

        return redirect()
            ->route('contact.index')
            ->with('success', 'Gửi yêu cầu hỗ trợ thành công, chúng tôi sẽ phản hồi sớm.');
    }
}
