<x-layouts.kasir
    title="Riwayat Transaksi"
    breadcrumb="RedStock / <span>Riwayat Transaksi</span>"
    topbar-title="Panel Kasir"
>
    <x-ui.page-header
        eyebrow="Operasional"
        title="Riwayat Transaksi"
        description="Daftar seluruh transaksi penjualan yang tersimpan di RedStock."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('kasir.penjualan.create') }}" icon="plus">
                Transaksi Baru
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel
        title="Daftar Transaksi"
        description="Menampilkan 50 transaksi terbaru."
        :badge="count($penjualan) . ' Transaksi'"
        :flush="true"
    >
        @if (session('success'))
            <x-ui.alert variant="success">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        @if (session('error'))
            <x-ui.alert variant="error">
                {{ session('error') }}
            </x-ui.alert>
        @endif

        @if ($errors->any())
            <x-ui.alert variant="error">
                {{ $errors->first() }}
            </x-ui.alert>
        @endif

        @if (count($penjualan) > 0)
            <x-ui.table-search
                placeholder="Cari ID, kasir, status, atau tanggal..."
                label="Cari transaksi penjualan"
                :total="count($penjualan)"
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
                        <th scope="col">Aksi</th>
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
                                @if ($transaksi->status === 'S')
                                    <x-ui.badge variant="success" dot>Selesai</x-ui.badge>
                                @else
                                    <x-ui.badge variant="warning" dot>Pending</x-ui.badge>
                                @endif
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

                            <td>
                                <div class="rs-table-actions">
                                    <x-ui.button
                                        href="{{ route('kasir.penjualan.detail', $transaksi->idpenjualan) }}"
                                        variant="secondary"
                                        size="sm"
                                        icon="eye"
                                    >
                                        Detail
                                    </x-ui.button>

                                    <x-ui.button
                                        href="{{ route('kasir.penjualan.struk', $transaksi->idpenjualan) }}"
                                        variant="secondary"
                                        size="sm"
                                        icon="receipt"
                                    >
                                        Struk
                                    </x-ui.button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <x-ui.empty-state
                                    icon="receipt"
                                    title="Belum ada transaksi"
                                    description="Transaksi yang disimpan akan muncul di sini."
                                />
                            </td>
                        </tr>
                    @endforelse

                    <tr data-table-search-empty hidden>
                        <td colspan="8">
                            <x-ui.empty-state
                                icon="search"
                                title="Transaksi tidak ditemukan"
                                description="Coba gunakan kata kunci lain."
                            />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-layouts.kasir>
