<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Simpan alamat baru (checkout & address:create).
 * destination_id wajib (dipilih dari autocomplete).
 */
class AddressSaveRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sudah dilindungi middleware auth
    }

    public function rules(): array
    {
        return [
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'destination_name' => 'required|string',
            'destination_id' => 'required|string|max:50',
        ];
    }
}
