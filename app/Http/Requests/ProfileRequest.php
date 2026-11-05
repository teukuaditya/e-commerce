<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update profil user yang sedang login.
 */
class ProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sudah dilindungi middleware auth
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            // Email harus unik (kecuali milik sendiri)
            'email' => 'required|email|max:255|unique:users,email,'.auth()->id(),
        ];
    }
}
