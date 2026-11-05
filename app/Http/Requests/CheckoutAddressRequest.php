<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Set/ubah alamat langsung dari halaman checkout.
 */
class CheckoutAddressRequest extends FormRequest
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
            'address' => 'required|string|max:255',
            'destination_name' => 'required|string|max:255',
            'destination_id' => 'required|string|max:50',
        ];
    }
}
