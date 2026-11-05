<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Callback sukses pembayaran Midtrans dari client.
 */
class PaymentCallbackRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // sudah dilindungi middleware auth
    }

    public function rules(): array
    {
        return [
            'order_id' => 'required|string',
            'payment_type' => 'nullable|string|max:50',
        ];
    }
}
