<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;  // Tambahkan ini

class RajaOngkirController extends Controller
{
    /**
     * Menyediakan layanan pencarian destinasi pengiriman
     */
    public function searchDestinations(Request $request)
    {
        try {
            $term = $request->term ?? '';
            $apiKey = config('rajaongkir.api_key');

            if (empty($apiKey)) {
                return response()->json(['error' => 'RajaOngkir API key belum dikonfigurasi.'], 500);
            }

            $response = Http::withHeaders([
                'key' => $apiKey
            ])->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
                'search' => $term,
                'limit' => 20,
                'offset' => 0
            ]);

            if (!$response->successful()) {
                Log::warning('RajaOngkir searchDestinations failed', [
                    'status' => $response->status(),
                    'error' => $response->json('message') ?? 'API request failed'
                ]);
                return response()->json([
                    'error' => 'API request failed',
                    'message' => 'Gagal mencari destinasi pengiriman.'
                ], 500);
            }

            $results = [];
            $data = $response->json();

            if (!empty($data['data'])) {
                foreach ($data['data'] as $item) {
                    $results[] = [
                        'id' => $item['id'],
                        'label' => $item['label'],
                        'value' => $item['label'],
                    ];
                }
            }

            return response()->json($results);

        } catch (\Exception $e) {
            Log::error('Error in searchDestinations: ' . $e->getMessage());
            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Terjadi kesalahan sistem saat mencari destinasi.'
            ], 500);
        }
    }

    /**
     * Menyediakan layanan cek ongkir
     */
    public function cekOngkir(Request $request)
    {
        try {
            // Validasi input
            $request->validate([
                'courier' => 'required|string',
                'destination' => 'required|string',
                'weight' => 'required|integer',
            ]);

            $apiKey = config('rajaongkir.api_key');
            if (empty($apiKey)) {
                return response()->json(['error' => 'RajaOngkir API key belum dikonfigurasi.'], 500);
            }

            $origin = config('rajaongkir.origin', 17549);

            $response = Http::withHeaders([
                'key' => $apiKey
            ])->asForm()->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin' => $origin,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier
            ]);

            if (!$response->successful()) {
                Log::warning('RajaOngkir cekOngkir failed', [
                    'status' => $response->status(),
                    'error' => $response->json('message') ?? 'API request failed'
                ]);
                return response()->json([
                    'error' => 'API request failed',
                    'message' => 'Gagal menghitung ongkos kirim.'
                ], 500);
            }

            // Mengambil hasil response dan memformat hasil ongkir
            $ongkir = $response->json();

            $services = [];
            if (isset($ongkir['data'])) {
                foreach ($ongkir['data'] as $service) {
                    $services[] = [
                        'service' => $service['service'],
                        'description' => $service['description'],
                        'cost' => number_format($service['cost'], 0, ',', '.'),
                        'etd' => $service['etd'],
                    ];
                }
            }

            return response()->json([
                'services' => $services
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'error' => 'Validation failed',
                'messages' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error in cekOngkir: ' . $e->getMessage());
            return response()->json([
                'error' => 'Internal server error',
                'message' => 'Terjadi kesalahan sistem saat mengecek ongkir.'
            ], 500);
        }
    }
}