<x-layouts.kasir
    title="Riwayat Transaksi"
    breadcrumb="RedStock / <span>Riwayat Transaksi</span>"
    topbar-title="Panel Kasir"
>
    <x-ui.page-header
        eyebrow="Operasional"
        title="Riwayat Transaksi"
        description="Daftar transaksi penjualan yang tersimpan di RedStock."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('kasir.penjualan.create') }}" icon="plus">
                Transaksi Baru
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

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

    <x-ui.panel
        title="Daftar Transaksi"
        description="Menampilkan 50 transaksi terbaru."
        :badge="count($penjualans) . ' Transaksi'"
        :flush="true"
    >
        @if (count($penjualans) > 0)
            <x-ui.table-search
                placeholder="Cari ID, kasir, atau tanggal..."
                label="Cari transaksi penjualan"
                :total="count($penjualans)"
            />
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Kasir</th>
                        <th scope="col" class="is-right">Subtotal</th>
                        <th scope="col" class="is-right">PPN</th>
                        <th scope="col" class="is-right">Total</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($penjualans as $penjualan)
                        <tr data-table-row>
                            <td>
                                <span class="rs-mono">#{{ $penjualan->idpenjualan }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">
                                    {{ $penjualan->created_at
                                        ? \Illuminate\Support\Carbon::parse($penjualan->created_at)->translatedFormat('d M Y, H:i')
                                        : '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $penjualan->kasir }}</span>
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $penjualan->subtotal_nilai, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $penjualan->ppn, 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $penjualan->total_nilai, 0, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                <x-ui.empty-state
                                    icon="receipt"
                                    title="Belum ada transaksi"
                                    description="Transaksi yang Anda simpan akan muncul di sini."
                                />
                            </td>
                        </tr>
                    @endforelse

                    <tr data-table-search-empty hidden>
                        <td colspan="6">
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
