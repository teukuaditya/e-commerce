<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * CRUD user pada panel admin.
 * Satu class untuk store (password wajib) & update (password opsional);
 * aturan unik meng-abaikan milik user yang sedang di-update.
 */
class AdminUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sudah dilindungi middleware auth + admin
    }

    public function rules(): array
    {
        // Ambil ID model dari route binding (WAJIB ->getKey(), bukan objeknya!
        // Concat objek model menghasilkan JSON-nya (bisa berisi koma) dan
        // memecah rule 'unique:...' jadi parameter acak -> Undefined array key.)
        $userId = $this->route('user')?->getKey() ?? $this->route('id');

        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users', 'email')->ignore($userId)],
            'phone' => 'nullable|string|max:15',
            'role' => 'required|in:user,admin',
            // Password wajib untuk tambah user; opsional saat edit
            'password' => $this->isMethod('POST') ? 'required|string|min:8' : 'nullable|string|min:8',
        ];
    }
}
