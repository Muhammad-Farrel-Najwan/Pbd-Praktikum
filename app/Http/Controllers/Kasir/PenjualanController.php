<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Throwable;

class PenjualanController extends Controller
{
    // Menampilkan seluruh data penjualan
    public function dataPenjualan()
    {
        $penjualan = DB::select(
            'SELECT
                p.idpenjualan,
                p.created_at,
                p.subtotal_nilai,
                p.ppn,
                p.total_nilai,
                p.status,
                u.username AS kasir
             FROM penjualan p
             JOIN `user` u
                ON p.user_iduser = u.iduser
             ORDER BY p.created_at DESC
             LIMIT 50'
        );

        return view('kasir.penjualan.index', compact('penjualan'));
    }

    // Form tambah penjualan
    public function createPenjualan()
    {
        // Margin penjualan yang sedang aktif
        $margin = DB::selectOne(
            'SELECT idmargin_penjualan,
                persen
             FROM margin_penjualan
             WHERE status = 1
             LIMIT 1'
        );

        $barang = [];

        // Ambil barang aktif beserta sisa stoknya
        if ($margin) {
            $barang = DB::select(
                'SELECT
                    b.idbarang,
                    b.nama,
                    b.harga,
                    fn_hitung_harga_jual(b.harga) AS harga_jual,
                    COALESCE((
                        SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
                        FROM kartu_stok AS ks
                        WHERE ks.barang_idbarang = b.idbarang
                    ), 0) AS stok
                 FROM barang AS b
                 WHERE b.status = 1
                 ORDER BY b.nama ASC'
            );
        }

        return view('kasir.penjualan.create', compact('barang', 'margin'));
    }

    // Menyimpan penjualan
    public function storePenjualan(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array|min:1',
            'items.*.idbarang' => 'required|integer|min:1',
            'items.*.jumlah' => 'required|integer|min:1|max:100000',
        ]);

        // Gabungkan barang yang sama biar tidak ada detail ganda
        $items = [];

        foreach ($validated['items'] as $input) {
            $idbarang = (int) $input['idbarang'];
            $jumlah = (int) $input['jumlah'];

            if (isset($items[$idbarang])) {
                $items[$idbarang]['jumlah'] += $jumlah;

                continue;
            }

            $items[$idbarang] = [
                'idbarang' => $idbarang,
                'jumlah' => $jumlah,
            ];
        }

        $items = array_values($items);

        $iduser = (int) session('iduser');

        DB::beginTransaction();

        try {
            // Kunci margin aktif biar nilainya tidak berubah di tengah transaksi
            $margin = DB::selectOne(
                'SELECT idmargin_penjualan,
                    persen
                 FROM margin_penjualan
                 WHERE status = 1
                 LIMIT 1
                 FOR UPDATE'
            );

            if (! $margin) {
                throw ValidationException::withMessages([
                    'items' => 'Belum ada margin penjualan aktif. Hubungi admin terlebih dahulu.',
                ]);
            }

            $ids = array_column($items, 'idbarang');
            $placeholders = implode(',', array_fill(0, count($items), '?'));

            // Kunci barang biar stoknya tidak dijual bersamaan oleh transaksi lain
            DB::select(
                "SELECT idbarang
                 FROM barang
                 WHERE idbarang IN ($placeholders)
                 ORDER BY idbarang
                 FOR UPDATE",
                $ids
            );

            $barangs = DB::select(
                "SELECT
                    b.idbarang,
                    b.nama,
                    b.harga,
                    fn_hitung_harga_jual(b.harga) AS harga_jual
                 FROM barang AS b
                 WHERE b.status = 1
                   AND b.idbarang IN ($placeholders)",
                $ids
            );

            $barangById = collect($barangs)->keyBy('idbarang');

            if ($barangById->count() !== count($items)) {
                throw ValidationException::withMessages([
                    'items' => 'Salah satu barang tidak ditemukan atau sudah tidak aktif.',
                ]);
            }

            $subtotal = 0;
            $detail = [];

            foreach ($items as $item) {
                $barang = $barangById->get($item['idbarang']);
                $hargaJual = (int) round((float) $barang->harga_jual);
                $jumlah = (int) $item['jumlah'];
                $subtotalBaris = $hargaJual * $jumlah;

                $subtotal += $subtotalBaris;

                $detail[] = [
                    'idbarang' => (int) $barang->idbarang,
                    'harga_satuan' => $hargaJual,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotalBaris,
                ];
            }

            // PPN 11%
            $ppn = (int) round($subtotal * 0.11);
            $total = $subtotal + $ppn;

            if ($total > 2147483647) {
                throw ValidationException::withMessages([
                    'items' => 'Total transaksi melebihi batas nilai yang dapat disimpan.',
                ]);
            }

            // Simpan header penjualan
            DB::insert(
                'INSERT INTO penjualan
                (
                    created_at,
                    subtotal_nilai,
                    ppn,
                    total_nilai,
                    user_iduser,
                    margin_penjualan_idmargin_penjualan,
                    status
                )
                VALUES (NOW(), ?, ?, ?, ?, ?, ?)',
                [
                    $subtotal,
                    $ppn,
                    $total,
                    $iduser,
                    (int) $margin->idmargin_penjualan,
                    'P',
                ]
            );

            $idpenjualan = (int) DB::getPdo()->lastInsertId();

            // Simpan detail penjualan
            foreach ($detail as $row) {
                DB::insert(
                    'INSERT INTO detail_penjualan
                    (
                        harga_satuan,
                        jumlah,
                        subtotal,
                        penjualan_idpenjualan,
                        barang_idbarang
                    )
                    VALUES (?, ?, ?, ?, ?)',
                    [
                        $row['harga_satuan'],
                        $row['jumlah'],
                        $row['subtotal'],
                        $idpenjualan,
                        $row['idbarang'],
                    ]
                );
            }

            // Selesaikan penjualan, stok keluar dicatat oleh trigger
            DB::statement('CALL sp_selesai_penjualan(?)', [$idpenjualan]);

            DB::commit();

            return redirect()
                ->route('kasir.penjualan.index')
                ->with('success', 'Transaksi penjualan berhasil disimpan.');
        } catch (ValidationException $e) {
            DB::rollBack();

            throw $e;
        } catch (Throwable $e) {
            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', 'Gagal menyimpan transaksi: '.$e->getMessage());
        }
    }

    // Menampilkan detail transaksi penjualan
    public function detailPenjualan($id)
    {
        $penjualan = DB::selectOne(
            'SELECT
                p.idpenjualan,
                p.created_at,
                p.subtotal_nilai,
                p.ppn,
                p.total_nilai,
                p.status,
                u.username AS kasir,
                m.persen AS margin_persen
             FROM penjualan p
             JOIN `user` u
                ON p.user_iduser = u.iduser
             LEFT JOIN margin_penjualan m
                ON p.margin_penjualan_idmargin_penjualan = m.idmargin_penjualan
             WHERE p.idpenjualan = ?
             LIMIT 1',
            [$id]
        );

        if (! $penjualan) {
            abort(404, 'Data penjualan tidak ditemukan.');
        }

        $detail = DB::select(
            'SELECT
                dp.iddetail_penjualan,
                dp.harga_satuan,
                dp.jumlah,
                dp.subtotal,
                b.nama AS nama_barang,
                s.nama_satuan
             FROM detail_penjualan dp
             JOIN barang b
                ON dp.barang_idbarang = b.idbarang
             LEFT JOIN satuan s
                ON b.satuan_idsatuan = s.idsatuan
             WHERE dp.penjualan_idpenjualan = ?
             ORDER BY dp.iddetail_penjualan ASC',
            [$id]
        );

        return view('kasir.penjualan.detail', compact('penjualan', 'detail'));
    }

    // Menampilkan halaman struk yang siap dicetak
    public function strukPenjualan($id)
    {
        $penjualan = DB::selectOne(
            'SELECT
                p.idpenjualan,
                p.created_at,
                p.subtotal_nilai,
                p.ppn,
                p.total_nilai,
                p.status,
                u.username AS kasir,
                m.persen AS margin_persen
             FROM penjualan p
             JOIN `user` u
                ON p.user_iduser = u.iduser
             LEFT JOIN margin_penjualan m
                ON p.margin_penjualan_idmargin_penjualan = m.idmargin_penjualan
             WHERE p.idpenjualan = ?
             LIMIT 1',
            [$id]
        );

        if (! $penjualan) {
            abort(404, 'Data penjualan tidak ditemukan.');
        }

        $detail = DB::select(
            'SELECT
                dp.iddetail_penjualan,
                dp.harga_satuan,
                dp.jumlah,
                dp.subtotal,
                b.nama AS nama_barang,
                s.nama_satuan
             FROM detail_penjualan dp
             JOIN barang b
                ON dp.barang_idbarang = b.idbarang
             LEFT JOIN satuan s
                ON b.satuan_idsatuan = s.idsatuan
             WHERE dp.penjualan_idpenjualan = ?
             ORDER BY dp.iddetail_penjualan ASC',
            [$id]
        );

        return view('kasir.penjualan.struk', compact('penjualan', 'detail'));
    }
}
