<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;


class UserController extends Controller
{
    public function saveAddress(Request $request)
    {
        // Validasi input
        $request->validate([
            'recipient_name' => 'required|string',
            'recipient_phone' => 'required|string',
            'address' => 'required|string',
            'city_name' => 'required|string',
            'province_name' => 'required|string',
        ]);

        // Pastikan user terautentikasi
        $user = auth()->user();
        if (!$user) {
            return response()->json(['error' => 'User is not authenticated'], 401);
        }

        // Ambil data provinsi dan kota
        $provinceName = $request->province_name;
        $cityName = $request->city_name;

        // Cari ID Kota
        $cityId = $this->getCityId($provinceName, $cityName);

        if (!$cityId) {
            return response()->json(['error' => 'City not found'], 404);
        }

        // Cari ID Provinsi
        $provinceId = $this->getProvinceId($provinceName);
        if (!$provinceId) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        // Gabungkan detail alamat, termasuk city_id dan province_id
        $fullAddress = sprintf(
            "Nama Penerima: %s, No. Telepon: %s, Alamat: %s, Kota: %s, Provinsi: %s, City ID: %s, Province ID: %s",
            $request->recipient_name,
            $request->recipient_phone,
            $request->address,
            $cityName,
            $provinceName,
            $cityId, // Menyertakan city_id
            $provinceId // Menyertakan province_id
        );

        // Simpan alamat (termasuk city_id dan province_id) ke database
        $user->address = $fullAddress; // Simpan dalam kolom address
        $user->save();

        return response()->json([
            'success' => true,
            'address' => $user->address,
            'city_id' => $cityId, // Mengembalikan city_id
            'province_id' => $provinceId, // Mengembalikan province_id
        ]);
    }

    public function getCitiesByProvince(Request $request)
    {
        $provinceName = $request->get('province_name');

        // Cari ID provinsi
        $provinceId = $this->getProvinceId($provinceName);

        if (!$provinceId) {
            return response()->json(['error' => 'Province not found'], 404);
        }

        // Ambil daftar kota berdasarkan ID provinsi
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'), // Ganti dengan API Key RajaOngkir Anda
        ])->get('https://api.rajaongkir.com/starter/city', [
                    'province' => $provinceId, // Gunakan ID provinsi
                ]);

        if ($response->successful()) {
            $cities = $response->json()['rajaongkir']['results'];

            // Kirimkan data kota ke front-end
            return response()->json(['success' => true, 'cities' => $cities]);
        }

        return response()->json(['error' => 'Failed to retrieve city data'], 500);
    }

    /**
     * Fungsi untuk mendapatkan ID kota berdasarkan nama provinsi dan kota.
     */
    private function getCityId($provinceName, $cityName)
    {
        // Ambil ID provinsi berdasarkan nama
        $provinceId = $this->getProvinceId($provinceName);

        if (!$provinceId) {
            return null; // Jika provinsi tidak ditemukan
        }

        // Ambil daftar kota berdasarkan ID provinsi
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'), // Ganti dengan API Key RajaOngkir Anda
        ])->get('https://api.rajaongkir.com/starter/city', [
                    'province' => $provinceId, // Gunakan ID provinsi
                ]);

        if ($response->successful()) {
            $cities = $response->json()['rajaongkir']['results'];
            foreach ($cities as $city) {
                if (strtolower($city['city_name']) == strtolower($cityName)) {
                    return $city['city_id'];
                }
            }
        }

        return null; // Jika kota tidak ditemukan
    }

    /**
     * Fungsi untuk mendapatkan ID provinsi berdasarkan nama provinsi.
     */
    private function getProvinceId($provinceName)
    {
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'), // Ganti dengan API Key RajaOngkir Anda
        ])->get('https://api.rajaongkir.com/starter/province');

        if ($response->successful()) {
            $provinces = $response->json()['rajaongkir']['results'];
            foreach ($provinces as $province) {
                if (strtolower($province['province']) == strtolower($provinceName)) {
                    return $province['province_id'];
                }
            }
        }

        return null; // Jika provinsi tidak ditemukan
    }

    public function updateAddress(Request $request)
    {
        // Validasi input
        $request->validate([
            'recipient_name' => 'required|string',
            'recipient_phone' => 'required|string',
            'address' => 'required|string',
            'city_name' => 'required|string',
            'province_name' => 'required|string',
        ]);


        // Ambil ID Kota dan Provinsi berdasarkan nama yang dikirim
        $provinceName = $request->province_name;
        $cityName = $request->city_name;
        $cityId = $this->getCityId($provinceName, $cityName);
        $provinceId = $this->getProvinceId($provinceName);

        // Ambil data pengguna yang sedang login
        $user = auth()->user();
        // Gabungkan detail alamat
        $fullAddress = sprintf(
            "Nama Penerima: %s, No. Telepon: %s, Alamat: %s, Kota: %s, Provinsi: %s, City ID: %s, Province ID: %s",
            $request->recipient_name,
            $request->recipient_phone,
            $request->address,
            $cityName,
            $provinceName,
            $cityId,
            $provinceId
        );

        // Simpan alamat yang telah diperbarui
        $user->address = $fullAddress;
        $user->save();

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'Address updated successfully']);
        }

        // Mengarahkan kembali dengan alert
        return redirect()->back()->with('success', 'Address updated successfully');
    }

    public function deleteAddress(Request $request)
    {
        $user = auth()->user();

        if ($user->address) {
            $user->address = null; // Hapus alamat
            $user->save();

            return response()->json(['success' => true, 'message' => 'Address deleted successfully.']);
        }

        return response()->json(['success' => false, 'message' => 'Address not found.']);
    }

    public function editUser(Request $request)
    {
        // Validasi input tanpa password
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:15',
            'email' => 'required|email|max:255',
        ]);

        // Pastikan user terautentikasi
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login')->withErrors(['error' => 'User is not authenticated']);
        }

        // Jika ada data yang ingin diubah, perbarui
        if ($request->has('name')) {
            $user->name = $request->name;
        }

        if ($request->has('phone')) {
            $user->phone = $request->phone;
        }

        if ($request->has('email')) {
            $user->email = $request->email;
        }

        // Simpan perubahan data user
        $user->save();

        // Redirect ke halaman profil dengan pesan sukses
        return redirect()->route('user.edit')->with('success', 'User data has been updated successfully.');
    }


    public function showEditForm()
    {
        return view('pages.user.profile.index');
    }

    public function showEditAddress()
    {
        // Ambil data alamat dari pengguna yang sedang login
        $user = auth()->user();
        $address = $user->address;

        // Gunakan regex untuk mengurai string alamat
        preg_match(
            '/Nama Penerima: (.*?), No. Telepon: (.*?), Alamat: (.*?), Kota: (.*?), Provinsi: (.*?), City ID: (.*?), Province ID: (.*?)/',
            $address,
            $matches
        );

        // Simpan bagian-bagian yang relevan ke dalam variabel
        $recipientName = $matches[1] ?? '';
        $recipientPhone = $matches[2] ?? '';
        $streetAddress = $matches[3] ?? '';
        $city = $matches[4] ?? '';
        $province = $matches[5] ?? '';
        $cityId = $matches[6] ?? '';
        $provinceId = $matches[7] ?? '';

        // Ambil daftar provinsi
        $response = Http::withHeaders([
            'key' => env('RAJAONGKIR_API_KEY'), // Ganti dengan API Key RajaOngkir Anda
        ])->get('https://api.rajaongkir.com/starter/province');

        $provinces = [];
        if ($response->successful()) {
            $provinces = $response->json()['rajaongkir']['results'];
        }

        // Ambil daftar kota berdasarkan provinsi yang dipilih
        $cities = [];
        if ($province) {
            $provinceId = $this->getProvinceId($province);
            if ($provinceId) {
                $response = Http::withHeaders([
                    'key' => env('RAJAONGKIR_API_KEY'), // Ganti dengan API Key RajaOngkir Anda
                ])->get('https://api.rajaongkir.com/starter/city', [
                            'province' => $provinceId,
                        ]);

                if ($response->successful()) {
                    $cities = $response->json()['rajaongkir']['results'];
                }
            }
        }


        // Kirimkan data ke view
        return view('pages.user.address.index', compact('recipientName', 'recipientPhone', 'streetAddress', 'city', 'province', 'cityId', 'provinceId', 'provinces', 'cities'));
    }


}
