<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class kasirController extends Controller
{
    private const PPN_RATE = 0.11;

    public function index()
    {
        $iduser = (int) session('iduser');

        // Total transaksi hari ini
        $totalTransaksi = DB::selectOne(
            'SELECT COUNT(*) AS total
            FROM penjualan
            WHERE DATE(created_at) = CURDATE()
            AND user_iduser = ?',
            [$iduser]
        );

        // Total pendapatan hari ini
        $totalPendapatan = DB::selectOne(
            'SELECT COALESCE(SUM(total_nilai), 0) AS total
            FROM penjualan
            WHERE DATE(created_at) = CURDATE()
            AND user_iduser = ?',
            [$iduser]
        );

        // Margin penjualan yang sedang aktif
        $marginAktif = DB::selectOne(
            'SELECT idmargin_penjualan,
            persen
            FROM margin_penjualan
            WHERE status = 1
            LIMIT 1'
        );

        // Riwayat transaksi terbaru milik kasir yang sedang login
        $riwayatTransaksi = DB::select(
            'SELECT idpenjualan,
            created_at,
            subtotal_nilai,
            ppn,
            total_nilai
            FROM penjualan
            WHERE user_iduser = ?
            ORDER BY created_at DESC
            LIMIT 5',
            [$iduser]
        );

        return view('kasir.dashboard', compact(
            'totalTransaksi',
            'totalPendapatan',
            'marginAktif',
            'riwayatTransaksi'
        ));
    }

    // Daftar transaksi penjualan
    public function dataPenjualan()
    {
        $penjualans = DB::select(
            'SELECT p.idpenjualan,
            p.created_at,
            p.subtotal_nilai,
            p.ppn,
            p.total_nilai,
            u.username AS kasir
            FROM penjualan AS p
            JOIN user AS u ON u.iduser = p.user_iduser
            ORDER BY p.created_at DESC
            LIMIT 50'
        );

        return view('kasir.penjualan.index', [
            'penjualans' => $penjualans,
        ]);
    }

    // Form transaksi penjualan baru
    public function createPenjualan()
    {
        $barang = DB::select(
            'SELECT idbarang,
            nama,
            harga
            FROM barang
            WHERE status = 1
            ORDER BY nama ASC'
        );

        $margin = DB::selectOne(
            'SELECT idmargin_penjualan,
            persen
            FROM margin_penjualan
            WHERE status = 1
            LIMIT 1'
        );

        return view('kasir.penjualan.create', compact('barang', 'margin'));
    }

    // Simpan transaksi penjualan
    public function storePenjualan(Request $request)
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1', 'max:100'],
            'items.*.idbarang' => ['required', 'integer', 'min:1'],
            'items.*.jumlah' => ['required', 'integer', 'min:1', 'max:100000'],
        ]);

        // Gabungkan baris dengan barang yang sama agar tidak ada detail ganda
        $items = collect($validated['items'])
            ->groupBy('idbarang')
            ->map(function (array $rows, $idbarang) {
                return [
                    'idbarang' => (int) $idbarang,
                    'jumlah' => (int) $rows->sum('jumlah'),
                ];
            })
            ->values()
            ->all();

        foreach ($items as $item) {
            if ($item['jumlah'] > 100000) {
                throw ValidationException::withMessages([
                    'items' => 'Jumlah barang melebihi batas yang diizinkan.',
                ]);
            }
        }

        $iduser = (int) session('iduser');

        DB::transaction(function () use ($items, $iduser) {
            // Kunci margin aktif agar nilainya tidak berubah di tengah transaksi
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

            $placeholders = implode(',', array_fill(0, count($items), '?'));
            $ids = array_column($items, 'idbarang');

            $barangs = DB::select(
                "SELECT idbarang,
                nama,
                harga
                FROM barang
                WHERE status = 1
                AND idbarang IN ($placeholders)",
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
                $harga = (int) $barang->harga;
                $jumlah = (int) $item['jumlah'];
                $subtotalBaris = $harga * $jumlah;

                $subtotal += $subtotalBaris;

                $detail[] = [
                    'idbarang' => (int) $barang->idbarang,
                    'harga_satuan' => $harga,
                    'jumlah' => $jumlah,
                    'subtotal' => $subtotalBaris,
                ];
            }

            $nilaiMargin = (int) round($subtotal * ((float) $margin->persen / 100));
            $ppn = (int) round(($subtotal + $nilaiMargin) * self::PPN_RATE);
            $total = $subtotal + $nilaiMargin + $ppn;

            if ($total > 2147483647) {
                throw ValidationException::withMessages([
                    'items' => 'Total transaksi melebihi batas nilai yang dapat disimpan.',
                ]);
            }

            DB::insert(
                'INSERT INTO penjualan
                (created_at, subtotal_nilai, ppn, total_nilai, user_iduser, margin_penjualan_idmargin_penjualan)
                VALUES (?, ?, ?, ?, ?, ?)',
                [
                    now(),
                    $subtotal,
                    $ppn,
                    $total,
                    $iduser,
                    (int) $margin->idmargin_penjualan,
                ]
            );

            $idpenjualan = (int) DB::getPdo()->lastInsertId();

            foreach ($detail as $row) {
                DB::insert(
                    'INSERT INTO detail_penjualan
                    (harga_satuan, jumlah, subtotal, penjualan_idpenjualan, barang_idbarang)
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
        });

        return redirect()
            ->route('kasir.penjualan.index')
            ->with('success', 'Transaksi penjualan berhasil disimpan.');
    }
}
