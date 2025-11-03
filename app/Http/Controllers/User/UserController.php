<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class UserController extends Controller
{
    /**
     * Save new address
     */
    public function saveAddress(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string|max:255',
            'recipient_phone' => 'required|string|max:20',
            'address' => 'required|string',
            'destination_name' => 'required|string',
            'destination_id' => 'required'
        ]);

        $user = auth()->user();
        
        $fullAddress = sprintf(
            "Nama Penerima: %s, No. Telepon: %s, Alamat: %s, Destination: %s, Destination ID: %s",
            $request->recipient_name,
            $request->recipient_phone,
            $request->address,
            $request->destination_name,
            $request->destination_id
        );
        
        $user->address = $fullAddress;
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
        $user = auth()->user();
        $address = $user->address;

        preg_match(
            '/Nama Penerima: (.*?), No\. Telepon: (.*?), Alamat: (.*?), Destination: (.*?), Destination ID: (.*)/',
            $address,
            $matches
        );

        return view('pages.user.address.index', [
            'recipientName' => $matches[1] ?? '',
            'recipientPhone' => $matches[2] ?? '',
            'streetAddress' => $matches[3] ?? '',
            'destinationName' => $matches[4] ?? '',
            'destinationId' => $matches[5] ?? '',
        ]);
    }

    /**
     * Update existing address
     */
    public function updateAddress(Request $request)
    {
        $request->validate([
            'recipient_name' => 'required|string',
            'recipient_phone' => 'required|string',
            'address' => 'required|string',
            'destination_name' => 'required|string',
            'destination_id' => 'nullable|string',
        ]);

        $destinationId = $request->destination_id;

        if (!$destinationId) {
            preg_match('/Destination ID: (.*?)(,|$)/', auth()->user()->address, $match);
            $destinationId = trim($match[1] ?? '');
        }

        $user = auth()->user();

        $fullAddress = sprintf(
            "Nama Penerima: %s, No. Telepon: %s, Alamat: %s, Destination: %s, Destination ID: %s",
            $request->recipient_name,
            $request->recipient_phone,
            $request->address,
            $request->destination_name,
            $destinationId
        );

        $user->address = $fullAddress;
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
    public function editUser(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'required|email|max:255',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('user.profile.update')->with('success', 'User data has been updated successfully.');
    }

    /**
     * Show edit user profile form
     */
    public function showEditForm()
    {
        return view('pages.user.profile.index');
    }
}