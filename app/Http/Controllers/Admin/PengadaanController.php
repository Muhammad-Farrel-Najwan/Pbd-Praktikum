<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PengadaanController extends Controller
{
    // Menampilkan data pengadaan
    public function dataPengadaan()
    {
        $pengadaan = DB::select(
            'SELECT
                p.idpengadaan,
                p.created_at,
                p.status,
                p.subtotal_nilai,
                p.ppn,
                p.total_nilai,
                v.nama_vendor,
                u.username
             FROM pengadaan p
             JOIN vendor v
                ON p.vendor_idvendor = v.idvendor
             JOIN `user` u
                ON p.user_iduser = u.iduser
             ORDER BY p.idpengadaan DESC'
        );

        return view('admin.pengadaan.index', [
            'pengadaan' => $pengadaan,
        ]);
    }

    // Form tambah pengadaan
    public function createPengadaan()
    {
        $vendors = DB::select(
            "SELECT idvendor, nama_vendor
             FROM vendor
             WHERE status = 'A'
             ORDER BY nama_vendor ASC"
        );

        $barang = DB::select(
            'SELECT idbarang, nama, harga
             FROM barang
             WHERE status = 1
             ORDER BY nama ASC'
        );

        return view('admin.pengadaan.create', [
            'vendors' => $vendors,
            'barang' => $barang,
        ]);
    }

    // Menyimpan pengadaan
    public function storePengadaan(Request $request)
    {
        $validated = $request->validate([
            'vendor_idvendor' => 'required|integer',
            'barang_idbarang' => 'required|array|min:1',
            'barang_idbarang.*' => 'required|integer|distinct',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:1',
        ]);

        $vendor = DB::selectOne(
            "SELECT idvendor
             FROM vendor
             WHERE idvendor = ?
             AND status = 'A'
             LIMIT 1",
            [$validated['vendor_idvendor']]
        );

        if (! $vendor) {
            throw ValidationException::withMessages([
                'vendor_idvendor' => 'Vendor tidak valid atau sudah tidak aktif.',
            ]);
        }

        $items = [];
        $subtotal = 0;

        // Validasi barang dan hitung subtotal
        foreach ($validated['barang_idbarang'] as $idbarang) {
            $jumlah = (int) ($validated['jumlah'][$idbarang] ?? 0);

            if ($jumlah <= 0) {
                throw ValidationException::withMessages([
                    'jumlah' => 'Jumlah barang harus lebih dari nol.',
                ]);
            }

            $barang = DB::selectOne(
                'SELECT idbarang, harga
                 FROM barang
                 WHERE idbarang = ?
                 AND status = 1
                 LIMIT 1',
                [$idbarang]
            );

            if (! $barang) {
                throw ValidationException::withMessages([
                    'barang_idbarang' => 'Salah satu barang tidak valid atau sudah tidak aktif.',
                ]);
            }

            $harga = (int) $barang->harga;

            $items[] = [
                'idbarang' => (int) $barang->idbarang,
                'harga' => $harga,
                'jumlah' => $jumlah,
                'sub_total' => $harga * $jumlah,
            ];

            $subtotal += $harga * $jumlah;
        }

        DB::beginTransaction();

        try {
            // Simpan header pengadaan
            DB::insert(
                'INSERT INTO pengadaan
                (
                    created_at,
                    user_iduser,
                    status,
                    vendor_idvendor,
                    subtotal_nilai,
                    ppn,
                    total_nilai
                )
                VALUES (NOW(), ?, NULL, ?, ?, 0, ?)',
                [
                    session('iduser'),
                    $validated['vendor_idvendor'],
                    $subtotal,
                    $subtotal,
                ]
            );

            $idpengadaan = DB::getPdo()->lastInsertId();

            // Simpan detail pengadaan
            foreach ($items as $item) {
                DB::insert(
                    'INSERT INTO detail_pengadaan
                    (
                        harga_satuan,
                        jumlah,
                        sub_total,
                        barang_idbarang,
                        pengadaan_idpengadaan
                    )
                    VALUES (?, ?, ?, ?, ?)',
                    [
                        $item['harga'],
                        $item['jumlah'],
                        $item['sub_total'],
                        $item['idbarang'],
                        $idpengadaan,
                    ]
                );
            }
            DB::commit();

            return redirect()
                ->route('admin.dashboard.pengadaan')
                ->with('success', 'Pengadaan berhasil ditambahkan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan pengadaan: '.$e->getMessage());
        }
    }

    // Menampilkan detail pengadaan
    public function detailPengadaan($id)
    {
        $pengadaan = DB::selectOne(
            'SELECT
                p.idpengadaan,
                p.created_at,
                p.status,
                p.subtotal_nilai,
                p.ppn,
                p.total_nilai,
                v.nama_vendor,
                u.username
             FROM pengadaan p
             JOIN vendor v
                ON p.vendor_idvendor = v.idvendor
             JOIN `user` u
                ON p.user_iduser = u.iduser
             WHERE p.idpengadaan = ?
             LIMIT 1',
            [$id]
        );

        if (! $pengadaan) {
            abort(404, 'Data pengadaan tidak ditemukan.');
        }

        $detail = DB::select(
            'SELECT
                dp.iddetail_pengadaan,
                dp.harga_satuan,
                dp.jumlah,
                dp.sub_total,
                dp.barang_idbarang,
                b.nama
             FROM detail_pengadaan dp
             JOIN barang b
                ON dp.barang_idbarang = b.idbarang
             WHERE dp.pengadaan_idpengadaan = ?
             ORDER BY dp.iddetail_pengadaan ASC',
            [$id]
        );

        return view('admin.pengadaan.detail', [
            'pengadaan' => $pengadaan,
            'detail' => $detail,
        ]);
    }
}
