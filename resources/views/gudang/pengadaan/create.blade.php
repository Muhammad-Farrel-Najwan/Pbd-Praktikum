<x-layouts.gudang
    title="Tambah Pengadaan"
    breadcrumb="RedStock / <span>Tambah Pengadaan</span>"
    topbar-title="Panel Petugas Gudang"
>
    <x-ui.page-header
        eyebrow="Transaksi"
        title="Tambah Pengadaan"
        description="Catat pembelian barang dari vendor. Pengadaan akan menunggu approval Manager."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('gudang.pengadaan.index') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    @if ($errors->any())
        <x-ui.alert variant="error">
            {{ $errors->first() }}
        </x-ui.alert>
    @endif

    @if (session('error'))
        <x-ui.alert variant="error">
            {{ session('error') }}
        </x-ui.alert>
    @endif

    <form
        method="POST"
        action="{{ route('gudang.pengadaan.store') }}"
        id="pengadaan-form"
        data-pengadaan-form
    >
        @csrf

        <x-ui.panel
            title="Informasi Pengadaan"
            description="Pilih vendor yang akan memasok barang."
        >
            <div class="rs-form-grid">
                <x-ui.field
                    label="Vendor"
                    name="vendor_idvendor"
                    hint="Hanya vendor berstatus aktif yang ditampilkan."
                    :required="true"
                    class="rs-field-full"
                >
                    <select
                        @class(['rs-select', 'is-error' => $errors->has('vendor_idvendor')])
                        id="vendor_idvendor"
                        name="vendor_idvendor"
                        required
                    >
                        <option value="">-- Pilih Vendor --</option>

                        @foreach ($vendors as $vendor)
                            <option value="{{ $vendor->idvendor }}" @selected((string) old('vendor_idvendor') === (string) $vendor->idvendor)>
                                {{ $vendor->nama_vendor }}
                            </option>
                        @endforeach
                    </select>
                </x-ui.field>
            </div>
        </x-ui.panel>

        <div class="rs-form-layout">
            <x-ui.panel
                title="Daftar Barang"
                description="Cari barang lalu klik Tambah. Tekan Enter di kolom pencarian untuk menambah hasil teratas."
                :badge="count($barang) . ' Barang'"
                :flush="true"
                class="rs-mt-panel"
            >
                @if (count($barang) > 0)
                    <x-ui.table-search
                        placeholder="Cari nama barang..."
                        label="Cari barang yang akan dibeli"
                        :total="count($barang)"
                    />
                @endif

                <div class="rs-table-wrap">
                    <table class="rs-table">
                        <thead>
                            <tr>
                                <th scope="col">Nama Barang</th>
                                <th scope="col" class="is-right">Harga Satuan</th>
                                <th scope="col">Aksi</th>
                            </tr>
                        </thead>

                        <tbody>
                            @forelse ($barang as $item)
                                <tr
                                    data-table-row
                                    data-pengadaan-row
                                    data-id="{{ $item->idbarang }}"
                                    data-nama="{{ $item->nama }}"
                                    data-harga="{{ (int) $item->harga }}"
                                >
                                    <td>
                                        <span class="rs-cell-primary">{{ $item->nama }}</span>
                                        <span class="rs-pengadaan-badge" data-pengadaan-badge hidden></span>
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
                                            data-pengadaan-add
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
                                        description="Coba kata kunci lain atau bersihkan pencarian."
                                    />
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </x-ui.panel>

            <x-ui.panel
                title="Keranjang Pengadaan"
                description="Ringkasan barang yang akan dibeli."
                class="rs-mt-panel"
            >
                <div class="rs-pos-cart">
                    <div class="rs-pos-empty" data-pengadaan-empty>
                        <span class="rs-pos-empty-icon" aria-hidden="true">
                            <x-ui.icon name="package" />
                        </span>

                        <strong>Keranjang masih kosong</strong>
                        <p>Tambahkan barang dari daftar untuk memulai pengadaan.</p>
                    </div>

                    <div class="rs-pos-items" data-pengadaan-items></div>
                </div>

                <p class="rs-pengadaan-count" data-pengadaan-count aria-live="polite">
                    Belum ada barang dipilih
                </p>

                <dl class="rs-pos-summary">
                    <div>
                        <dt>Subtotal</dt>
                        <dd data-pengadaan-subtotal>Rp 0</dd>
                    </div>

                    <div class="is-total">
                        <dt>Total Pengadaan</dt>
                        <dd data-pengadaan-total>Rp 0</dd>
                    </div>
                </dl>

                <x-slot:footer>
                    <div class="rs-form-actions">
                        <x-ui.button href="{{ route('gudang.pengadaan.index') }}" variant="secondary">
                            Batal
                        </x-ui.button>

                        <x-ui.button type="submit" icon="check" data-pengadaan-submit disabled>
                            Simpan Pengadaan
                        </x-ui.button>
                    </div>
                </x-slot:footer>
            </x-ui.panel>
        </div>

        <div data-pengadaan-hidden></div>

        @php
            $oldItems = collect(old('items', []))
                ->filter(fn ($item) => is_array($item) && isset($item['idbarang']))
                ->mapWithKeys(fn ($item) => [(string) $item['idbarang'] => (int) ($item['jumlah'] ?? 1)])
                ->all();
        @endphp

        <script type="application/json" id="pengadaan-old-items">@json($oldItems)</script>
    </form>
</x-layouts.gudang>
