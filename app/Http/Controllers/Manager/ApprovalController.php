<?php

namespace App\Http\Controllers\Manager;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApprovalController extends Controller
{
    // Menampilkan data pengadaan untuk approval
    public function dataApproval(Request $request)
    {
        $status = $request->query('status');
        $q = trim((string) $request->query('q'));
        $pattern = $this->kataKunciPencarian($q);

        // Hanya izinkan filter status P dan S
        if (! in_array($status, ['P', 'S'], true)) {
            $status = null;
        }

        $pengadaan = $this->paginateData(
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
             WHERE (? IS NULL OR p.status = ?)
               AND (? IS NULL
                    OR CAST(p.idpengadaan AS CHAR) LIKE ?
                    OR v.nama_vendor LIKE ?
                    OR u.username LIKE ?)
             ORDER BY p.idpengadaan DESC',
            [$status, $status, $pattern, $pattern, $pattern, $pattern]
        );

        return view('manager.approval.index', compact('pengadaan', 'status', 'q'));
    }

    // Menampilkan detail pengadaan yang akan disetujui
    public function detailApproval($id)
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

        return view('manager.approval.detail', compact('pengadaan', 'detail'));
    }

    // Menyetujui pengadaan
    public function setujuiPengadaan($id)
    {
        DB::beginTransaction();

        try {
            // Hitung total dan tandai pengadaan selesai
            DB::select(
                'CALL sp_selesai_pengadaan(?, @status, @message)',
                [$id]
            );

            $hasil = DB::selectOne(
                'SELECT @status AS status, @message AS message'
            );

            $status = $hasil->status ?? 'E';
            $message = $hasil->message ?? 'Pengadaan gagal disetujui.';

            if ($status !== 'S') {
                DB::rollBack();

                return back()->with('error', $message);
            }

            DB::commit();

            return redirect()
                ->route('manager.approval.detail', $id)
                ->with('success', $message);
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal menyetujui pengadaan: '.$e->getMessage());
        }
    }
}
