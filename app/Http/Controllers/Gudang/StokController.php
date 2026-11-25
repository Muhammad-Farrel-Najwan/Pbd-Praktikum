<?php

namespace App\Http\Controllers\Gudang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StokController extends Controller
{
    // Menampilkan data stok semua barang
    public function dataStok(Request $request)
    {
        $q = trim((string) $request->query('q'));
        $pattern = $this->kataKunciPencarian($q);

        $stok = $this->paginateData(
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
             WHERE (? IS NULL OR b.nama LIKE ? OR s.nama_satuan LIKE ?)
             ORDER BY b.idbarang ASC',
            [$pattern, $pattern, $pattern]
        );

        return view('gudang.stok.index', compact('stok', 'q'));
    }

    // Menampilkan riwayat mutasi stok barang
    public function detailStok(Request $request, $id)
    {
        $q = trim((string) $request->query('q'));
        $pattern = $this->kataKunciPencarian($q);

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

        $mutasi = $this->paginateData(
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
               AND (? IS NULL
                    OR ks.jenis_transaksi LIKE ?
                    OR CAST(ks.idtransaksi AS CHAR) LIKE ?)
             ORDER BY ks.idkartu_stok DESC',
            [$id, $pattern, $pattern, $pattern]
        );

        return view('gudang.stok.detail', compact('barang', 'mutasi', 'q'));
    }
}
