<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SatuanController extends Controller
{
    // Tabel satuan menggunakan VIEW
    public function dataSatuan()
    {
        $satuans = DB::select(
            'SELECT id, nama, status_data
         FROM view_satuan
         ORDER BY id ASC'
        );

        return view('admin.satuan.index', [
            'satuans' => $satuans,
        ]);
    }

    // form tambah satuan
    public function createSatuan()
    {
        return view('admin.satuan.create');
    }

    // tambah satuan
    public function storeSatuan(Request $request)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:45',
            'status' => 'required|in:0,1',
        ]);

        try {
            DB::insert(
                'INSERT into satuan(nama_satuan, status)
                values(?, ?)',
                [
                    $validated['nama_satuan'],
                    $validated['status'],
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_satuan' => 'Satuan gagal disimpan. Silahkan coba lagi',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.satuan')
            ->with('success', 'Satuan berhasil ditambahkan');
    }

    // form edit
    public function editSatuan($id)
    {
        $satuan = DB::select(
            'select idsatuan,
            nama_satuan,
            status
            from satuan
            where idsatuan = ?',
            [$id]
        );

        if (empty($satuan)) {
            abort(404, 'Data satuan tidak ditemukan');
        }

        return view('admin.satuan.edit', [
            'satuan' => $satuan[0],
        ]);
    }

    public function updateSatuan(Request $request, $id)
    {
        $validated = $request->validate([
            'nama_satuan' => 'required|string|max:45',
            'status' => 'required|in:0,1',
        ]);

        $satuan = DB::select(
            'select idsatuan,
            status
            from satuan
            where idsatuan = ?
            limit 1',
            [$id]
        );

        if (empty($satuan)) {
            abort(404, 'Data satuan tidak ditemukan');
        }
        $statusLama = (int) $satuan[0]->status;
        $statusBaru = (int) $validated['status'];

        // aturan aktif nonaktif
        if ($statusLama === 1 && $statusBaru === 0) {
            $barang = DB::select(
                'SELECT idbarang
                FROM barang
                WHERE satuan_idsatuan = ?
                LIMIT 1',
                [$id]
            );

            if (! empty($barang)) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'status' => 'Satuan tidak bisa dinonaktifkan karena masih digunakan oleh barang.',
                    ]);
            }
        }

        try {
            DB::update(
                'UPDATE satuan
                SET nama_satuan = ?,
                status = ?
                WHERE idsatuan = ?',
                [
                    $validated['nama_satuan'],
                    $statusBaru,
                    $id,
                ]
            );
        } catch (QueryException $e) {
            return back()
                ->withInput()
                ->withErrors([
                    'nama_satuan' => 'Gagal memperbarui satuan. Silakan coba lagi',
                ]);
        }

        return redirect()
            ->route('admin.dashboard.satuan')
            ->with('success', 'Satuan berhasil diperbarui');
    }

    // hapus satuan
    public function destroySatuan($id)
    {
        $satuan = DB::select(
            'select idsatuan
            from satuan
            where idsatuan = ?
            limit 1',
            [$id]
        );

        if (empty($satuan)) {
            abort(404, 'Data satuan tidak ditemukan');
        }

        try {
            DB::delete(
                'delete from satuan
                where idsatuan = ?',
                [$id]
            );
        } catch (QueryException $e) {
            return redirect()
                ->route('admin.dashboard.satuan')
                ->with('error', 'Satuan tidak dapat dihapus karena masih digunakan oleh barang.');
        }

        return redirect()
            ->route('admin.dashboard.satuan')
            ->with('success', 'Satuan berhasil dihapus');
    }
}
