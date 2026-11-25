<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Dashboard
    public function index()
    {
        // Pengadaan yang menunggu approval Manager
        $menungguApproval = DB::selectOne(
            "SELECT COUNT(*) AS total
             FROM pengadaan
             WHERE status = 'P'"
        );

        // Pengadaan yang sudah disetujui dan masih memiliki sisa barang
        $siapDiterima = DB::selectOne(
            "SELECT COUNT(*) AS total
             FROM pengadaan pg
             WHERE pg.status = 'S'
               AND EXISTS (
                   SELECT 1
                   FROM detail_pengadaan dp
                   WHERE dp.pengadaan_idpengadaan = pg.idpengadaan
                     AND dp.jumlah > COALESCE((
                         SELECT SUM(dpt.jumlah_terima)
                         FROM detail_penerimaan dpt
                         JOIN penerimaan pt
                            ON dpt.penerimaan_idpenerimaan = pt.idpenerimaan
                         WHERE pt.pengadaan_idpengadaan = dp.pengadaan_idpengadaan
                           AND dpt.barang_idbarang = dp.barang_idbarang
                     ), 0)
               )"
        );

        // Penerimaan yang dicatat hari ini
        $penerimaanHariIni = DB::selectOne(
            'SELECT COUNT(*) AS total
             FROM penerimaan
             WHERE DATE(created_at) = CURDATE()'
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

        // Pengadaan terbaru
        $pengadaanTerbaru = DB::select(
            'SELECT
                p.idpengadaan,
                p.created_at,
                p.status,
                p.total_nilai,
                v.nama_vendor
             FROM pengadaan p
             JOIN vendor v
                ON p.vendor_idvendor = v.idvendor
             ORDER BY p.idpengadaan DESC
             LIMIT 5'
        );

        // Penerimaan terbaru
        $penerimaanTerbaru = DB::select(
            'SELECT
                p.idpenerimaan,
                p.created_at,
                p.status,
                v.nama_vendor
             FROM penerimaan p
             JOIN pengadaan pg
                ON p.pengadaan_idpengadaan = pg.idpengadaan
             JOIN vendor v
                ON pg.vendor_idvendor = v.idvendor
             ORDER BY p.idpenerimaan DESC
             LIMIT 5'
        );

        return view('gudang.dashboard', compact(
            'menungguApproval',
            'siapDiterima',
            'penerimaanHariIni',
            'stokHabis',
            'pengadaanTerbaru',
            'penerimaanTerbaru'
        ));
    }
}
