<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sudah dilindungi middleware auth
    }

    public function rules(): array
    {
        // Dipakai add (size opsional) & update kuantitas; size extra saat update diabaikan
        return [
            'size' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
        ];
    }
}
