<x-layouts.manager
    title="Laporan Penjualan"
    breadcrumb="RedStock / <span>Laporan Penjualan</span>"
    topbar-title="Panel Manager"
>
    <x-ui.page-header
        eyebrow="Analitik"
        title="Laporan Penjualan"
        description="Rekap transaksi penjualan selesai dalam periode tertentu."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('manager.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($errors->any())
        <x-ui.alert variant="error">
            {{ $errors->first() }}
        </x-ui.alert>
    @endif

    <x-ui.panel
        title="Filter Periode"
        description="Pilih rentang tanggal laporan yang ingin ditampilkan."
    >
        <form method="GET" action="{{ route('manager.laporan.penjualan') }}" id="filter-laporan-form">
            <div class="rs-form-grid">
                <x-ui.field label="Dari Tanggal" name="dari">
                    <input
                        type="date"
                        class="rs-input"
                        id="dari"
                        name="dari"
                        value="{{ $dari }}"
                    >
                </x-ui.field>

                <x-ui.field label="Sampai Tanggal" name="sampai">
                    <input
                        type="date"
                        class="rs-input"
                        id="sampai"
                        name="sampai"
                        value="{{ $sampai }}"
                    >
                </x-ui.field>
            </div>

            <div class="rs-form-actions">
                <x-ui.button type="submit" icon="search">
                    Tampilkan Laporan
                </x-ui.button>
            </div>
        </form>
    </x-ui.panel>

    <section aria-labelledby="summary-title">
        <div class="rs-section-head">
            <div>
                <h2 id="summary-title">Ringkasan Periode</h2>
                <p>
                    {{ \Illuminate\Support\Carbon::parse($dari)->translatedFormat('d M Y') }}
                    sampai
                    {{ \Illuminate\Support\Carbon::parse($sampai)->translatedFormat('d M Y') }}
                </p>
            </div>
        </div>

        <div class="rs-stats">
            <x-ui.stat-card
                label="Jumlah Transaksi"
                :value="(int) $ringkasan->jumlah_transaksi"
                note="Penjualan berstatus selesai"
                icon="receipt"
                tone="blue"
                :delay="100"
            />

            <x-ui.stat-card
                label="Subtotal Penjualan"
                value="Rp {{ number_format((float) $ringkasan->total_subtotal, 0, ',', '.') }}"
                note="Sebelum PPN"
                icon="chart"
                tone="green"
                :delay="160"
            />

            <x-ui.stat-card
                label="Total PPN"
                value="Rp {{ number_format((float) $ringkasan->total_ppn, 0, ',', '.') }}"
                note="PPN 11% dari subtotal"
                icon="file-text"
                tone="amber"
                :delay="220"
            />

            <x-ui.stat-card
                label="Total Omzet"
                value="Rp {{ number_format((float) $ringkasan->total_nilai, 0, ',', '.') }}"
                note="Subtotal ditambah PPN"
                icon="database"
                tone="red"
                :delay="280"
            />
        </div>
    </section>

    <x-ui.panel
        title="Daftar Transaksi"
        description="Transaksi penjualan selesai pada periode yang dipilih."
        :badge="$penjualan->total() . ' Transaksi'"
        :flush="true"
    >
        @if ($penjualan->total() > 0 || filled($q))
            <x-ui.table-search
                placeholder="Cari ID atau kasir..."
                label="Cari transaksi penjualan"
                :total="$penjualan->total()"
                :action="route('manager.laporan.penjualan')"
                :value="$q"
            />
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Kasir</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="is-right">Subtotal</th>
                        <th scope="col" class="is-right">PPN</th>
                        <th scope="col" class="is-right">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($penjualan as $transaksi)
                        <tr data-table-row>
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
                                <span class="rs-cell-primary">{{ $transaksi->kasir }}</span>
                            </td>

                            <td>
                                <x-ui.badge variant="success" dot>Selesai</x-ui.badge>
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $transaksi->subtotal_nilai, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $transaksi->ppn, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $transaksi->total_nilai, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-ui.table-empty
                                    q="{{ $q }}"
                                    icon="chart"
                                    title="Belum ada transaksi"
                                    description="Tidak ada penjualan selesai pada periode yang dipilih."
                                    search-title="Transaksi tidak ditemukan"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:footer>
            <x-ui.table-footer :paginator="$penjualan" label="transaksi" />
        </x-slot:footer>
    </x-ui.panel>
</x-layouts.manager>
