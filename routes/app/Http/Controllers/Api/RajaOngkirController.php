<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class RajaOngkirController extends Controller
{
    private $apiKey;

    public function __construct()
    {
        $this->apiKey = env('RAJAONGKIR_API_KEY'); // Ambil API Key dari .env
    }

    public function provinces()
    {
        $client = new Client();
        $response = $client->request('GET', 'https://api.rajaongkir.com/starter/province', [
            'headers' => [
                'key' => env('RAJAONGKIR_API_KEY'),
            ],
        ]);

        $provinces = json_decode($response->getBody()->getContents());
        return response()->json($provinces);
    }

    public function cities(int $provinceId)
    {
        $client = new Client();
        $response = $client->request('GET', 'https://api.rajaongkir.com/starter/city?province=' . $provinceId, [
            'headers' => [
                'key' => env('RAJAONGKIR_API_KEY'),
            ],
        ]);

        $cities = json_decode($response->getBody()->getContents());
        return response()->json($cities);
    }

    /**
     * Menyediakan layanan pengiriman berdasarkan input origin, destination, weight, dan courier.
     */
    public function cekOngkir(Request $request)
    {
        // Validasi input
        $request->validate([
            'courier' => 'required|string',
            'destination' => 'required|integer',
            'weight' => 'required|integer',
        ]);

        $origin = 153; // ID Kota Jakarta (ubah sesuai kebutuhan)
        $courier = $request->courier;
        $destination = $request->destination;
        $weight = $request->weight;

        // Menggunakan Guzzle untuk memanggil API RajaOngkir
        $client = new Client();
        $response = $client->request('POST', 'https://api.rajaongkir.com/starter/cost', [
            'headers' => [
                'key' => env('RAJAONGKIR_API_KEY'),
            ],
            'form_params' => [
                'origin' => $origin,
                'destination' => $destination,
                'weight' => $weight,
                'courier' => $courier,
            ]
        ]);

        // Mengambil hasil response dan memformat hasil ongkir
        $ongkir = json_decode($response->getBody()->getContents());

        $services = [];
        if (isset($ongkir->rajaongkir->results)) {
            foreach ($ongkir->rajaongkir->results as $result) {
                foreach ($result->costs as $cost) {
                    $services[] = [
                        'service' => $cost->service,
                        'description' => $cost->description,
                        'cost' => number_format($cost->cost[0]->value, 0, ',', '.'),
                        'etd' => $cost->cost[0]->etd, // Estimasi waktu pengiriman
                    ];
                }
            }
        }

        return response()->json([
            'services' => $services
        ]);
    }
}
