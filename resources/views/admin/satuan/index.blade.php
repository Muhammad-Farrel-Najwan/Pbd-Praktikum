<x-layouts.admin title="Data Satuan" breadcrumb="RedStock / <span>Data Satuan</span>" topbar-title="Panel Administrator">
    <x-ui.page-header eyebrow="Master Data" title="Data Satuan"
        description="Kelola dan pantau seluruh satuan barang yang terdaftar di RedStock.">
        <x-slot:actions>
            <x-ui.button href="{{ route('admin.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
            </x-ui.button>

            <x-ui.button href="{{ route('admin.satuan.create') }}" icon="plus">
                Tambah Satuan
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.panel title="Daftar Satuan" description="Seluruh satuan yang tersimpan di database RedStock."
        :badge="count($satuans) . ' Satuan'" :flush="true">
        @if (session('success'))
            <x-ui.alert variant="success">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nama Satuan</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($satuans as $item)
                        <tr>
                            <td>
                                <span class="rs-mono">#{{ $item->id }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $item->nama }}</span>
                            </td>

                            <td>
                                @if ($item->status_data === 'Aktif')
                                    <x-ui.badge variant="success" dot>Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="red" dot>Tidak Aktif</x-ui.badge>
                                @endif
                            </td>
                            <td>
                                <x-ui.button
                                    href="{{ route('admin.satuan.edit', $item->id) }}"
                                    variant="secondary"
                                    size="sm"
                                    icon="settings"
                                >
                                    Edit
                                </x-ui.button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4">
                                <x-ui.empty-state icon="ruler" title="Belum ada data satuan"
                                    description="Data satuan akan tampil di sini setelah ditambahkan." />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-ui.panel>
</x-layouts.admin>
