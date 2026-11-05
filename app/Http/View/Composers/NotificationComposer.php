<?php

namespace App\Http\View\Composers;

use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class NotificationComposer
{
    /**
     * Notification groups cached for the current request.
     *
     * @var array<string, array<int, array<string, mixed>>>
     */
    private static array $cache = [];

    public function compose(View $view): void
    {
        $role = (string) session('role_slug', '');

        $view->with([
            'notificationGroups' => $this->groupsForRole($role),
            'notificationsEnabled' => $role === 'admin',
        ]);
    }

    /**
     * Build the pending-action notifications for the given role.
     *
     * @return array<int, array<string, mixed>>
     */
    private function groupsForRole(string $role): array
    {
        if ($role !== 'admin') {
            return [];
        }

        if (isset(self::$cache[$role])) {
            return self::$cache[$role];
        }

        $counts = DB::selectOne(
            'SELECT
                (SELECT COUNT(*) FROM retur WHERE status = ?) AS retur_pending,
                (SELECT COUNT(*) FROM penerimaan WHERE status = ?) AS penerimaan_pending,
                (SELECT COUNT(*) FROM penjualan WHERE status = ?) AS penjualan_pending,
                (SELECT COUNT(*) FROM pengadaan pg
                    WHERE NOT EXISTS (
                        SELECT 1
                        FROM penerimaan p
                        WHERE p.pengadaan_idpengadaan = pg.idpengadaan
                    )
                ) AS pengadaan_pending,
                (SELECT COUNT(*) FROM barang b
                    WHERE b.status = 1
                      AND COALESCE((
                          SELECT SUM(COALESCE(ks.masuk, 0) - COALESCE(ks.keluar, 0))
                          FROM kartu_stok ks
                          WHERE ks.barang_idbarang = b.idbarang
                      ), 0) <= 0
                ) AS stok_habis,
                (SELECT COUNT(*) FROM margin_penjualan WHERE status = 1) AS margin_aktif',
            ['P', 'P', 'P']
        );

        $groups = [];

        if ((int) $counts->retur_pending > 0) {
            $groups[] = [
                'key' => 'retur',
                'icon' => 'rotate',
                'tone' => 'warning',
                'title' => 'Retur menunggu validasi',
                'description' => $counts->retur_pending.' retur perlu diproses',
                'count' => (int) $counts->retur_pending,
                'url' => route('admin.dashboard.retur'),
            ];
        }

        if ((int) $counts->penerimaan_pending > 0) {
            $groups[] = [
                'key' => 'penerimaan',
                'icon' => 'truck',
                'tone' => 'info',
                'title' => 'Penerimaan belum selesai',
                'description' => $counts->penerimaan_pending.' penerimaan perlu diselesaikan',
                'count' => (int) $counts->penerimaan_pending,
                'url' => route('admin.dashboard.penerimaan'),
            ];
        }

        if ((int) $counts->penjualan_pending > 0) {
            $groups[] = [
                'key' => 'penjualan',
                'icon' => 'receipt',
                'tone' => 'info',
                'title' => 'Penjualan belum selesai',
                'description' => $counts->penjualan_pending.' transaksi perlu diselesaikan',
                'count' => (int) $counts->penjualan_pending,
                'url' => route('admin.penjualan.index'),
            ];
        }

        if ((int) $counts->pengadaan_pending > 0) {
            $groups[] = [
                'key' => 'pengadaan',
                'icon' => 'package',
                'tone' => 'info',
                'title' => 'Pengadaan belum diterima',
                'description' => $counts->pengadaan_pending.' pengadaan menunggu penerimaan',
                'count' => (int) $counts->pengadaan_pending,
                'url' => route('admin.dashboard.pengadaan'),
            ];
        }

        if ((int) $counts->stok_habis > 0) {
            $groups[] = [
                'key' => 'stok',
                'icon' => 'box',
                'tone' => 'danger',
                'title' => 'Stok barang habis',
                'description' => $counts->stok_habis.' barang aktif tidak bisa dijual',
                'count' => (int) $counts->stok_habis,
                'url' => route('admin.dashboard.stok'),
            ];
        }

        if ((int) $counts->margin_aktif === 0) {
            $groups[] = [
                'key' => 'margin',
                'icon' => 'sliders',
                'tone' => 'danger',
                'title' => 'Margin penjualan belum aktif',
                'description' => 'Transaksi kasir belum bisa dilakukan.',
                'count' => 1,
                'url' => route('admin.dashboard').'#pengaturan-margin',
            ];
        }

        return self::$cache[$role] = $groups;
    }
}
