<x-layouts.manager
    title="Dashboard Manager"
    breadcrumb="RedStock / <span>Dashboard Manager</span>"
    topbar-title="Panel Manager"
>
    <x-ui.page-intro
        eyebrow="Business Intelligence"
        title="Dashboard Manager"
        description="Pantau performa penjualan dan kesehatan persediaan RedStock dari satu panel."
        :metrics="[
            ['label' => 'Margin Aktif', 'value' => $marginAktif ? $marginAktif->persen . '%' : '—'],
            ['label' => 'Pengadaan Pending', 'value' => (int) $menungguApproval->total],
            ['label' => 'Periode', 'value' => now()->translatedFormat('F Y')],
        ]"
        :delay="40"
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('manager.laporan.penjualan') }}" variant="secondary" icon="chart">
                Laporan Penjualan
            </x-ui.button>

            <x-ui.button href="{{ route('manager.approval.index') }}" icon="check">
                Approval
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-intro>

    <section aria-labelledby="summary-title">
        <div class="rs-section-head">
            <div>
                <h2 id="summary-title">Ringkasan Bulan Ini</h2>
                <p>Performa penjualan dan kondisi persediaan saat ini.</p>
            </div>
        </div>

        <div class="rs-stats">
            <x-ui.stat-card
                label="Omzet Bulan Ini"
                value="Rp {{ number_format((float) $omzetBulanIni->total, 0, ',', '.') }}"
                note="Total penjualan berstatus selesai"
                icon="chart"
                tone="green"
                :delay="100"
            />

            <x-ui.stat-card
                label="Transaksi Bulan Ini"
                :value="(int) $transaksiBulanIni->total"
                note="Jumlah transaksi penjualan selesai"
                icon="receipt"
                tone="blue"
                :delay="160"
            />

            <x-ui.stat-card
                label="Menunggu Approval"
                :value="(int) $menungguApproval->total"
                note="Pengadaan yang perlu ditinjau"
                icon="check"
                tone="amber"
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
            title="Pengadaan Menunggu Approval"
            description="Lima pengadaan terbaru yang menunggu persetujuan Manager."
            :badge="count($pengadaanMenunggu) . ' Pengadaan'"
            :flush="true"
            class="rs-mt-panel"
        >
            @if (count($pengadaanMenunggu) > 0)
                <div class="rs-table-wrap">
                    <table class="rs-table">
                        <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Vendor</th>
                                <th scope="col">Petugas</th>
                                <th scope="col" class="is-right">Total</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @foreach ($pengadaanMenunggu as $item)
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
                                        <span class="rs-cell-muted">{{ $item->username }}</span>
                                    </td>

                                    <td class="is-right">
                                        <span class="rs-price">
                                            Rp {{ number_format((float) $item->total_nilai, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    <td>
                                        <div class="rs-table-actions">
                                            <x-ui.button
                                                href="{{ route('manager.approval.detail', $item->idpengadaan) }}"
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
                    icon="check"
                    title="Tidak ada pengadaan menunggu"
                    description="Semua pengadaan sudah ditinjau atau belum ada pengajuan baru."
                />
            @endif

            <x-slot:footer>
                <x-ui.button href="{{ route('manager.approval.index', ['status' => 'P']) }}" variant="secondary" size="sm" icon="check">
                    Lihat Semua Approval
                </x-ui.button>
            </x-slot:footer>
        </x-ui.panel>

        <x-ui.panel
            title="Stok Teratas"
            description="Enam barang dengan sisa stok terbanyak."
            :badge="count($stokTeratas) . ' Barang'"
            class="rs-mt-panel"
        >
            @if (count($stokTeratas) > 0)
                @php
                    $maxSisa = max(1, (int) collect($stokTeratas)->max('sisa'));
                @endphp

                <div class="rs-bars">
                    @foreach ($stokTeratas as $row)
                        @php
                            $sisa = max(0, (int) $row->sisa);
                            $lebar = max(2, (int) round(($sisa / $maxSisa) * 100));
                        @endphp

                        <div class="rs-bar-row">
                            <span class="rs-bar-label" title="{{ $row->nama }}">{{ $row->nama }}</span>
                            <span class="rs-bar-track">
                                <span class="rs-bar-fill" style="--rs-bar: {{ $lebar }}%"></span>
                            </span>
                            <span class="rs-bar-value">{{ $sisa }}</span>
                        </div>
                    @endforeach
                </div>
            @else
                <x-ui.empty-state
                    icon="layers"
                    title="Belum ada data stok"
                    description="Data stok akan muncul setelah barang diterima."
                />
            @endif
        </x-ui.panel>
    </div>

    <section aria-labelledby="modules-title">
        <div class="rs-section-head">
            <div>
                <h2 id="modules-title">Modul Manager</h2>
                <p>Modul analitik yang sudah dapat digunakan.</p>
            </div>
        </div>

        <div class="rs-modules">
            <x-ui.module-card
                title="Laporan Penjualan"
                description="Lihat rekap omzet dan performa penjualan dalam periode tertentu."
                icon="chart"
                status="available"
                :href="route('manager.laporan.penjualan')"
                :delay="100"
            />

            <x-ui.module-card
                title="Laporan Stok"
                description="Pantau nilai persediaan serta barang yang mendekati batas minimum."
                icon="database"
                status="available"
                :href="route('manager.laporan.stok')"
                :delay="160"
            />

            <x-ui.module-card
                title="Approval Pengadaan"
                description="Tinjau dan setujui permintaan pengadaan dari petugas gudang."
                icon="check"
                status="available"
                :href="route('manager.approval.index')"
                :delay="220"
            />
        </div>
    </section>
</x-layouts.manager>
