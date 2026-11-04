<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VendorController extends Controller
{
    // Tabel vendor menggunakan VIEW
    public function dataVendor()
    {
        $vendors = DB::select(
            'SELECT id, nama, jenis_badan_usaha, status_vendor
         FROM view_vendor
         ORDER BY id ASC'
        );

        return view('admin.vendor.index', [
            'vendors' => $vendors,
        ]);
    }

    // Form tambah vendor
    public function createVendor()
    {
        return view('admin.vendor.create');
    }

    // Simpan vendor menggunakan SQL manual
    public function storeVendor(Request $request)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:100',
            'badan_hukum' => 'required|in:P,C,U,K,Y',
            'status' => 'required|in:A,N',
        ]);

        try {
            DB::insert(
                'INSERT INTO vendor (nama_vendor, badan_hukum, status)
             VALUES (?, ?, ?)',
                [
                    $validated['nama_vendor'],
                    $validated['badan_hukum'],
                    $validated['status'],
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_vendor' => 'Vendor gagal disimpan. Silakan coba lagi.',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.vendor')
            ->with('success', 'Vendor berhasil ditambahkan.');
    }

    // Form edit vendor
    public function editVendor($id)
    {
        $vendors = DB::select(
            'SELECT id, nama, jenis_badan_usaha, status_vendor
         FROM view_vendor
         WHERE id = ?
         LIMIT 1',
            [$id]
        );

        if (empty($vendors)) {
            abort(404, 'Data vendor tidak ditemukan.');
        }

        // Ambil kode asli badan hukum dan status dari tabel vendor
        $dataVendor = DB::select(
            'SELECT badan_hukum, status
         FROM vendor
         WHERE idvendor = ?
         LIMIT 1',
            [$id]
        );

        return view('admin.vendor.edit', [
            'vendor' => $vendors[0],
            'badanHukum' => $dataVendor[0]->badan_hukum,
            'status' => $dataVendor[0]->status,
        ]);
    }

    // Proses update vendor
    public function updateVendor(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_vendor' => 'required|string|max:100',
            'badan_hukum' => 'required|in:P,C,U,K,Y',
            'status' => 'required|in:A,N',
        ]);

        $vendor = DB::select(
            'SELECT idvendor
         FROM vendor
         WHERE idvendor = ?
         LIMIT 1',
            [$id]
        );

        if (empty($vendor)) {
            abort(404, 'Data vendor tidak ditemukan.');
        }

        try {
            DB::update(
                'UPDATE vendor
             SET nama_vendor = ?,
                 badan_hukum = ?,
                 status = ?
             WHERE idvendor = ?',
                [
                    $validated['nama_vendor'],
                    $validated['badan_hukum'],
                    $validated['status'],
                    $id,
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_vendor' => 'Vendor gagal diperbarui. Silakan coba lagi.',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.vendor')
            ->with('success', 'Vendor berhasil diperbarui.');
    }

    public function destroyVendor($id)
    {
        // 1. Pastikan vendor tersedia
        $vendor = DB::select(
            'SELECT idvendor
         FROM vendor
         WHERE idvendor = ?
         LIMIT 1',
            [$id]
        );

        if (empty($vendor)) {
            abort(404, 'Data vendor tidak ditemukan.');
        }

        // 2. Periksa apakah vendor pernah digunakan dalam pengadaan
        $pengadaan = DB::select(
            'SELECT idpengadaan
         FROM pengadaan
         WHERE vendor_idvendor = ?
         LIMIT 1',
            [$id]
        );

        if (! empty($pengadaan)) {
            return redirect()
                ->route('admin.dashboard.vendor')
                ->with(
                    'error',
                    'Vendor tidak dapat dihapus karena sudah digunakan dalam riwayat pengadaan. Ubah statusnya menjadi tidak aktif.'
                );
        }

        // 3. Hapus vendor jika tidak memiliki riwayat pengadaan
        try {
            DB::delete(
                'DELETE FROM vendor WHERE idvendor = ?',
                [$id]
            );
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.dashboard.vendor')
                ->with('error', 'Vendor gagal dihapus karena masih memiliki relasi data.');
        }

        return redirect()
            ->route('admin.dashboard.vendor')
            ->with('success', 'Vendor berhasil dihapus.');
    }
}
