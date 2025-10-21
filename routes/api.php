<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\RajaOngkirController;
use App\Http\Controllers\Api\MidtransController;
use App\Http\Controllers\User\CheckoutController;

/*
|----------------------------------------------------------------------
| API Routes
|----------------------------------------------------------------------
|
| Berikut adalah tempat untuk mendaftarkan route API untuk aplikasi Anda.
| Route ini akan dimuat oleh RouteServiceProvider dan semuanya akan
| diberi middleware "api". Gunakan untuk membuat API yang hebat!
|
*/

// Route untuk menghitung ongkos kirim dengan mengirimkan data ke RajaOngkir
Route::get('/search-destinations', [RajaOngkirController::class, 'searchDestinations']);
Route::post('cost', [RajaOngkirController::class, 'cekOngkir']);

// Route untuk menangani callback dari Midtrans (setelah pembayaran)
Route::post('/midtrans/snap', [MidtransController::class, 'getSnapToken']);
Route::post('/midtrans/callback', [MidtransController::class, 'handleMidtransCallback']);

