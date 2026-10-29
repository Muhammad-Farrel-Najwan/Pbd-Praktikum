<x-layouts.admin
    title="Data Vendor"
    breadcrumb="RedStock / <span>Data Vendor</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Master Data"
        title="Data Vendor"
        description="Kelola dan pantau seluruh vendor yang terdaftar di RedStock."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
            </x-ui.button>

            <x-ui.button href="{{ route('admin.vendor.create') }}" icon="plus">
                Tambah Vendor
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel
        title="Daftar Vendor"
        description="Seluruh vendor yang tersimpan di database RedStock."
        :badge="count($vendors) . ' Vendor'"
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

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nama Vendor</th>
                        <th scope="col">Badan Usaha</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($vendors as $vendor)
                        <tr>
                            <td>
                                <span class="rs-mono">#{{ $vendor->id }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $vendor->nama }}</span>
                            </td>

                            <td>
                                <span class="rs-chip">{{ $vendor->jenis_badan_usaha }}</span>
                            </td>

                            <td>
                                @if ($vendor->status_vendor === 'Aktif')
                                    <x-ui.badge variant="success" dot>Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="red" dot>Tidak Aktif</x-ui.badge>
                                @endif
                            </td>

                            <td>
                                <div class="rs-table-actions">
                                    <x-ui.button
                                        href="{{ route('admin.vendor.edit', $vendor->id) }}"
                                        variant="secondary"
                                        size="sm"
                                        icon="settings"
                                    >
                                        Edit
                                    </x-ui.button>

                                    <form
                                        action="{{ route('admin.vendor.destroy', $vendor->id) }}"
                                        method="POST"
                                        onsubmit="return confirm('Yakin ingin menghapus vendor ini?')"
                                    >
                                        @csrf
                                        @method('DELETE')

                                        <x-ui.button type="submit" variant="danger" size="sm" icon="x">
                                            Hapus
                                        </x-ui.button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5">
                                <x-ui.empty-state
                                    icon="truck"
                                    title="Belum ada data vendor"
                                    description="Data vendor akan tampil di sini setelah ditambahkan."
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-layouts.admin>
