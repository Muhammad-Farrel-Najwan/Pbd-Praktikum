<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    // Menampilkan laporan penjualan
    public function laporanPenjualan(Request $request)
    {
        $validated = $request->validate([
            'dari' => 'nullable|date',
            'sampai' => 'nullable|date|after_or_equal:dari',
        ]);

        $dari = $validated['dari'] ?? Carbon::now()->startOfMonth()->toDateString();
        $sampai = $validated['sampai'] ?? Carbon::now()->toDateString();
        $q = trim((string) $request->query('q'));
        $pattern = $this->kataKunciPencarian($q);

        $penjualan = $this->paginateData(
            "SELECT
                p.idpenjualan,
                p.created_at,
                p.subtotal_nilai,
                p.ppn,
                p.total_nilai,
                p.status,
                u.username AS kasir
             FROM penjualan p
             JOIN `user` u
                ON p.user_iduser = u.iduser
             WHERE p.status = 'S'
               AND DATE(p.created_at) BETWEEN ? AND ?
               AND (? IS NULL
                    OR CAST(p.idpenjualan AS CHAR) LIKE ?
                    OR u.username LIKE ?)
             ORDER BY p.idpenjualan DESC",
            [$dari, $sampai, $pattern, $pattern, $pattern]
        );

        $ringkasan = DB::selectOne(
            "SELECT
                COUNT(*) AS jumlah_transaksi,
                COALESCE(SUM(subtotal_nilai), 0) AS total_subtotal,
                COALESCE(SUM(ppn), 0) AS total_ppn,
                COALESCE(SUM(total_nilai), 0) AS total_nilai
             FROM penjualan
             WHERE status = 'S'
               AND DATE(created_at) BETWEEN ? AND ?",
            [$dari, $sampai]
        );

        return view('manager.laporan.penjualan', compact(
            'penjualan',
            'ringkasan',
            'dari',
            'sampai',
            'q'
        ));
    }

    // Menampilkan laporan stok
    public function laporanStok(Request $request)
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

        // Ringkasan dihitung dari seluruh data yang cocok, bukan hanya baris halaman ini
        $ringkasan = DB::selectOne(
            'SELECT
                COUNT(*) AS total_barang,
                COALESCE(SUM(data.stok), 0) AS total_stok,
                COALESCE(SUM(CASE WHEN data.stok <= 0 THEN 1 ELSE 0 END), 0) AS stok_habis
             FROM (
                SELECT
                    COALESCE((
                        SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
                        FROM kartu_stok ks
                        WHERE ks.barang_idbarang = b.idbarang
                    ), 0) AS stok
                FROM barang b
                JOIN satuan s
                    ON b.satuan_idsatuan = s.idsatuan
                WHERE (? IS NULL OR b.nama LIKE ? OR s.nama_satuan LIKE ?)
             ) AS data',
            [$pattern, $pattern, $pattern]
        );

        $totalBarang = (int) $ringkasan->total_barang;
        $totalStok = (int) $ringkasan->total_stok;
        $stokHabis = (int) $ringkasan->stok_habis;

        return view('manager.laporan.stok', compact(
            'stok',
            'totalBarang',
            'totalStok',
            'stokHabis',
            'q'
        ));
    }

    // Menampilkan riwayat mutasi stok barang
    public function detailLaporanStok(Request $request, $id)
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

        return view('manager.laporan.stok-detail', compact('barang', 'mutasi', 'q'));
    }
}
