<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Dashboard
    public function index()
    {
        // Pengadaan yang menunggu approval
        $menungguApproval = DB::selectOne(
            "SELECT COUNT(*) AS total
             FROM pengadaan
             WHERE status = 'P'"
        );

        // Omzet penjualan bulan ini
        $omzetBulanIni = DB::selectOne(
            "SELECT COALESCE(SUM(total_nilai), 0) AS total
             FROM penjualan
             WHERE status = 'S'
               AND YEAR(created_at) = YEAR(CURDATE())
               AND MONTH(created_at) = MONTH(CURDATE())"
        );

        // Jumlah transaksi penjualan bulan ini
        $transaksiBulanIni = DB::selectOne(
            "SELECT COUNT(*) AS total
             FROM penjualan
             WHERE status = 'S'
               AND YEAR(created_at) = YEAR(CURDATE())
               AND MONTH(created_at) = MONTH(CURDATE())"
        );

        // Barang aktif yang stoknya habis
        $stokHabis = DB::selectOne(
            'SELECT COUNT(*) AS total
             FROM barang b
             WHERE b.status = 1
               AND COALESCE((
                   SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
                   FROM kartu_stok ks
                   WHERE ks.barang_idbarang = b.idbarang
               ), 0) <= 0'
        );

        // Margin penjualan yang sedang aktif
        $marginAktif = DB::selectOne(
            'SELECT idmargin_penjualan,
                persen
             FROM margin_penjualan
             WHERE status = 1
             LIMIT 1'
        );

        // Pengadaan terbaru yang menunggu approval
        $pengadaanMenunggu = DB::select(
            "SELECT
                p.idpengadaan,
                p.created_at,
                p.subtotal_nilai,
                p.total_nilai,
                v.nama_vendor,
                u.username
             FROM pengadaan p
             JOIN vendor v
                ON p.vendor_idvendor = v.idvendor
             JOIN `user` u
                ON p.user_iduser = u.iduser
             WHERE p.status = 'P'
             ORDER BY p.idpengadaan DESC
             LIMIT 5"
        );

        // Enam barang dengan sisa stok terbanyak untuk grafik ringkas
        $stokTeratas = DB::select(
            'SELECT b.nama,
                COALESCE(SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0)), 0) AS sisa
             FROM barang b
             LEFT JOIN kartu_stok ks
                ON ks.barang_idbarang = b.idbarang
             WHERE b.status = 1
             GROUP BY b.idbarang, b.nama
             ORDER BY sisa DESC
             LIMIT 6'
        );

        return view('manager.dashboard', compact(
            'menungguApproval',
            'omzetBulanIni',
            'transaksiBulanIni',
            'stokHabis',
            'marginAktif',
            'pengadaanMenunggu',
            'stokTeratas'
        ));
    }
}
