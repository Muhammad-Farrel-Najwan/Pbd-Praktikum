<x-layouts.admin
    title="Manajemen User"
    breadcrumb="RedStock / <span>Manajemen User</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Manajemen User"
        description="Kelola akun dan hak akses pengguna RedStock."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
            </x-ui.button>

            <x-ui.button href="{{ route('admin.user.create') }}" icon="plus">
                Tambah User
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel
        title="Daftar User"
        description="Seluruh akun yang tersimpan di database RedStock."
        :badge="count($users) . ' User'"
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

        @if (count($users) > 0)
            <x-ui.table-search placeholder="Cari username atau role..." label="Cari data user" :total="count($users)" />
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Username</th>
                        <th scope="col">Role</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        @php
                            $isCurrentUser = (int) session('iduser') === (int) $user->id;
                        @endphp

                        <tr data-table-row>
                            <td>
                                <span class="rs-mono">#{{ $user->id }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $user->nama_pengguna }}</span>

                                @if ($isCurrentUser)
                                    <x-ui.badge variant="success" dot>Anda</x-ui.badge>
                                @endif
                            </td>

                            <td>
                                <span class="rs-chip">{{ $user->peran }}</span>
                            </td>

                            <td>
                                <div class="rs-table-actions">
                                    <x-ui.button
                                        href="{{ route('admin.user.edit', $user->id) }}"
                                        variant="secondary"
                                        size="sm"
                                        icon="settings"
                                    >
                                        Edit
                                    </x-ui.button>

                                    @if ($isCurrentUser)
                                        <x-ui.button
                                            type="button"
                                            variant="danger"
                                            size="sm"
                                            icon="x"
                                            disabled
                                            title="Akun yang sedang digunakan tidak boleh dihapus"
                                        >
                                            Hapus
                                        </x-ui.button>
                                    @else
                                        <form
                                            action="{{ route('admin.user.destroy', $user->id) }}"
                                            method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus user ini?')"
                                        >
                                            @csrf
                                            @method('DELETE')

                                            <x-ui.button type="submit" variant="danger" size="sm" icon="x">
                                                Hapus
                                            </x-ui.button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-ui.empty-state
                                    icon="users"
                                    title="Belum ada data user"
                                    description="Data user akan tampil di sini setelah ditambahkan."
                                />
                            </td>
                        </tr>
                    @endforelse

                    <tr data-table-search-empty hidden>
                        <td colspan="4">
                            <x-ui.empty-state icon="search" title="User tidak ditemukan"
                                description="Coba kata kunci lain atau bersihkan pencarian." />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-layouts.admin>
