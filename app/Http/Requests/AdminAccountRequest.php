<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Cho phép mọi admin truy cập
    }

    public function rules(): array
    {
        $id = $this->route('account'); // khi update
        return [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,' . $id,
            'password' => $this->isMethod('post') ? 'required|min:6' : 'nullable',
            'role_id' => 'required|exists:roles,id',
            'status' => 'nullable|boolean',
        ];
    }
}
