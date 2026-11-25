<x-layouts.manager
    title="Laporan Stok"
    breadcrumb="RedStock / <span>Laporan Stok</span>"
    topbar-title="Panel Manager"
>
    <x-ui.page-header
        eyebrow="Analitik"
        title="Laporan Stok"
        description="Pantau sisa persediaan setiap barang berdasarkan kartu stok."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('manager.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <section aria-labelledby="summary-title">
        <div class="rs-section-head">
            <div>
                <h2 id="summary-title">Ringkasan Persediaan</h2>
                <p>Kondisi stok seluruh barang yang terdaftar.</p>
            </div>
        </div>

        <div class="rs-stats">
            <x-ui.stat-card
                label="Total Barang"
                :value="$totalBarang"
                note="Seluruh barang terdaftar"
                icon="box"
                tone="blue"
                :delay="100"
            />

            <x-ui.stat-card
                label="Total Stok"
                :value="$totalStok"
                note="Akumulasi sisa stok"
                icon="layers"
                tone="green"
                :delay="160"
            />

            <x-ui.stat-card
                label="Stok Habis"
                :value="$stokHabis"
                note="Barang tanpa sisa stok"
                icon="alert"
                tone="red"
                :delay="220"
            />
        </div>
    </section>

    <x-ui.panel
        title="Daftar Stok Barang"
        description="Sisa stok dihitung dari total masuk dikurangi total keluar."
        :badge="$stok->total() . ' Barang'"
        :flush="true"
    >
        @if ($stok->total() > 0 || filled($q))
            <x-ui.table-search
                placeholder="Cari nama barang atau satuan..."
                label="Cari data stok"
                :total="$stok->total()"
                :action="route('manager.laporan.stok')"
                :value="$q"
            />
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nama Barang</th>
                        <th scope="col">Satuan</th>
                        <th scope="col" class="is-right">Stok</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($stok as $item)
                        <tr data-table-row>
                            <td>
                                <span class="rs-mono">#{{ $item->idbarang }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $item->nama }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">{{ $item->nama_satuan ?? '-' }}</span>
                            </td>

                            <td class="is-right">
                                @if ((int) $item->stok > 0)
                                    <span class="rs-chip">{{ (int) $item->stok }}</span>
                                @else
                                    <x-ui.badge variant="red" dot>Habis</x-ui.badge>
                                @endif
                            </td>

                            <td>
                                @if ((int) $item->status === 1)
                                    <x-ui.badge variant="success" dot>Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="red" dot>Tidak Aktif</x-ui.badge>
                                @endif
                            </td>

                            <td>
                                <div class="rs-table-actions">
                                    <x-ui.button
                                        href="{{ route('manager.laporan.stok.detail', $item->idbarang) }}"
                                        variant="secondary"
                                        size="sm"
                                        icon="clock"
                                    >
                                        Riwayat
                                    </x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-ui.table-empty
                                    q="{{ $q }}"
                                    icon="layers"
                                    title="Belum ada data barang"
                                    description="Data barang dikelola melalui panel admin."
                                    search-title="Barang tidak ditemukan"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:footer>
            <x-ui.table-footer :paginator="$stok" label="barang" />
        </x-slot:footer>
    </x-ui.panel>
</x-layouts.manager>
