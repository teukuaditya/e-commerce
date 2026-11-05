<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Update alamat (address edit). Destination ID boleh kosong —
 * dipertahankan dari alamat lama (perilaku asli).
 */
class AddressUpdateRequest extends FormRequest
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
        ];
    }
}
