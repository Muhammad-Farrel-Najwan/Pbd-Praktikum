<x-layouts.gudang
    title="Dashboard Gudang"
    breadcrumb="RedStock / <span>Dashboard Gudang</span>"
    topbar-title="Panel Petugas Gudang"
>
    @php
        $antrian = (int) $siapDiterima->total + (int) $menungguApproval->total;
        $persenSiap = $antrian > 0 ? (int) round(((int) $siapDiterima->total / $antrian) * 100) : 0;
    @endphp

    <x-ui.page-intro
        eyebrow="Inventory Control"
        title="Dashboard Gudang"
        description="Pantau pergerakan stok dan kesiapan operasional gudang RedStock."
        :metrics="[
            ['label' => 'Penerimaan Hari Ini', 'value' => (int) $penerimaanHariIni->total],
            ['label' => 'Siap Diterima', 'value' => (int) $siapDiterima->total],
            ['label' => 'Stok Habis', 'value' => (int) $stokHabis->total],
        ]"
        :delay="40"
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('gudang.pengadaan.create') }}" variant="secondary" icon="plus">
                Pengadaan Baru
            </x-ui.button>

            <x-ui.button href="{{ route('gudang.penerimaan.create') }}" icon="truck">
                Terima Barang
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-intro>

    <section aria-labelledby="summary-title">
        <div class="rs-section-head">
            <div>
                <h2 id="summary-title">Ringkasan Operasional</h2>
                <p>Kondisi pengadaan, penerimaan, dan persediaan saat ini.</p>
            </div>
        </div>

        <div class="rs-stats">
            <x-ui.stat-card
                label="Menunggu Approval"
                :value="(int) $menungguApproval->total"
                note="Pengadaan menunggu persetujuan Manager"
                icon="check"
                tone="amber"
                :delay="100"
            />

            <x-ui.stat-card
                label="Siap Diterima"
                :value="(int) $siapDiterima->total"
                note="Pengadaan disetujui yang belum lengkap"
                icon="truck"
                tone="blue"
                :meter="$persenSiap"
                :meter-label="$persenSiap . '% antrian siap diterima'"
                :delay="160"
            />

            <x-ui.stat-card
                label="Penerimaan Hari Ini"
                :value="(int) $penerimaanHariIni->total"
                note="Transaksi penerimaan yang dicatat hari ini"
                icon="receipt"
                tone="green"
                :delay="220"
            />

            <x-ui.stat-card
                label="Stok Habis"
                :value="(int) $stokHabis->total"
                note="Barang aktif tanpa sisa stok"
                icon="box"
                tone="red"
                :delay="280"
            />
        </div>
    </section>

    <div class="rs-split">
        <x-ui.panel
            title="Pengadaan Terbaru"
            description="Lima pengadaan terakhir yang tercatat."
            :badge="count($pengadaanTerbaru) . ' Pengadaan'"
            :flush="true"
            class="rs-mt-panel"
        >
            @if (count($pengadaanTerbaru) > 0)
                <div class="rs-table-wrap">
                    <table class="rs-table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Vendor</th>
                                <th scope="col">Status</th>
                                <th scope="col" class="is-right">Total</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($pengadaanTerbaru as $item)
                                <tr>
                                    <td>
                                        <span class="rs-mono">#{{ $item->idpengadaan }}</span>
                                    </td>

                                    <td>
                                        <span class="rs-cell-primary">{{ $item->nama_vendor }}</span>
                                        <span class="rs-cell-sub">
                                            {{ $item->created_at
                                                ? \Illuminate\Support\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i')
                                                : '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($item->status === 'P')
                                            <x-ui.badge variant="warning" dot>Menunggu</x-ui.badge>
                                        @elseif ($item->status === 'S')
                                            <x-ui.badge variant="success" dot>Disetujui</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="muted">-</x-ui.badge>
                                        @endif
                                    </td>

                                    <td class="is-right">
                                        <span class="rs-price">
                                            Rp {{ number_format((float) $item->total_nilai, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="rs-table-actions">
                                            <x-ui.button
                                                href="{{ route('gudang.pengadaan.detail', $item->idpengadaan) }}"
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
                    icon="package"
                    title="Belum ada pengadaan"
                    description="Pengadaan yang Anda buat akan muncul di sini."
                />
            @endif

            <x-slot:footer>
                <x-ui.button href="{{ route('gudang.pengadaan.index') }}" variant="secondary" size="sm" icon="package">
                    Lihat Semua Pengadaan
                </x-ui.button>
            </x-slot:footer>
        </x-ui.panel>

        <x-ui.panel
            title="Penerimaan Terbaru"
            description="Lima penerimaan barang terakhir."
            :badge="count($penerimaanTerbaru) . ' Penerimaan'"
            :flush="true"
            class="rs-mt-panel"
        >
            @if (count($penerimaanTerbaru) > 0)
                <div class="rs-table-wrap">
                    <table class="rs-table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Vendor</th>
                                <th scope="col">Status</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($penerimaanTerbaru as $item)
                                <tr>
                                    <td>
                                        <span class="rs-mono">#{{ $item->idpenerimaan }}</span>
                                    </td>

                                    <td>
                                        <span class="rs-cell-primary">{{ $item->nama_vendor }}</span>
                                        <span class="rs-cell-sub">
                                            {{ $item->created_at
                                                ? \Illuminate\Support\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i')
                                                : '-' }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ($item->status === 'S')
                                            <x-ui.badge variant="success" dot>Selesai</x-ui.badge>
                                        @else
                                            <x-ui.badge variant="warning" dot>Pending</x-ui.badge>
                                        @endif
                                    </td>

                                    <td>
                                        <div class="rs-table-actions">
                                            <x-ui.button
                                                href="{{ route('gudang.penerimaan.detail', $item->idpenerimaan) }}"
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
                    icon="truck"
                    title="Belum ada penerimaan"
                    description="Penerimaan barang yang Anda catat akan muncul di sini."
                />
            @endif

            <x-slot:footer>
                <x-ui.button href="{{ route('gudang.penerimaan.index') }}" variant="secondary" size="sm" icon="truck">
                    Lihat Semua Penerimaan
                </x-ui.button>
            </x-slot:footer>
        </x-ui.panel>
    </div>

    <section aria-labelledby="modules-title">
        <div class="rs-section-head">
            <div>
                <h2 id="modules-title">Modul Gudang</h2>
                <p>Modul inventori yang sudah dapat digunakan.</p>
            </div>
        </div>

        <div class="rs-modules">
            <x-ui.module-card
                title="Pengadaan Barang"
                description="Ajukan permintaan pengadaan saat persediaan menipis."
                icon="package"
                status="available"
                :href="route('gudang.pengadaan.index')"
                :delay="100"
            />

            <x-ui.module-card
                title="Penerimaan Barang"
                description="Catat barang masuk dari vendor beserta jumlah dan kondisi fisiknya."
                icon="truck"
                status="available"
                :href="route('gudang.penerimaan.index')"
                :delay="160"
            />

            <x-ui.module-card
                title="Kartu Stok"
                description="Pantau mutasi stok masuk dan keluar untuk setiap barang."
                icon="layers"
                status="available"
                :href="route('gudang.stok.index')"
                :delay="220"
            />
        </div>
    </section>
</x-layouts.gudang>
