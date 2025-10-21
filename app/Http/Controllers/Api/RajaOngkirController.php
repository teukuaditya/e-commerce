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
            
            Log::info('Search term: ' . $term);
            Log::info('API Key exists: ' . (env('RAJAONGKIR_API_KEY') ? 'Yes' : 'No'));

            $response = Http::withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])->get('https://rajaongkir.komerce.id/api/v1/destination/domestic-destination', [
                'search' => $term,
                'limit' => 20,
                'offset' => 0
            ]);

            Log::info('API Response Status: ' . $response->status());
            Log::info('API Response Body: ' . $response->body());

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'API request failed',
                    'status' => $response->status(),
                    'message' => $response->body()
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
                'message' => $e->getMessage()
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

            Log::info('Cek Ongkir Request:', [
                'courier' => $request->courier,
                'destination' => $request->destination,
                'weight' => $request->weight
            ]);

            $response = Http::withHeaders([
                'key' => env('RAJAONGKIR_API_KEY')
            ])->asForm()->post('https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost', [
                'origin' => 17549,
                'destination' => $request->destination,
                'weight' => $request->weight,
                'courier' => $request->courier
            ]);

            Log::info('API Response Status: ' . $response->status());

            if (!$response->successful()) {
                return response()->json([
                    'error' => 'API request failed',
                    'status' => $response->status(),
                    'message' => $response->body()
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
                'message' => $e->getMessage()
            ], 500);
        }
    }
}