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
        data-margin="{{ $margin ? (float) $margin->persen : 0 }}"
        data-margin-active="{{ $margin ? '1' : '0' }}"
    >
        @csrf

        <div class="rs-form-layout">
            <x-ui.panel
                title="Daftar Barang"
                description="Barang aktif yang dapat dijual."
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
                                <th scope="col" class="is-right">Harga</th>
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
                                            Rp {{ number_format((float) $item->harga, 0, ',', '.') }}
                                        </span>
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
                                            data-harga="{{ (int) $item->harga }}"
                                        >
                                            Tambah
                                        </x-ui.button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3">
                                        <x-ui.empty-state
                                            icon="box"
                                            title="Belum ada barang aktif"
                                            description="Tambahkan barang melalui panel admin terlebih dahulu."
                                        />
                                    </td>
                                </tr>
                            @endforelse

                            <tr data-table-search-empty hidden>
                                <td colspan="3">
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
                        <dt>
                            Margin
                            <span data-pos-margin-label>{{ $margin ? (float) $margin->persen : 0 }}%</span>
                        </dt>
                        <dd data-pos-margin>Rp 0</dd>
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
