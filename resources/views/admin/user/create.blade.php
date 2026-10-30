<x-layouts.admin
    title="Tambah User"
    breadcrumb="RedStock / <span>Tambah User</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Tambah User"
        description="Buat akun baru beserta hak aksesnya di RedStock."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard.user') }}" variant="secondary" icon="arrow-left">
                Kembali
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <div class="rs-form-layout">
        <x-ui.panel
            title="Informasi User"
            description="Kolom bertanda * wajib diisi."
        >
            <form method="POST" action="{{ route('admin.user.store') }}" id="user-form">
                @csrf

                <div class="rs-form-grid">
                    <x-ui.field
                        label="Username"
                        name="username"
                        hint="Maksimal 45 karakter dan harus unik."
                        :required="true"
                        class="rs-field-full"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('username')])
                            type="text"
                            id="username"
                            name="username"
                            maxlength="45"
                            value="{{ old('username') }}"
                            placeholder="Contoh: admin_redstock"
                            required
                            autofocus
                        >
                    </x-ui.field>

                    <x-ui.field
                        label="Password"
                        name="password"
                        hint="Minimal 8 karakter."
                        :required="true"
                        class="rs-field-full"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('password')])
                            type="password"
                            id="password"
                            name="password"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                    </x-ui.field>

                    <x-ui.field
                        label="Konfirmasi Password"
                        name="password_confirmation"
                        hint="Ulangi password yang sama."
                        :required="true"
                        class="rs-field-full"
                    >
                        <input
                            @class(['rs-input', 'is-error' => $errors->has('password_confirmation')])
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            minlength="8"
                            autocomplete="new-password"
                            required
                        >
                    </x-ui.field>

                    <x-ui.field
                        label="Role"
                        name="role_idrole"
                        :required="true"
                        class="rs-field-full"
                    >
                        <select
                            @class(['rs-select', 'is-error' => $errors->has('role_idrole')])
                            id="role_idrole"
                            name="role_idrole"
                            required
                        >
                            <option value="">-- Pilih Role --</option>

                            @foreach ($roles as $role)
                                <option
                                    value="{{ $role->idrole }}"
                                    @selected((string) old('role_idrole') === (string) $role->idrole)
                                >
                                    {{ $role->nama_role }}
                                </option>
                            @endforeach
                        </select>
                    </x-ui.field>
                </div>
            </form>

            <x-slot:footer>
                <p>
                    <strong>Perhatian:</strong> pastikan username belum digunakan dan role sudah sesuai.
                </p>

                <div class="rs-form-actions">
                    <x-ui.button href="{{ route('admin.dashboard.user') }}" variant="secondary">
                        Batal
                    </x-ui.button>

                    <x-ui.button type="submit" form="user-form" icon="check">
                        Simpan User
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </x-ui.panel>
    </div>
</x-layouts.admin>
