<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Dashboard
    public function index()
    {
        $iduser = (int) session('iduser');

        // Total transaksi hari ini
        $totalTransaksi = DB::selectOne(
            'SELECT COUNT(*) AS total
            FROM penjualan
            WHERE DATE(created_at) = CURDATE()
            AND user_iduser = ?',
            [$iduser]
        );

        // Total pendapatan hari ini
        $totalPendapatan = DB::selectOne(
            'SELECT COALESCE(SUM(total_nilai), 0) AS total
            FROM penjualan
            WHERE DATE(created_at) = CURDATE()
            AND user_iduser = ?',
            [$iduser]
        );

        // Margin penjualan yang sedang aktif
        $marginAktif = DB::selectOne(
            'SELECT idmargin_penjualan,
            persen
            FROM margin_penjualan
            WHERE status = 1
            LIMIT 1'
        );

        // Riwayat transaksi terbaru milik kasir yang sedang login
        $riwayatTransaksi = DB::select(
            'SELECT idpenjualan,
            created_at,
            subtotal_nilai,
            ppn,
            total_nilai,
            status
            FROM penjualan
            WHERE user_iduser = ?
            ORDER BY created_at DESC
            LIMIT 5',
            [$iduser]
        );

        return view('kasir.dashboard', compact(
            'totalTransaksi',
            'totalPendapatan',
            'marginAktif',
            'riwayatTransaksi'
        ));
    }
}
