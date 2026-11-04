<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PenerimaanController extends Controller
{
    // Menampilkan data penerimaan
    public function dataPenerimaan()
    {
        $penerimaan = DB::select(
            'SELECT
                p.idpenerimaan,
                p.created_at,
                p.status,
                p.pengadaan_idpengadaan,
                p.user_iduser,
                v.nama_vendor,
                u.username,
                COALESCE((
                    SELECT SUM(dp.sub_total_terima)
                    FROM detail_penerimaan dp
                    WHERE dp.penerimaan_idpenerimaan = p.idpenerimaan
                ), 0) AS total_nilai
             FROM penerimaan p
             JOIN pengadaan pg
                ON p.pengadaan_idpengadaan = pg.idpengadaan
             JOIN vendor v
                ON pg.vendor_idvendor = v.idvendor
             JOIN `user` u
                ON p.user_iduser = u.iduser
             ORDER BY p.idpenerimaan DESC'
        );

        return view('admin.penerimaan.index', compact('penerimaan'));
    }

    // Form tambah penerimaan
    public function createPenerimaan(Request $request)
    {
        // Hanya tampilkan pengadaan yang masih memiliki sisa barang
        $pengadaan = DB::select(
            'SELECT
                pg.idpengadaan,
                pg.created_at,
                v.nama_vendor
             FROM pengadaan pg
             JOIN vendor v
                ON pg.vendor_idvendor = v.idvendor
             WHERE EXISTS (
                SELECT 1
                FROM detail_pengadaan dp
                WHERE dp.pengadaan_idpengadaan = pg.idpengadaan
                  AND dp.jumlah > COALESCE((
                      SELECT SUM(dpt.jumlah_terima)
                      FROM detail_penerimaan dpt
                      JOIN penerimaan pt
                         ON dpt.penerimaan_idpenerimaan = pt.idpenerimaan
                      WHERE pt.pengadaan_idpengadaan = dp.pengadaan_idpengadaan
                        AND dpt.barang_idbarang = dp.barang_idbarang
                  ), 0)
             )
             ORDER BY pg.idpengadaan DESC'
        );

        $idpengadaan = $request->integer('pengadaan_idpengadaan');
        $detailPengadaan = [];

        // Mengambil detail berdasarkan pengadaan yang dipilih
        if ($idpengadaan > 0) {
            $detailPengadaan = DB::select(
                'SELECT
                    dp.barang_idbarang,
                    b.nama,
                    dp.harga_satuan,
                    dp.jumlah,
                    dp.jumlah - COALESCE((
                        SELECT SUM(dpt.jumlah_terima)
                        FROM detail_penerimaan dpt
                        JOIN penerimaan pt
                           ON dpt.penerimaan_idpenerimaan = pt.idpenerimaan
                        WHERE pt.pengadaan_idpengadaan = dp.pengadaan_idpengadaan
                          AND dpt.barang_idbarang = dp.barang_idbarang
                    ), 0) AS sisa_belum_diterima
                 FROM detail_pengadaan dp
                 JOIN barang b
                    ON dp.barang_idbarang = b.idbarang
                 WHERE dp.pengadaan_idpengadaan = ?
                 HAVING sisa_belum_diterima > 0
                 ORDER BY dp.iddetail_pengadaan ASC',
                [$idpengadaan]
            );

            if (empty($detailPengadaan)) {
                return redirect()
                    ->route('admin.penerimaan.create')
                    ->with('error', 'Pengadaan tidak ditemukan atau seluruh barangnya sudah diterima.');
            }
        }

        return view(
            'admin.penerimaan.create',
            compact('pengadaan', 'detailPengadaan', 'idpengadaan')
        );
    }

    // Menyimpan penerimaan
    public function storePenerimaan(Request $request)
    {
        $validated = $request->validate([
            'pengadaan_idpengadaan' => 'required|integer|min:1',
            'barang_idbarang' => 'required|array|min:1',
            'barang_idbarang.*' => 'required|integer|distinct|min:1',
            'jumlah_terima' => 'required|array',
            'jumlah_terima.*' => 'required|integer|min:0',
        ]);

        if (count($validated['barang_idbarang']) !== count($validated['jumlah_terima'])) {
            return back()
                ->withInput()
                ->with('error', 'Data barang dan jumlah penerimaan tidak sesuai.');
        }

        $idpengadaan = (int) $validated['pengadaan_idpengadaan'];
        $iduser = (int) session('iduser');

        DB::beginTransaction();

        try {
            // Kunci pengadaan agar dua penerimaan tidak dihitung bersamaan
            $pengadaan = DB::selectOne(
                'SELECT idpengadaan
                 FROM pengadaan
                 WHERE idpengadaan = ?
                 LIMIT 1
                 FOR UPDATE',
                [$idpengadaan]
            );

            if (! $pengadaan) {
                throw new \Exception('Pengadaan tidak ditemukan.');
            }

            // Membuat header penerimaan
            DB::insert(
                'INSERT INTO penerimaan
                (
                    created_at,
                    status,
                    pengadaan_idpengadaan,
                    user_iduser
                )
                VALUES (NOW(), ?, ?, ?)',
                [
                    'P',
                    $idpengadaan,
                    $iduser,
                ]
            );

            $idpenerimaan = (int) DB::getPdo()->lastInsertId();
            $adaBarang = false;

            // Menyimpan detail penerimaan
            foreach ($validated['barang_idbarang'] as $index => $idbarang) {
                $jumlah = (int) $validated['jumlah_terima'][$index];

                if ($jumlah === 0) {
                    continue;
                }

                // Pastikan barang memang terdapat dalam pengadaan
                $detail = DB::selectOne(
                    'SELECT
                        dp.harga_satuan,
                        dp.jumlah,
                        dp.jumlah - COALESCE((
                            SELECT SUM(dpt.jumlah_terima)
                            FROM detail_penerimaan dpt
                            JOIN penerimaan pt
                               ON dpt.penerimaan_idpenerimaan = pt.idpenerimaan
                            WHERE pt.pengadaan_idpengadaan = dp.pengadaan_idpengadaan
                              AND dpt.barang_idbarang = dp.barang_idbarang
                        ), 0) AS sisa
                     FROM detail_pengadaan dp
                     WHERE dp.pengadaan_idpengadaan = ?
                       AND dp.barang_idbarang = ?
                     LIMIT 1',
                    [
                        $idpengadaan,
                        $idbarang,
                    ]
                );

                if (! $detail) {
                    throw new \Exception('Barang tidak ditemukan dalam pengadaan.');
                }

                $sisa = (int) $detail->sisa;

                if ($sisa <= 0) {
                    throw new \Exception('Barang sudah diterima seluruhnya.');
                }

                if ($jumlah > $sisa) {
                    throw new \Exception('Jumlah penerimaan melebihi sisa pesanan.');
                }

                $harga = (int) $detail->harga_satuan;
                $subtotal = $harga * $jumlah;

                DB::insert(
                    'INSERT INTO detail_penerimaan
                    (
                        barang_idbarang,
                        penerimaan_idpenerimaan,
                        jumlah_terima,
                        harga_satuan_terima,
                        sub_total_terima
                    )
                    VALUES (?, ?, ?, ?, ?)',
                    [
                        $idbarang,
                        $idpenerimaan,
                        $jumlah,
                        $harga,
                        $subtotal,
                    ]
                );

                $adaBarang = true;
            }

            if (! $adaBarang) {
                throw new \Exception('Minimal satu barang harus diterima.');
            }

            // Mengubah status menjadi selesai.
            // Trigger trg_penerimaan_selesai_stok akan mencatat barang masuk ke kartu_stok.
            DB::update(
                'UPDATE penerimaan
                 SET status = ?
                 WHERE idpenerimaan = ?',
                [
                    'S',
                    $idpenerimaan,
                ]
            );

            DB::commit();

            return redirect()
                ->route('admin.dashboard.penerimaan')
                ->with('success', 'Penerimaan berhasil disimpan.');
        } catch (\Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('admin.penerimaan.create', [
                    'pengadaan_idpengadaan' => $idpengadaan,
                ])
                ->withInput()
                ->with('error', 'Gagal menyimpan penerimaan: '.$e->getMessage());
        }
    }

    // Menampilkan detail penerimaan
    public function detailPenerimaan($id)
    {
        $penerimaan = DB::selectOne(
            'SELECT
                p.idpenerimaan,
                p.created_at,
                p.status,
                p.pengadaan_idpengadaan,
                p.user_iduser,
                v.nama_vendor,
                u.username
             FROM penerimaan p
             JOIN pengadaan pg
                ON p.pengadaan_idpengadaan = pg.idpengadaan
             JOIN vendor v
                ON pg.vendor_idvendor = v.idvendor
             JOIN `user` u
                ON p.user_iduser = u.iduser
             WHERE p.idpenerimaan = ?
             LIMIT 1',
            [$id]
        );

        if (! $penerimaan) {
            abort(404, 'Data penerimaan tidak ditemukan.');
        }

        $detail = DB::select(
            'SELECT
                dp.iddetail_penerimaan,
                dp.barang_idbarang,
                dp.jumlah_terima,
                dp.harga_satuan_terima,
                dp.sub_total_terima,
                b.nama
             FROM detail_penerimaan dp
             JOIN barang b
                ON dp.barang_idbarang = b.idbarang
             WHERE dp.penerimaan_idpenerimaan = ?
             ORDER BY dp.iddetail_penerimaan ASC',
            [$id]
        );

        $totalPenerimaan = 0;

        foreach ($detail as $item) {
            $totalPenerimaan += (int) $item->sub_total_terima;
        }

        return view(
            'admin.penerimaan.detail',
            compact('penerimaan', 'detail', 'totalPenerimaan')
        );
    }
}
