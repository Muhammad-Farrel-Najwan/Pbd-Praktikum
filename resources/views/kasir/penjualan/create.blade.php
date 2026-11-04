<x-layouts.kasir
    title="Transaksi Penjualan"
    breadcrumb="RedStock / <span>Transaksi Penjualan</span>"
    topbar-title="Panel Kasir"
>
    <x-ui.page-header
        eyebrow="Point of Sale"
        title="Transaksi Penjualan"
        description="Pilih barang, atur jumlah, lalu simpan transaksi pelanggan."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('kasir.penjualan.index') }}" variant="secondary" icon="arrow-left">
                Riwayat Transaksi
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

    @if (! $margin)
        <x-ui.alert variant="error">
            Belum ada margin penjualan aktif. Hubungi admin sebelum membuat transaksi.
        </x-ui.alert>
    @endif

    <form
        method="POST"
        action="{{ route('kasir.penjualan.store') }}"
        id="pos-form"
        data-pos-form
        data-margin-active="{{ $margin ? '1' : '0' }}"
    >
        @csrf

        <div class="rs-form-layout">
            <x-ui.panel
                title="Daftar Barang"
                description="Barang aktif yang memiliki stok dan dapat dijual."
                :badge="count($barang) . ' Barang'"
                :flush="true"
            >
                @if (count($barang) > 0)
                    <x-ui.table-search
                        placeholder="Cari nama barang..."
                        label="Cari barang yang akan dijual"
                        :total="count($barang)"
                    />
                @endif

                <div class="rs-table-wrap">
                    <table class="rs-table">
                        <thead>
                            <tr>
                                <th scope="col">Nama Barang</th>
                                <th scope="col" class="is-right">Harga Jual</th>
                                <th scope="col">Stok</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($barang as $item)
                                <tr data-table-row>
                                    <td>
                                        <span class="rs-cell-primary">{{ $item->nama }}</span>
                                    </td>

                                    <td class="is-right">
                                        <span class="rs-price">
                                            Rp {{ number_format((float) $item->harga_jual, 0, ',', '.') }}
                                        </span>
                                    </td>

                                    <td>
                                        @if ((int) $item->stok > 0)
                                            <span class="rs-chip">{{ (int) $item->stok }}</span>
                                        @else
                                            <x-ui.badge variant="red" dot>Habis</x-ui.badge>
                                        @endif
                                    </td>

                                    <td>
                                        <x-ui.button
                                            type="button"
                                            variant="secondary"
                                            size="sm"
                                            icon="plus"
                                            data-pos-add
                                            data-id="{{ $item->idbarang }}"
                                            data-nama="{{ $item->nama }}"
                                            data-harga="{{ (int) round((float) $item->harga_jual) }}"
                                            data-stok="{{ (int) $item->stok }}"
                                            :disabled="(int) $item->stok <= 0"
                                        >
                                            {{ (int) $item->stok > 0 ? 'Tambah' : 'Habis' }}
                                        </x-ui.button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4">
                                        <x-ui.empty-state
                                            icon="box"
                                            title="Belum ada barang yang dapat dijual"
                                            description="Tambahkan barang dan stok melalui panel admin terlebih dahulu."
                                        />
                                    </td>
                                </tr>
                            @endforelse

                            <tr data-table-search-empty hidden>
                                <td colspan="4">
                                    <x-ui.empty-state
                                        icon="search"
                                        title="Barang tidak ditemukan"
                                        description="Coba gunakan kata kunci lain."
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-ui.panel>

            <x-ui.panel
                title="Keranjang Penjualan"
                description="Ringkasan transaksi yang akan disimpan."
            >
                <div class="rs-pos-cart">
                    <div class="rs-pos-empty" data-pos-empty>
                        <span class="rs-pos-empty-icon" aria-hidden="true">
                            <x-ui.icon name="receipt" />
                        </span>

                        <strong>Keranjang masih kosong</strong>
                        <p>Pilih barang dari daftar untuk memulai transaksi.</p>
                    </div>

                    <div class="rs-pos-items" data-pos-items></div>
                </div>

                <dl class="rs-pos-summary">
                    <div>
                        <dt>Subtotal</dt>
                        <dd data-pos-subtotal>Rp 0</dd>
                    </div>

                    <div>
                        <dt>PPN 11%</dt>
                        <dd data-pos-ppn>Rp 0</dd>
                    </div>

                    <div class="is-total">
                        <dt>Total</dt>
                        <dd data-pos-total>Rp 0</dd>
                    </div>
                </dl>

                <p class="rs-pos-note">
                    Harga jual sudah termasuk margin aktif
                    {{ $margin ? (float) $margin->persen : 0 }}%.
                </p>

                <x-slot:footer>
                    <div class="rs-form-actions">
                        <x-ui.button href="{{ route('kasir.penjualan.index') }}" variant="secondary">
                            Batal
                        </x-ui.button>

                        <x-ui.button type="submit" icon="check" data-pos-submit disabled>
                            Simpan Transaksi
                        </x-ui.button>
                    </div>
                </x-slot:footer>
            </x-ui.panel>
        </div>

        <div data-pos-hidden-inputs></div>
    </form>
</x-layouts.kasir>
