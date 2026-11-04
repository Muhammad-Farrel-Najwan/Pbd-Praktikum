<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReturController extends Controller
{
    // Menampilkan data retur
    public function dataRetur()
    {
        $retur = DB::select(
            'SELECT
                r.idretur,
                r.created_at,
                r.status,
                p.idpenerimaan,
                v.nama_vendor,
                u.username,
                COUNT(dr.iddetail_retur) AS jumlah_detail,
                COALESCE(SUM(dr.jumlah), 0) AS total_barang
             FROM retur r
             JOIN penerimaan p
                ON p.idpenerimaan = r.penerimaan_idpenerimaan
             JOIN pengadaan pg
                ON pg.idpengadaan = p.pengadaan_idpengadaan
             JOIN vendor v
                ON v.idvendor = pg.vendor_idvendor
             JOIN `user` u
                ON u.iduser = r.user_iduser
             LEFT JOIN detail_retur dr
                ON dr.retur_idretur = r.idretur
             GROUP BY
                r.idretur, r.created_at, r.status,
                p.idpenerimaan, v.nama_vendor, u.username
             ORDER BY r.idretur DESC'
        );

        return view('admin.retur.index', compact('retur'));
    }

    // Form tambah retur
    public function createRetur(Request $request)
    {
        // Hanya tampilkan penerimaan selesai yang masih punya sisa barang
        $penerimaan = DB::select(
            'SELECT
                p.idpenerimaan,
                p.created_at,
                v.nama_vendor
             FROM penerimaan p
             JOIN pengadaan pg
                ON pg.idpengadaan = p.pengadaan_idpengadaan
             JOIN vendor v
                ON v.idvendor = pg.vendor_idvendor
             WHERE p.status = ?
               AND EXISTS (
                   SELECT 1
                   FROM detail_penerimaan dp
                   WHERE dp.penerimaan_idpenerimaan = p.idpenerimaan
                     AND dp.jumlah_terima > COALESCE((
                         SELECT SUM(dr.jumlah)
                         FROM detail_retur dr
                         WHERE dr.detail_penerimaan_iddetail_penerimaan =
                               dp.iddetail_penerimaan
                     ), 0)
               )
             ORDER BY p.idpenerimaan DESC',
            ['S']
        );

        $idpenerimaan = $request->integer('penerimaan_idpenerimaan');
        $penerimaanTerpilih = null;
        $detailPenerimaan = [];

        // Mengambil detail berdasarkan penerimaan yang dipilih
        if ($idpenerimaan > 0) {
            $penerimaanTerpilih = DB::selectOne(
                'SELECT
                    p.idpenerimaan,
                    p.created_at,
                    v.nama_vendor
                 FROM penerimaan p
                 JOIN pengadaan pg
                    ON pg.idpengadaan = p.pengadaan_idpengadaan
                 JOIN vendor v
                    ON v.idvendor = pg.vendor_idvendor
                 WHERE p.idpenerimaan = ?
                   AND p.status = ?
                 LIMIT 1',
                [
                    $idpenerimaan,
                    'S',
                ]
            );

            if (! $penerimaanTerpilih) {
                return redirect()
                    ->route('admin.retur.create')
                    ->with('error', 'Penerimaan tidak ditemukan atau belum selesai.');
            }

            $detailPenerimaan = DB::select(
                'SELECT
                    dp.iddetail_penerimaan,
                    dp.barang_idbarang,
                    b.nama,
                    dp.jumlah_terima,
                    COALESCE(SUM(dr.jumlah), 0) AS sudah_diretur,
                    dp.jumlah_terima - COALESCE(SUM(dr.jumlah), 0) AS sisa
                 FROM detail_penerimaan dp
                 JOIN barang b
                    ON b.idbarang = dp.barang_idbarang
                 LEFT JOIN detail_retur dr
                    ON dr.detail_penerimaan_iddetail_penerimaan =
                       dp.iddetail_penerimaan
                 WHERE dp.penerimaan_idpenerimaan = ?
                 GROUP BY
                    dp.iddetail_penerimaan,
                    dp.barang_idbarang,
                    b.nama,
                    dp.jumlah_terima
                 HAVING sisa > 0
                 ORDER BY dp.iddetail_penerimaan ASC',
                [$idpenerimaan]
            );
        }

        return view(
            'admin.retur.create',
            compact(
                'penerimaan',
                'penerimaanTerpilih',
                'detailPenerimaan',
                'idpenerimaan'
            )
        );
    }

    // Menyimpan retur
    public function storeRetur(Request $request)
    {
        $validated = $request->validate([
            'penerimaan_idpenerimaan' => 'required|integer|min:1',
            'detail_penerimaan_iddetail_penerimaan' => 'required|array|min:1',
            'detail_penerimaan_iddetail_penerimaan.*' => 'required|integer|distinct|min:1',
            'jumlah' => 'required|array',
            'jumlah.*' => 'required|integer|min:0',
            'alasan' => 'nullable|array',
            'alasan.*' => 'nullable|string|max:200',
        ]);

        if (
            count($validated['detail_penerimaan_iddetail_penerimaan'])
            !== count($validated['jumlah'])
        ) {
            return back()
                ->withInput()
                ->with('error', 'Data barang dan jumlah retur tidak sesuai.');
        }

        $idpenerimaan = (int) $validated['penerimaan_idpenerimaan'];
        $iduser = (int) $request->session()->get('iduser');

        DB::beginTransaction();

        try {
            // Kunci penerimaan agar tidak berubah saat retur dibuat
            $penerimaan = DB::selectOne(
                'SELECT idpenerimaan
                 FROM penerimaan
                 WHERE idpenerimaan = ?
                   AND status = ?
                 LIMIT 1
                 FOR UPDATE',
                [
                    $idpenerimaan,
                    'S',
                ]
            );

            if (! $penerimaan) {
                throw new \Exception('Penerimaan tidak ditemukan atau belum selesai.');
            }

            $idDetail = $validated['detail_penerimaan_iddetail_penerimaan'];
            $jumlah = $validated['jumlah'];
            $alasan = $validated['alasan'] ?? [];

            $detailRetur = [];

            foreach ($idDetail as $index => $id) {
                $qty = (int) ($jumlah[$index] ?? 0);

                if ($qty <= 0) {
                    continue;
                }

                // Pastikan detail memang milik penerimaan yang dipilih
                $detail = DB::selectOne(
                    'SELECT
                        dp.iddetail_penerimaan,
                        dp.jumlah_terima,
                        dp.penerimaan_idpenerimaan,
                        b.nama,
                        COALESCE((
                            SELECT SUM(dr.jumlah)
                            FROM detail_retur dr
                            WHERE dr.detail_penerimaan_iddetail_penerimaan =
                                  dp.iddetail_penerimaan
                        ), 0) AS sudah_diretur
                     FROM detail_penerimaan dp
                     JOIN barang b
                        ON b.idbarang = dp.barang_idbarang
                     WHERE dp.iddetail_penerimaan = ?
                       AND dp.penerimaan_idpenerimaan = ?
                     LIMIT 1
                     FOR UPDATE',
                    [
                        $id,
                        $idpenerimaan,
                    ]
                );

                if (! $detail) {
                    throw new \Exception('Detail barang tidak sesuai dengan penerimaan.');
                }

                $sisa = (int) $detail->jumlah_terima - (int) $detail->sudah_diretur;

                if ($qty > $sisa) {
                    throw new \Exception(
                        'Jumlah retur untuk '.$detail->nama.
                        ' melebihi sisa yang dapat diretur ('.$sisa.').'
                    );
                }

                $detailRetur[] = [
                    'id' => (int) $id,
                    'jumlah' => $qty,
                    'alasan' => $alasan[$index] ?? null,
                ];
            }

            if (empty($detailRetur)) {
                throw new \Exception('Masukkan minimal satu jumlah barang retur.');
            }

            // Membuat header retur
            DB::insert(
                'INSERT INTO retur
                (
                    created_at,
                    penerimaan_idpenerimaan,
                    user_iduser,
                    status
                )
                VALUES (NOW(), ?, ?, ?)',
                [
                    $idpenerimaan,
                    $iduser,
                    'P',
                ]
            );

            $idretur = (int) DB::getPdo()->lastInsertId();

            // Menyimpan detail retur
            foreach ($detailRetur as $item) {
                DB::insert(
                    'INSERT INTO detail_retur
                    (
                        jumlah,
                        alasan,
                        detail_penerimaan_iddetail_penerimaan,
                        retur_idretur
                    )
                    VALUES (?, ?, ?, ?)',
                    [
                        $item['jumlah'],
                        $item['alasan'],
                        $item['id'],
                        $idretur,
                    ]
                );
            }

            DB::commit();

            return redirect()
                ->route('admin.retur.detail', $idretur)
                ->with('success', 'Retur berhasil dibuat. Silakan proses jika data sudah benar.');
        } catch (Throwable $e) {
            DB::rollBack();

            return redirect()
                ->route('admin.retur.create', [
                    'penerimaan_idpenerimaan' => $idpenerimaan,
                ])
                ->withInput()
                ->with('error', 'Gagal menyimpan retur: '.$e->getMessage());
        }
    }

    // Menampilkan detail retur
    public function detailRetur($id)
    {
        $retur = DB::selectOne(
            'SELECT
                r.idretur,
                r.created_at,
                r.status,
                p.idpenerimaan,
                v.nama_vendor,
                u.username
             FROM retur r
             JOIN penerimaan p
                ON p.idpenerimaan = r.penerimaan_idpenerimaan
             JOIN pengadaan pg
                ON pg.idpengadaan = p.pengadaan_idpengadaan
             JOIN vendor v
                ON v.idvendor = pg.vendor_idvendor
             JOIN `user` u
                ON u.iduser = r.user_iduser
             WHERE r.idretur = ?
             LIMIT 1',
            [$id]
        );

        if (! $retur) {
            abort(404, 'Data retur tidak ditemukan.');
        }

        $detail = DB::select(
            'SELECT
                dr.iddetail_retur,
                dr.jumlah,
                dr.alasan,
                b.nama,
                dp.jumlah_terima
             FROM detail_retur dr
             JOIN detail_penerimaan dp
                ON dp.iddetail_penerimaan =
                   dr.detail_penerimaan_iddetail_penerimaan
             JOIN barang b
                ON b.idbarang = dp.barang_idbarang
             WHERE dr.retur_idretur = ?
             ORDER BY dr.iddetail_retur ASC',
            [$id]
        );

        $totalBarang = 0;

        foreach ($detail as $item) {
            $totalBarang += (int) $item->jumlah;
        }

        return view(
            'admin.retur.detail',
            compact('retur', 'detail', 'totalBarang')
        );
    }

    // Memproses retur dan mengurangi stok lewat prosedur + trigger
    public function prosesRetur($id)
    {
        $idretur = (int) $id;

        DB::beginTransaction();

        try {
            DB::statement(
                'CALL sp_proses_retur(?)',
                [$idretur]
            );

            DB::commit();

            return redirect()
                ->route('admin.retur.detail', $idretur)
                ->with('success', 'Retur berhasil diselesaikan dan stok telah diperbarui.');
        } catch (Throwable $e) {
            DB::rollBack();

            return back()->with(
                'error',
                'Gagal memproses retur: '.$e->getMessage()
            );
        }
    }
}
