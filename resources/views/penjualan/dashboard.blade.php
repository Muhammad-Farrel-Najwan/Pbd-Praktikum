<x-layouts.penjualan
    title="Dashboard Penjualan"
    breadcrumb="RedStock / <span>Dashboard Penjualan</span>"
    topbar-title="Panel Staff Penjualan"
>
    <x-ui.page-intro
        eyebrow="Sales Operations"
        title="Dashboard Penjualan"
        description="Kelola aktivitas penjualan barang di lingkungan RedStock."
        :metrics="[
            ['label' => 'Margin Aktif', 'value' => $marginAktif ? $marginAktif->persen . '%' : '—'],
            ['label' => 'Transaksi Hari Ini', 'value' => (int) $totalTransaksi->total],
            ['label' => 'Pendapatan Hari Ini', 'value' => 'Rp ' . number_format((float) $totalPendapatan->total, 0, ',', '.')],
        ]"
        :delay="40"
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('penjualan.penjualan.create') }}" icon="plus">
                Transaksi Baru
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-intro>

    @if (! $marginAktif)
        <x-ui.alert variant="error">
            Belum ada margin penjualan aktif. Hubungi admin sebelum membuat transaksi.
        </x-ui.alert>
    @endif

    <section aria-labelledby="summary-title">
        <div class="rs-section-head">
            <div>
                <h2 id="summary-title">Ringkasan Hari Ini</h2>
                <p>Performa transaksi yang Anda proses pada hari ini.</p>
            </div>
        </div>

        <div class="rs-stats">
            <x-ui.stat-card
                label="Transaksi Saya Hari Ini"
                :value="(int) $totalTransaksi->total"
                note="Jumlah transaksi yang diproses"
                icon="receipt"
                tone="blue"
                :delay="100"
            />

            <x-ui.stat-card
                label="Pendapatan Saya Hari Ini"
                value="Rp {{ number_format((float) $totalPendapatan->total, 0, ',', '.') }}"
                note="Total nilai penjualan hari ini"
                icon="chart"
                tone="green"
                :delay="160"
            />

            <x-ui.stat-card
                label="Margin Aktif"
                :value="$marginAktif?->persen ?? 0"
                :suffix="$marginAktif ? '%' : ''"
                note="Margin penjualan yang berlaku"
                icon="sliders"
                tone="amber"
                :delay="220"
            />
        </div>
    </section>

    <x-ui.panel
        title="Transaksi Terbaru"
        description="Lima transaksi terakhir yang Anda proses."
        :badge="count($riwayatTransaksi) . ' Transaksi'"
        :flush="true"
        class="rs-mt-panel"
    >
        @if (count($riwayatTransaksi) > 0)
            <div class="rs-table-wrap">
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th scope="col">ID</th>
                            <th scope="col">Waktu</th>
                            <th scope="col">Status</th>
                            <th scope="col" class="is-right">Total</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($riwayatTransaksi as $transaksi)
                            <tr>
                                <td>
                                    <span class="rs-mono">#{{ $transaksi->idpenjualan }}</span>
                                </td>

                                <td>
                                    <span class="rs-cell-muted">
                                        {{ $transaksi->created_at
                                            ? \Illuminate\Support\Carbon::parse($transaksi->created_at)->translatedFormat('d M Y, H:i')
                                            : '-' }}
                                    </span>
                                </td>

                                <td>
                                    @if ($transaksi->status === 'S')
                                        <x-ui.badge variant="success" dot>Selesai</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="warning" dot>Pending</x-ui.badge>
                                    @endif
                                </td>

                                <td class="is-right">
                                    <span class="rs-price">
                                        Rp {{ number_format((float) $transaksi->total_nilai, 0, ',', '.') }}
                                    </span>
                                </td>

                                <td>
                                    <div class="rs-table-actions">
                                        <x-ui.button
                                            href="{{ route('penjualan.penjualan.detail', $transaksi->idpenjualan) }}"
                                            variant="secondary"
                                            size="sm"
                                            icon="eye"
                                        >
                                            Detail
                                        </x-ui.button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <x-ui.empty-state
                icon="receipt"
                title="Belum ada transaksi"
                description="Transaksi yang Anda simpan akan muncul di sini."
            />
        @endif

        <x-slot:footer>
            <x-ui.button href="{{ route('penjualan.penjualan.index') }}" variant="secondary" size="sm" icon="clock">
                Lihat Semua
            </x-ui.button>
        </x-slot:footer>
    </x-ui.panel>

    <section aria-labelledby="modules-title">
        <div class="rs-section-head">
            <div>
                <h2 id="modules-title">Modul Penjualan</h2>
                <p>Modul penjualan yang sudah dapat digunakan.</p>
            </div>
        </div>

        <div class="rs-modules">
            <x-ui.module-card
                title="Transaksi Penjualan"
                description="Catat transaksi penjualan dan pantau status pemrosesannya."
                icon="receipt"
                status="available"
                :href="route('penjualan.penjualan.create')"
                :delay="100"
            />

            <x-ui.module-card
                title="Riwayat Penjualan"
                description="Telusuri riwayat transaksi penjualan yang telah tercatat."
                icon="clock"
                status="available"
                :href="route('penjualan.penjualan.index')"
                :delay="160"
            />
        </div>
    </section>
</x-layouts.penjualan>
