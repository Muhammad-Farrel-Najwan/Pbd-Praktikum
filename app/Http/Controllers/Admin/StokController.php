<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    // Menampilkan data stok semua barang
    public function dataStok()
    {
        $stok = DB::select(
            'SELECT
                b.idbarang,
                b.nama,
                b.status,
                s.nama_satuan,
                COALESCE((
                    SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
                    FROM kartu_stok ks
                    WHERE ks.barang_idbarang = b.idbarang
                ), 0) AS stok
             FROM barang b
             JOIN satuan s
                ON b.satuan_idsatuan = s.idsatuan
             ORDER BY b.nama ASC'
        );

        return view('admin.stok.index', compact('stok'));
    }

    // Menampilkan riwayat mutasi stok barang
    public function detailStok($id)
    {
        $barang = DB::selectOne(
            'SELECT
                b.idbarang,
                b.nama,
                b.status,
                s.nama_satuan,
                COALESCE((
                    SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
                    FROM kartu_stok ks
                    WHERE ks.barang_idbarang = b.idbarang
                ), 0) AS stok
             FROM barang b
             JOIN satuan s
                ON b.satuan_idsatuan = s.idsatuan
             WHERE b.idbarang = ?
             LIMIT 1',
            [$id]
        );

        if (! $barang) {
            abort(404, 'Data barang tidak ditemukan.');
        }

        $mutasi = DB::select(
            'SELECT
                ks.idkartu_stok,
                ks.jenis_transaksi,
                ks.masuk,
                ks.keluar,
                ks.stock,
                ks.created_at,
                ks.idtransaksi
             FROM kartu_stok ks
             WHERE ks.barang_idbarang = ?
             ORDER BY ks.created_at DESC,
                ks.idkartu_stok DESC',
            [$id]
        );

        return view('admin.stok.detail', compact('barang', 'mutasi'));
    }
}
