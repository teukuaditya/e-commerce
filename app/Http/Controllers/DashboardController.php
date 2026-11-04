<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;
use App\Models\Product;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        // Mengambil bulan saat ini
    $currentMonth = now()->month; // Bisa juga menggunakan Carbon::now()->month

    // Mengambil total pemasukan untuk bulan saat ini
    $earningsThisMonth = Transaction::where('transaction_status', 'settlement')
        ->whereMonth('transaction_time', $currentMonth) // Filter berdasarkan bulan saat ini
        ->sum('gross_amount');

        // Mengambil tahun saat ini
    $currentYear = now()->year;

    // Mengambil total pemasukan untuk tahun ini
    $earningsThisYear = Transaction::where('transaction_status', 'settlement')
        ->whereYear('transaction_time', $currentYear) // Filter berdasarkan tahun saat ini
        ->sum('gross_amount');

        // Menghitung jumlah transaksi yang statusnya 'pending' atau 'null' (grup where agar aman dari nesting)
        $pendingRequests = Transaction::where(function ($q) {
            $q->where('transaction_status', 'pending')
              ->orWhereNull('transaction_status');
        })->count();

        // Mengambil total pendapatan per bulan di tahun berjalan (DB-agnostic & filter settlement saja)
        $earningsPerMonth = Transaction::where('transaction_status', 'settlement')
            ->whereYear('transaction_time', $currentYear)
            ->whereNotNull('transaction_time')
            ->get()
            ->groupBy(function ($transaction) {
                return date('n', strtotime($transaction->transaction_time));
            })
            ->map(function ($rows, $month) {
                return (object) [
                    'month' => (int) $month,
                    'total' => (float) $rows->sum('gross_amount'),
                ];
            })
            ->values();

        /// Mengambil jumlah produk per kategori
    $productCountsByCategory = Product::selectRaw('category_id, COUNT(*) as count')
    ->groupBy('category_id')
    ->with('category') // Pastikan kategori dimuat
    ->get();

    //Menetapkan target pendapatan bulanan (misalnya, 100 juta)
    $monthlyTarget = 50000000; // Target pendapatan bulan ini, bisa disesuaikan

    // Mengambil total pendapatan bulan ini
    $totalEarnings = Transaction::where('transaction_status', 'settlement')
        ->whereMonth('transaction_time', now()->month) // Filter transaksi bulan ini
        ->sum('gross_amount');

    // Menghitung persentase pencapaian terhadap target
    $earningsPercentage = $monthlyTarget > 0 ? ($totalEarnings / $monthlyTarget) * 100 : 0;

        // Mengirimkan data ke view
        return view('pages.admin.dashboard.index', compact('earningsPerMonth', 'pendingRequests', 'earningsThisMonth', 'earningsThisYear', 'productCountsByCategory', 'earningsPercentage'));
    }
}
