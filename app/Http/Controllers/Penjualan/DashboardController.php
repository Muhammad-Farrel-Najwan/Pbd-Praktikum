<?php

namespace App\Http\Controllers\Penjualan;

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

        // Lima transaksi terakhir milik user yang login
        $riwayatTransaksi = DB::select(
            'SELECT
                idpenjualan,
                created_at,
                total_nilai,
                status
             FROM penjualan
             WHERE user_iduser = ?
             ORDER BY idpenjualan DESC
             LIMIT 5',
            [$iduser]
        );

        return view('penjualan.dashboard', compact(
            'totalTransaksi',
            'totalPendapatan',
            'marginAktif',
            'riwayatTransaksi'
        ));
    }
}
