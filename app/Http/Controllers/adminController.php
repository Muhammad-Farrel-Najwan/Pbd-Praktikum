<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class adminController extends Controller
{
    // dashboard
    public function index()
    {
        $margins = DB::select(
            '
        select idmargin_penjualan,
        persen,
        status,
        updated_at
        from margin_penjualan
        order by persen asc'
        );

        $activeMargin = null;

        foreach ($margins as $margin) {
            if ((int) $margin->status === 1) {
                $activeMargin = $margin->idmargin_penjualan;
                break;
            }
        }

        return view('admin.dashboard', [
            'margins' => $margins,
            'activeMargin' => $activeMargin,
        ]);
    }

    // ubah margin pake sp
    public function updateMargin(Request $request)
    {
        $request->validate([
            'idmargin_penjualan' => 'required|integer|min:1',
        ]);

        $idmargin = (int) $request->input('idmargin_penjualan');
        $iduser = $request->session()->get('iduser');

        // Pastikan margin tersedia
        $margin = DB::select(
            'SELECT idmargin_penjualan
         FROM margin_penjualan
         WHERE idmargin_penjualan = ?
         LIMIT 1',
            [$idmargin]
        );

        if (empty($margin)) {
            return back()->withErrors([
                'idmargin_penjualan' => 'Margin tidak ditemukan',
            ]);
        }

        try {
            DB::statement(
                'CALL sp_aktifkan_margin(?, ?)',
                [$idmargin, $iduser]
            );
        } catch (QueryException $e) {
            return back()->withErrors([
                'idmargin_penjualan' => 'Margin gagal diperbarui, silakan coba lagi.',
            ]);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Margin penjualan berhasil diperbarui');
    }

    // Tabel barang menggunakan VIEW
    public function dataBarang()
    {
        $barangs = DB::select(
            'SELECT id, kategori, nama_barang, satuan,
                status_barang, harga_barang
         FROM view_barang
         ORDER BY id ASC'
        );

        return view('admin.barang.index', [
            'barangs' => $barangs,
        ]);
    }

    // tabel tambah barang
    public function createBarang()
    {
        $satuans = DB::select(
            'select idsatuan,
            nama_satuan
            from satuan
            order by nama_satuan'
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
            'harga' => 'required|integer|min:0',
            'status' => 'required|in:0,1',
        ]);
        // pastikan satuan yang terpilih aktif
        $satuan = DB::select(
            'select idsatuan
            from satuan
            where idsatuan = ?
            and status = 1
            limit 1',
            [$validated['satuan_idsatuan']]
        );

        if (empty($satuan)) {
            return back()
                ->withInput()
                ->withErrors([
                    'satuan_idsatuan' => 'Satuan tidak ditemukan atau tidak aktif',
                ]);
        }

        DB::insert(
            'insert into barang(jenis, nama, satuan_idsatuan, status, harga)
            values (?, ?, ?, ?, ?)',
            [
                $validated['jenis'],
                $validated['nama'],
                $validated['satuan_idsatuan'],
                $validated['status'],
                $validated['harga'],
            ]
        );

        return redirect()
            ->route('admin.dashboard.barang')
            ->with('success', 'Barang berhasil ditambahkan');
    }

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
                values(?, ?)', [
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
            DB::update(
                'update satuan
                set nama_satuan = ?,
                status = ?
                where idsatuan = ?', [
                    $validated['nama_satuan'],
                    $validated['status'],
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
}
