<?php

namespace App\Http\Controllers;

use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class adminController extends Controller
{
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

    public function updateMargin(Request $request)
    {
        $request->validate([
            'idmargin_penjualan' => 'required|integer|min:1'
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
                'idmargin_penjualan' =>
                'Margin gagal diperbarui, silakan coba lagi.',
            ]);
        }

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Margin penjualan berhasil diperbarui');
    }

    public function dataBarang()
    {
        $barangs = DB::select(
            'select b.idbarang,
            b.jenis,
            b.nama,
            b.status,
            b.harga,
            s.nama_satuan
            from barang b
            join satuan s
            on b.satuan_idsatuan = s.idsatuan
            order by b.idbarang asc'
        );

        return view('admin.barang.index', [
            'barangs' => $barangs,
        ]);
    }
    public function createBarang()
    {
        $satuans = DB::select(
            'select idsatuan,
            nama_satuan
            from satuan
            order by nama_satuan'
        );

        return view('admin.barang.create', [
            'satuans' => $satuans
        ]);
    }
    public function storeBarang(Request $request)
    {
        $validated = $request->validate([
            'jenis' => 'required|string|size:1',
            'nama' => 'required|string|max:45',
            'satuan_idsatuan' => 'required|integer|min:1',
            'harga' => 'required|integer|min:0',
            'status' => 'required|in:0,1'
        ]);
        //pastikan satuan yang terpilih aktif
        $satuan = DB::select(
            'select idsatuan
            from satuan
            where idsatuan = ?
            and status = 1
            limit 1',
            [$validated['satuan_idsatuan']]
        );

        if(empty($satuan)){
            return back()
            ->withInput()
            ->withErrors([
                'satuan_idsatuan' => 'Satuan tidak ditemukan atau tidak aktif'
            ]);
        }

        DB::insert(
            'insert into barang(jenis, nama, satuan_idsatuan, status, harga)
            values (?, ?, ?, ?, ?)',[
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
}
