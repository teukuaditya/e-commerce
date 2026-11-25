<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Setting;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RajaOngkirController extends Controller
{
    private const DEST_URL = 'https://rajaongkir.komerce.id/api/v1/destination/domestic-destination';
    private const COST_URL = 'https://rajaongkir.komerce.id/api/v1/calculate/domestic-cost';

    /** Kunci cache untuk indeks destinasi lokal (dipakai agar autocomplete hemat kuota). */
    private const INDEX_KEY = 'rajaongkir.destination.index';

    /**
     * IP Cloudflare RajaOngkir yang sudah diketahui. Dipakai lewat CURLOPT_RESOLVE
     * untuk MEM-BYPASS DNS lokal WSL yang lambat (penyebab utama aplikasi "berat").
     * Jika IP ini gagal connect, otomatis fallback ke DNS normal.
     */
    private const RESOLVE_IP = '104.26.4.205';

    /**
     * Seed indeks destinasi (ID terverifikasi dari API). Berguna supaya autocomplete
     * per-huruf tetap berfungsi walau kuota API harian habis / offline. Indeks ini
     * akan terus bertambah otomatis setiap kali pencarian API berhasil.
     */
    private const SEED = [
        ['id' => 1520,  'label' => 'JAKARTA SELATAN, DKI JAKARTA'],
        ['id' => 17473, 'label' => 'GROGOL, GROGOL PETAMBURAN, JAKARTA BARAT, DKI JAKARTA, 11450'],
        ['id' => 17474, 'label' => 'JELAMBAR, GROGOL PETAMBURAN, JAKARTA BARAT, DKI JAKARTA, 11460'],
        ['id' => 17475, 'label' => 'JELAMBAR BARU, GROGOL PETAMBURAN, JAKARTA BARAT, DKI JAKARTA, 11460'],
        ['id' => 17476, 'label' => 'TANJUNG DUREN SELATAN, GROGOL PETAMBURAN, JAKARTA BARAT, DKI JAKARTA, 11470'],
        ['id' => 17477, 'label' => 'TANJUNG DUREN UTARA, GROGOL PETAMBURAN, JAKARTA BARAT, DKI JAKARTA, 11470'],
        ['id' => 17150, 'label' => 'DEPOK, DEPOK, CIREBON, JAWA BARAT, 45155'],
        ['id' => 25986, 'label' => 'DEPOK, PANCORAN MAS, DEPOK, JAWA BARAT, 16431'],
    ];

    /**
     * Kirim request ke RajaOngkir dengan cepat (bypass DNS) + fallback aman.
     */
    private function send(string $method, string $url, array $payload = [], bool $form = false)
    {
        $apiKey = config('rajaongkir.api_key');

        $make = function (array $curlOpts) use ($apiKey) {
            return Http::withOptions(['curl' => $curlOpts])
                ->withHeaders(['key' => $apiKey])
                ->timeout(25)
                ->connectTimeout(12);
        };

        $fast = [
            CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4,
            CURLOPT_RESOLVE => ['rajaongkir.komerce.id:443:'.self::RESOLVE_IP],
        ];
        $slow = [CURLOPT_IPRESOLVE => CURL_IPRESOLVE_V4];

        try {
            $req = $make($fast);

            return $form ? $req->asForm()->post($url, $payload) : $req->get($url, $payload);
        } catch (ConnectionException $e) {
            // IP pin gagal (mis. IP berubah) → fallback DNS normal (lebih lambat tapi jalan)
            Log::warning('RajaOngkir fast-resolve gagal, fallback DNS: '.$e->getMessage());
            $req = $make($slow);

            return $form ? $req->asForm()->post($url, $payload) : $req->get($url, $payload);
        }
    }

    /** Muat indeks destinasi lokal (seed bila kosong). */
    private function loadIndex(): array
    {
        $index = Cache::get(self::INDEX_KEY);
        if (! is_array($index) || empty($index)) {
            $index = self::SEED;
            Cache::put(self::INDEX_KEY, $index, now()->addDays(7));
        }

        return $index;
    }

    /** Gabungkan hasil baru ke indeks lokal (unik berdasarkan id). */
    private function mergeIndex(array $results): void
    {
        $index = $this->loadIndex();
        $seen = array_column($index, 'id');
        foreach ($results as $r) {
            if (! in_array($r['id'], $seen, true)) {
                $index[] = ['id' => $r['id'], 'label' => $r['label']];
                $seen[] = $r['id'];
            }
        }
        Cache::put(self::INDEX_KEY, $index, now()->addDays(7));
    }

    /**
     * Cocokkan term sebagai AWALAN nama tempat (per-huruf).
     * Hanya bagian nama tempat (kelurahan/kecamatan/kota) yang dicocokkan —
     * provinsi & kode pos diabaikan agar "d" tidak ikut memunculkan "DKI JAKARTA",
     * atau "j" memunculkan "JAWA BARAT".
     */
    private function prefixMatch(array $index, string $term): array
    {
        $t = mb_strtolower(trim($term));
        $out = [];
        foreach ($index as $d) {
            $parts = array_map('trim', explode(',', $d['label']));
            $n = count($parts);
            // Format umum: [kelurahan, kecamatan, kota, provinsi, kodepos] (5 bagian)
            // atau [kota, provinsi] (2 bagian). Buang provinsi & kodepos di ekor.
            $take = $n >= 5 ? 3 : max(1, $n - 1);
            for ($i = 0; $i < $take; $i++) {
                if (str_starts_with(mb_strtolower($parts[$i]), $t)) {
                    $out[] = ['id' => $d['id'], 'label' => $d['label'], 'value' => $d['label']];
                    break;
                }
            }
        }

        return $out;
    }

    /**
     * Pencarian destinasi: utamakan indeks LOKAL (instan, tanpa kuota API),
     * baru panggil API bila tidak ada kecocokan lokal. Hasil API di-cache &
     * dimasukkan ke indeks sehingga pengetikan per-huruf berikutnya gratis.
     */
    public function searchDestinations(Request $request)
    {
        $term = trim((string) ($request->term ?? ''));
        if ($term === '') {
            return response()->json([]);
        }

        // 1) Coba jawab dari indeks lokal (per-huruf, instan, hemat kuota)
        $local = $this->prefixMatch($this->loadIndex(), $term);
        if (! empty($local)) {
            return response()->json(array_slice($local, 0, 20));
        }

        // 2) Tidak ada di lokal → butuh API. Hindari term 1 huruf menembak API.
        if (mb_strlen($term) < 2 || empty(config('rajaongkir.api_key'))) {
            return response()->json([]);
        }

        $cacheKey = 'rajaongkir.search.'.md5(mb_strtolower($term));
        $cached = Cache::get($cacheKey);
        if ($cached !== null) {
            return response()->json(array_slice($cached, 0, 20));
        }

        try {
            $response = $this->send('get', self::DEST_URL, ['search' => $term, 'limit' => 20, 'offset' => 0]);

            if ($response->successful() && is_array($response->json('data'))) {
                $results = array_map(fn ($it) => [
                    'id' => $it['id'], 'label' => $it['label'], 'value' => $it['label'],
                ], $response->json('data'));

                Cache::put($cacheKey, $results, now()->addHours(12));
                $this->mergeIndex($results);

                return response()->json(array_slice($results, 0, 20));
            }

            // 404 (tidak ada) / 429 (kuota habis): cache kosong sebentar agar tidak
            // menembak API berulang, dan jangan bikin frontend error/spinner.
            Cache::put($cacheKey, [], now()->addMinutes(10));
            Log::warning('RajaOngkir search non-200', ['status' => $response->status(), 'term' => $term]);

            return response()->json([]);
        } catch (\Throwable $e) {
            Log::error('searchDestinations: '.$e->getMessage());

            return response()->json([]);
        }
    }

    /**
     * Cek ongkir (di-cache per kombinasi origin/dest/weight/courier).
     */
    public function cekOngkir(Request $request)
    {
        try {
            $request->validate([
                'courier' => 'required|string',
                'destination' => 'required|string',
                'weight' => 'required|integer',
                'cart_items' => 'nullable|string',
            ]);

            $apiKey = config('rajaongkir.api_key');
            $origin = config('rajaongkir.origin', 17549);
            $courier = strtolower(trim((string) $request->courier));

            // Hitung berat dari cart user bila ada (server-side), fallback ke request.
            $weight = max(1, (int) $request->weight);
            $user = $request->user() ?? Auth::user();
            $cartSignature = null;
            if ($user) {
                $cartQuery = Cart::where('user_id', $user->id)->with('product');
                $cartIds = array_filter(explode(',', (string) $request->input('cart_items', '')));
                if (! empty($cartIds)) {
                    $cartQuery->whereIn('id', $cartIds);
                }
                $cartItems = $cartQuery->get();
                if ($cartItems->isNotEmpty()) {
                    $weight = max(1, (int) $cartItems->sum(fn ($i) => ((int) ($i->product->weight ?? 0)) * ((int) $i->quantity)));
                    $cartSignature = sha1($cartItems->map(fn ($i) => $i->product_id.':'.$i->quantity)->sort()->implode('|'));
                }
            }

            $buildServices = function (array $services) use ($origin, $request, $weight, $courier, $cartSignature) {
                $normalized = [];
                foreach ($services as $service) {
                    $rawCost = (int) ($service['cost_raw'] ?? preg_replace('/\D/', '', (string) ($service['cost'] ?? 0)));
                    $normalized[] = [
                        'service' => $service['service'],
                        'description' => $service['description'] ?? '',
                        'cost' => number_format($rawCost, 0, ',', '.'),
                        'cost_raw' => $rawCost,
                        'etd' => $service['etd'] ?? '',
                    ];
                }
                session(['checkout_shipping_quote' => [
                    'origin' => (string) $origin,
                    'destination' => (string) $request->destination,
                    'weight' => (int) $weight,
                    'courier' => $courier,
                    'cart_signature' => $cartSignature,
                    'services' => collect($normalized)->mapWithKeys(fn ($s) => [strtolower($s['service']) => $s])->all(),
                    'created_at' => now()->timestamp,
                ]]);
                return $normalized;
            };

            if (empty($apiKey)) {
                $fallback = max(0, (int) Setting::getValue('default_shipping_fallback', 15000));
                $services = $buildServices([
                    ['service' => 'REG', 'description' => 'Layanan Reguler', 'cost_raw' => $fallback > 0 ? $fallback : 15000, 'etd' => '2-3 hari'],
                    ['service' => 'YES', 'description' => 'Yakin Esok Sampai', 'cost_raw' => $fallback > 0 ? $fallback + 10000 : 25000, 'etd' => '1 hari'],
                ]);
                return response()->json(['services' => $services]);
            }

            $cacheKey = 'rajaongkir.cost.'.md5(implode('|', [$origin, $request->destination, $weight, $courier]));
            $cached = Cache::get($cacheKey);
            if ($cached !== null) {
                $services = $buildServices($cached);
                return response()->json(['services' => $services]);
            }

            $response = $this->send('post', self::COST_URL, [
                'origin' => $origin,
                'destination' => $request->destination,
                'weight' => $weight,
                'courier' => $courier,
            ], true);

            if (! $response->successful()) {
                Log::warning('RajaOngkir cekOngkir non-200', ['status' => $response->status()]);

                return response()->json([
                    'error' => 'API request failed',
                    'message' => $response->status() === 429
                        ? 'Kuota API RajaOngkir harian habis. Coba lagi besok atau pakai key dengan kuota lebih besar.'
                        : 'Gagal menghitung ongkos kirim.',
                ], 500);
            }

            $services = [];
            foreach ((array) $response->json('data') as $service) {
                $services[] = [
                    'service' => $service['service'],
                    'description' => $service['description'],
                    'cost' => number_format($service['cost'], 0, ',', '.'),
                    'cost_raw' => (int) $service['cost'],
                    'etd' => $service['etd'],
                ];
            }

            Cache::put($cacheKey, $services, now()->addHours(6));
            $services = $buildServices($services);

            return response()->json(['services' => $services]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['error' => 'Validation failed', 'messages' => $e->errors()], 422);
        } catch (\Throwable $e) {
            Log::error('cekOngkir: '.$e->getMessage());

            return response()->json(['error' => 'Internal server error', 'message' => 'Terjadi kesalahan sistem saat mengecek ongkir.'], 500);
        }
    }
}