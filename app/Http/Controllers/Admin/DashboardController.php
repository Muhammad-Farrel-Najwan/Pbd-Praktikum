<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    // Dashboard
    public function index()
    {
        // Ambil data margin
        $margins = DB::select(
            'SELECT idmargin_penjualan,
                persen,
                status,
                updated_at
         FROM margin_penjualan
         ORDER BY persen ASC'
        );

        // Cari margin aktif
        $activeMargin = null;
        $currentMargin = null;

        foreach ($margins as $margin) {
            if ((int) $margin->status === 1) {
                $activeMargin = $margin->idmargin_penjualan;
                $currentMargin = $margin;
                break;
            }
        }

        // Ringkasan jumlah data master
        $totalBarang = DB::select(
            'SELECT COUNT(*) AS total FROM barang'
        )[0]->total;

        $totalSatuan = DB::select(
            'SELECT COUNT(*) AS total FROM satuan'
        )[0]->total;

        $totalVendor = DB::select(
            'SELECT COUNT(*) AS total FROM vendor'
        )[0]->total;

        $totalUser = DB::select(
            'SELECT COUNT(*) AS total FROM `user`'
        )[0]->total;

        return view('admin.dashboard', [
            'margins' => $margins,
            'activeMargin' => $activeMargin,
            'currentMargin' => $currentMargin,

            // Kirim ringkasan data master ke view
            'totalBarang' => $totalBarang,
            'totalSatuan' => $totalSatuan,
            'totalVendor' => $totalVendor,
            'totalUser' => $totalUser,
        ]);
    }

    // ubah margin pake sp
    public function updateMargin(Request $request)
    {
        $request->validate([
            'idmargin_penjualan' => 'required|integer|min:1',
        ]);

        $idmargin = (int) $request->input('idmargin_penjualan');
        $iduser = (int) $request->session()->get('iduser');

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
}
