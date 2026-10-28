<x-layouts.admin
    title="Tambah Satuan"
    breadcrumb="RedStock / <span>Tambah Satuan</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Tambah Satuan"
        description="Masukkan satuan baru ke dalam sistem RedStock."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.satuan') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="rs-form-layout">
        <x-ui.panel
            title="Informasi Satuan"
            description="Kolom bertanda * wajib diisi."
        >
            <form method="POST" action="{{ route('admin.satuan.store') }}" id="satuan-form">
                @csrf

                <div class="rs-form-grid">
                    <x-ui.field
                        label="Nama Satuan"
                        name="nama_satuan"
                        hint="Maksimal 45 karakter."
                        :required="true"
                        class="rs-field-full"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('nama_satuan')])
                            type="text"
                            id="nama_satuan"
                            name="nama_satuan"
                            maxlength="45"
                            value="{{ old('nama_satuan') }}"
                            placeholder="Contoh: Pcs"
                            required
                            autofocus
                        >
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
                            <option value="">-- Pilih Status --</option>
                            <option value="1" @selected(old('status') === '1')>
                                Aktif
                            </option>
                            <option value="0" @selected(old('status') === '0')>
                                Tidak Aktif
                            </option>
                        </select>
                    </x-ui.field>
                </div>
            </form>

            <x-slot:footer>
                <p>
                    <strong>Perhatian:</strong> pastikan nama satuan belum terdaftar sebelum disimpan.
                </p>

                <div class="rs-form-actions">
                    <x-ui.button href="{{ route('admin.dashboard.satuan') }}" variant="secondary">
                        Batal
                    </x-ui.button>

                    <x-ui.button type="submit" form="satuan-form" icon="check">
                        Simpan Satuan
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.panel>
    </div>
</x-layouts.admin>
