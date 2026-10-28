<x-layouts.admin
    title="Tambah Barang"
    breadcrumb="RedStock / <span>Tambah Barang</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Tambah Barang"
        description="Masukkan informasi barang baru ke dalam sistem RedStock."
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
            <form method="POST" action="{{ route('admin.barang.store') }}" id="barang-form">
                @csrf

                <div class="rs-form-grid">
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
                            value="{{ old('nama') }}"
                            placeholder="Contoh: MU Home Kit 2026/2027 Authentic"
                            required
                            autofocus
                        >
                    </x-ui.field>

                    <x-ui.field
                        label="Jenis Barang"
                        name="jenis"
                        hint="Isi satu karakter kode jenis."
                        :required="true"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('jenis')])
                            type="text"
                            id="jenis"
                            name="jenis"
                            maxlength="1"
                            value="{{ old('jenis') }}"
                            placeholder="Contoh: A"
                            required
                        >
                    </x-ui.field>

                    <x-ui.field
                        label="Satuan"
                        name="satuan_idsatuan"
                        :required="true"
                    >
                        <select
                            @class(['rs-select', 'is-error' => $errors->has('satuan_idsatuan')])
                            id="satuan_idsatuan"
                            name="satuan_idsatuan"
                            required
                        >
                            <option value="">-- Pilih Satuan --</option>

                            @foreach ($satuans as $satuan)
                                <option value="{{ $satuan->idsatuan }}" @selected((string) old('satuan_idsatuan') === (string) $satuan->idsatuan)>
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
                                value="{{ old('harga') }}"
                                placeholder="0"
                                required
                            >
                        </div>
                    </x-ui.field>

                    <x-ui.field
                        label="Status"
                        name="status"
                        :required="true"
                    >
                        <select
                            @class(['rs-select', 'is-error' => $errors->has('status')])
                            id="status"
                            name="status"
                            required
                        >
                            <option value="1" @selected(old('status', '1') === '1')>
                                Aktif
                            </option>
                            <option value="0" @selected(old('status') === '0')>
                                Nonaktif
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
                        Simpan Barang
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.panel>

        <x-ui.panel
            title="Panduan Singkat"
            description="Pastikan data sudah benar sebelum disimpan."
            :flush="true"
        >
            <div class="rs-tips">
                <div class="rs-tip">
                    <span class="rs-tip-icon" aria-hidden="true">
                        <x-ui.icon name="scan" :size="15" />
                    </span>

                    <div>
                        <strong>Kode jenis</strong>
                        <p>Jenis barang menggunakan satu karakter, misalnya A untuk alat tulis.</p>
                    </div>
                </div>

                <div class="rs-tip">
                    <span class="rs-tip-icon" aria-hidden="true">
                        <x-ui.icon name="box" :size="15" />
                    </span>

                    <div>
                        <strong>Satuan barang</strong>
                        <p>Pastikan satuan yang dipilih berstatus aktif agar barang dapat disimpan.</p>
                    </div>
                </div>

                <div class="rs-tip">
                    <span class="rs-tip-icon" aria-hidden="true">
                        <x-ui.icon name="tag" :size="15" />
                    </span>

                    <div>
                        <strong>Harga</strong>
                        <p>Tulis harga dalam rupiah tanpa titik atau koma, contoh 25000.</p>
                    </div>
                </div>
            </div>

            <p class="rs-info-note">
                Status <strong>Aktif</strong> menandai barang tersedia di daftar,
                sedangkan <strong>Nonaktif</strong> menyimpan data tanpa menonjolkan barang tersebut.
            </p>
        </x-ui.panel>
    </div>
</x-layouts.admin>
