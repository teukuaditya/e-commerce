<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\AddressSaveRequest;
use App\Http\Requests\AddressUpdateRequest;
use App\Http\Requests\ProfileRequest;
use App\Support\AddressFormatter;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Save new address
     */
    public function saveAddress(AddressSaveRequest $request)
    {
        $user = auth()->user();

        // Format alamat diseragamkan lewat AddressFormatter (temuan audit T1)
        $user->address = AddressFormatter::format($request->only([
            'recipient_name', 'recipient_phone', 'address', 'destination_name', 'destination_id',
        ]));
        $user->save();

        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Address saved successfully.');
    }

    /**
     * Show edit address form
     */
    public function showEditAddress()
    {
        $parsed = AddressFormatter::parse(auth()->user()->address);

        return view('pages.user.address.index', [
            'recipientName' => $parsed['recipient_name'] ?? '',
            'recipientPhone' => $parsed['recipient_phone'] ?? '',
            'streetAddress' => $parsed['street_address'] ?? '',
            'destinationName' => $parsed['destination_name'] ?? '',
            'destinationId' => $parsed['destination_id'] ?? '',
        ]);
    }

    /**
     * Update existing address
     */
    public function updateAddress(AddressUpdateRequest $request)
    {
        // Destination ID boleh kosong — dipertahankan dari alamat lama
        $destinationId = $request->input('destination_id');
        if (! $destinationId) {
            $old = AddressFormatter::parse(auth()->user()->address);
            $destinationId = $old['destination_id'] ?? '';
        }

        $user = auth()->user();

        $user->address = AddressFormatter::format([
            'recipient_name' => $request->input('recipient_name'),
            'recipient_phone' => $request->input('recipient_phone'),
            'address' => $request->input('address'),
            'destination_name' => $request->input('destination_name'),
            'destination_id' => $destinationId,
        ]);
        $user->save();

        // Cek jika request AJAX
        if ($request->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()->back()->with('success', 'Address updated successfully.');
    }

    /**
     * Delete address
     */
    public function deleteAddress(Request $request)
    {
        $user = auth()->user();

        if ($user->address) {
            $user->address = null;
            $user->save();
            if ($request->ajax()) {
                return response()->json(['success' => true]);
            }

            return redirect()->back()->with('success', 'Address deleted successfully.');
        }

        if ($request->ajax()) {
            return response()->json(['success' => false, 'message' => 'Address not found.']);
        }

        return redirect()->back()->with('error', 'Address not found.');
    }

    /**
     * Update user profile
     */
    public function editUser(ProfileRequest $request)
    {
        $user = auth()->user();

        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('user.profile.update.get')->with('success', 'User data has been updated successfully.');
    }

    /**
     * Show edit user profile form
     */
    public function showEditForm()
    {
        return view('pages.user.profile.index');
    }
}
