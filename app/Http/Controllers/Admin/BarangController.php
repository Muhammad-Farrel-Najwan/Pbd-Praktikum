<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BarangController extends Controller
{
    // Tabel barang menggunakan VIEW
    public function dataBarang()
    {
        $barangs = DB::select(
            'SELECT
                vb.id,
                vb.kategori,
                vb.nama_barang,
                vb.satuan,
                vb.status_barang,
                vb.harga_barang,
                COALESCE((
                    SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
                    FROM kartu_stok ks
                    WHERE ks.barang_idbarang = vb.id
                ), 0) AS stok
         FROM view_barang vb
         ORDER BY vb.id ASC'
        );

        return view('admin.barang.index', [
            'barangs' => $barangs,
        ]);
    }

    // tabel tambah barang
    public function createBarang()
    {
        $satuans = DB::select(
            'SELECT idsatuan,
                nama_satuan
         FROM satuan
         WHERE status = 1
         ORDER BY nama_satuan ASC'
        );

        return view('admin.barang.create', [
            'satuans' => $satuans,
        ]);
    }

    // tambah barang
    public function storeBarang(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|size:1',
            'nama' => 'required|string|max:45',
            'satuan_idsatuan' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0|max:2147483647',
            'status' => 'required|in:0,1',
        ]);

        $jenis = strtoupper($validated['jenis']);

        // Pastikan satuan yang terpilih aktif
        $satuan = DB::select(
            'SELECT idsatuan
         FROM satuan
         WHERE idsatuan = ?
           AND status = 1
         LIMIT 1',
            [$validated['satuan_idsatuan']]
        );

        if (empty($satuan)) {
            return back()
                ->withInput()
                ->withErrors([
                    'satuan_idsatuan' => 'Satuan tidak ditemukan atau tidak aktif',
                ]);
        }

        try {
            DB::insert(
                'INSERT INTO barang(jenis, nama, satuan_idsatuan, status, harga)
             VALUES (?, ?, ?, ?, ?)',
                [
                    $jenis,
                    $validated['nama'],
                    $validated['satuan_idsatuan'],
                    $validated['status'],
                    $validated['harga'],
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama' => 'Barang gagal disimpan. Silakan periksa kembali data.',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.barang')
            ->with('success', 'Barang berhasil ditambahkan');
    }

    public function editBarang($id)
    {
        $barangs = DB::select(
            'SELECT id, kategori, nama_barang, satuan,
                status_barang, harga_barang
         FROM view_barang
         WHERE id = ?
         LIMIT 1',
            [$id]
        );

        if (empty($barangs)) {
            abort(404, 'Data barang tidak ditemukan.');
        }

        $dataAsli = DB::select(
            'SELECT jenis, nama, satuan_idsatuan, status, harga
         FROM barang
         WHERE idbarang = ?
         LIMIT 1',
            [$id]
        );

        $satuans = DB::select(
            'SELECT idsatuan, nama_satuan
         FROM satuan
         WHERE status = 1
            OR idsatuan = ?
         ORDER BY nama_satuan ASC',
            [$dataAsli[0]->satuan_idsatuan]
        );

        return view('admin.barang.edit', [
            'barang' => $barangs[0],
            'dataAsli' => $dataAsli[0],
            'satuans' => $satuans,
        ]);
    }

    public function updateBarang(Request $request, $id)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|size:1',
            'nama' => 'required|string|max:45',
            'satuan_idsatuan' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0|max:2147483647',
            'status' => 'required|in:0,1',
        ]);

        $jenis = strtoupper($validated['jenis']);

        $barang = DB::select(
            'SELECT idbarang
         FROM barang
         WHERE idbarang = ?
         LIMIT 1',
            [$id]
        );

        if (empty($barang)) {
            abort(404, 'Data barang tidak ditemukan.');
        }

        $satuan = DB::select(
            'SELECT idsatuan
         FROM satuan
         WHERE idsatuan = ?
           AND status = 1
         LIMIT 1',
            [$validated['satuan_idsatuan']]
        );

        // Izinkan satuan lama tetap dipakai jika satuan itu sudah nonaktif.
        $satuanLama = DB::select(
            'SELECT satuan_idsatuan
         FROM barang
         WHERE idbarang = ?
         LIMIT 1',
            [$id]
        );

        $idSatuanBaru = (int) $validated['satuan_idsatuan'];
        $idSatuanLama = (int) $satuanLama[0]->satuan_idsatuan;

        if (empty($satuan) && $idSatuanBaru !== $idSatuanLama) {
            return back()
                ->withInput()
                ->withErrors([
                    'satuan_idsatuan' => 'Satuan baru harus berstatus aktif.',
                ]);
        }

        try {
            DB::update(
                'UPDATE barang
             SET jenis = ?,
                 nama = ?,
                 satuan_idsatuan = ?,
                 harga = ?,
                 status = ?
             WHERE idbarang = ?',
                [
                    $jenis,
                    $validated['nama'],
                    $idSatuanBaru,
                    $validated['harga'],
                    $validated['status'],
                    $id,
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama' => 'Barang gagal diperbarui. Periksa kembali relasi data.',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.barang')
            ->with('success', 'Data barang berhasil diperbarui.');
    }

    public function destroyBarang($id)
    {
        // Pastikan barang tersedia
        $barang = DB::select(
            'SELECT idbarang
         FROM barang
         WHERE idbarang = ?
         LIMIT 1',
            [$id]
        );

        if (empty($barang)) {
            abort(404, 'Data barang tidak ditemukan.');
        }

        // Tabel-tabel yang mungkin menyimpan riwayat barang
        $relasi = [
            'detail_pengadaan' => 'barang_idbarang',
            'detail_penerimaan' => 'barang_idbarang',
            'detail_penjualan' => 'barang_idbarang',
            'kartu_stok' => 'barang_idbarang',
        ];

        // Tolak penghapusan jika barang sudah digunakan
        foreach ($relasi as $tabel => $kolom) {
            $hasil = DB::select(
                "SELECT 1 FROM {$tabel}
             WHERE {$kolom} = ?
             LIMIT 1",
                [$id]
            );

            if (! empty($hasil)) {
                return redirect()
                    ->route('admin.dashboard.barang')
                    ->with(
                        'error',
                        'Barang tidak dapat dihapus karena sudah memiliki riwayat transaksi atau stok. Silakan nonaktifkan barang melalui fitur Edit.'
                    );
            }
        }

        // Hapus hanya jika tidak ada riwayat terkait
        try {
            DB::delete(
                'DELETE FROM barang WHERE idbarang = ?',
                [$id]
            );
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.dashboard.barang')
                ->with(
                    'error',
                    'Barang gagal dihapus karena masih terhubung dengan data lain.'
                );
        }

        return redirect()
            ->route('admin.dashboard.barang')
            ->with('success', 'Barang berhasil dihapus.');
    }
}
