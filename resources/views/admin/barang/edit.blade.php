<x-layouts.admin
    title="Edit Barang"
    breadcrumb="RedStock / <span>Edit Barang</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Edit Barang"
        description="Perbarui informasi barang yang sudah terdaftar di RedStock."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.barang') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="rs-form-layout">
        <x-ui.panel
            title="Informasi Barang"
            description="Kolom bertanda * wajib diisi."
        >
            <form method="POST" action="{{ route('admin.barang.update', $barang->id) }}" id="barang-form">
                @csrf
                @method('PUT')

                <div class="rs-form-grid">
                    <x-ui.field
                        label="Jenis Barang"
                        name="jenis"
                        hint="Isi satu karakter kode jenis."
                        :required="true"
                        class="rs-field-full"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('jenis')])
                            type="text"
                            id="jenis"
                            name="jenis"
                            maxlength="1"
                            value="{{ old('jenis', $dataAsli->jenis) }}"
                            placeholder="Contoh: A"
                            required
                            autofocus
                        >
                    </x-ui.field>

                    <x-ui.field
                        label="Nama Barang"
                        name="nama"
                        hint="Maksimal 45 karakter."
                        :required="true"
                        class="rs-field-full"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('nama')])
                            type="text"
                            id="nama"
                            name="nama"
                            maxlength="45"
                            value="{{ old('nama', $dataAsli->nama) }}"
                            placeholder="Contoh: MU Home Kit 2026/2027 Authentic"
                            required
                            autofocus
                        >
                    </x-ui.field>

                    <x-ui.field
                        label="Satuan"
                        name="satuan_idsatuan"
                        :required="true"
                        class="rs-field-full"
                    >
                        <select
                            @class(['rs-select', 'is-error' => $errors->has('satuan_idsatuan')])
                            id="satuan_idsatuan"
                            name="satuan_idsatuan"
                            required
                        >
                            <option value="">-- Pilih Satuan --</option>

                            @foreach ($satuans as $satuan)
                                <option value="{{ $satuan->idsatuan }}" @selected((string) old('satuan_idsatuan') === (string) $satuan->idsatuan)
                                    {{ $satuan->nama_satuan }}
                                </option>
                            @endforeach
                        </select>
                    </x-ui.field>

                    <x-ui.field
                        label="Harga"
                        name="harga"
                        hint="Masukkan angka tanpa titik atau koma."
                        :required="true"
                        class="rs-field-full"
                    >
                        <div class="rs-input-group">
                            <span class="rs-input-prefix" aria-hidden="true">Rp</span>

                            <input
                                @class(['rs-input', 'is-error' => $errors->has('harga')])
                                type="number"
                                id="harga"
                                name="harga"
                                min="0"
                                step="1"
                                value="{{ old('harga', $dataAsli->harga) }}"
                                placeholder="0"
                                required
                            >
                        </div>
                    </x-ui.field>

                    <x-ui.field
                        label="Status"
                        name="status"
                        :required="true"
                        class="rs-field-full"
                    >
                        <select
                            @class(['rs-select', 'is-error' => $errors->has('status')])
                            id="status"
                            name="status"
                            required
                        >
                            <option value="1" @selected(old('status', (string) $dataAsli->status) === '1')>
                                Aktif
                            </option>
                            <option value="0" @selected(old('status', (string) $dataAsli->status) === '0')>
                                Tidak Aktif
                            </option>
                        </select>
                    </x-ui.field>
                </div>
            </form>

            <x-slot:footer>
                <p>
                    <strong>Perhatian:</strong> pastikan data barang sudah benar sebelum disimpan.
                </p>

                <div class="rs-form-actions">
                    <x-ui.button href="{{ route('admin.dashboard.barang') }}" variant="secondary">
                        Batal
                    </x-ui.button>

                    <x-ui.button type="submit" form="barang-form" icon="check">
                        Simpan Perubahan
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.panel>
    </div>
</x-layouts.admin>