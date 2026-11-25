<x-layouts.manager
    title="Approval Pengadaan"
    breadcrumb="RedStock / <span>Approval Pengadaan</span>"
    topbar-title="Panel Manager"
>
    <x-ui.page-header
        eyebrow="Analitik"
        title="Approval Pengadaan"
        description="Tinjau dan setujui permintaan pengadaan sebelum barang dapat diterima gudang."
    >
        <x-slot:actions>
            <x-ui.button href="{{ route('manager.dashboard') }}" variant="secondary" icon="arrow-left">
                Dashboard
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

    <x-ui.panel
        title="Daftar Pengadaan"
        description="Pengadaan berstatus P menunggu approval, sedangkan status S sudah disetujui."
        :badge="$pengadaan->total() . ' Pengadaan'"
        :flush="true"
    >
        <div class="rs-table-toolbar" style="justify-content: flex-start;">
            <x-ui.button
                href="{{ route('manager.approval.index') }}"
                variant="{{ $status === null ? 'primary' : 'secondary' }}"
                size="sm"
            >
                Semua
            </x-ui.button>

            <x-ui.button
                href="{{ route('manager.approval.index', ['status' => 'P']) }}"
                variant="{{ $status === 'P' ? 'primary' : 'secondary' }}"
                size="sm"
            >
                Menunggu Approval
            </x-ui.button>

            <x-ui.button
                href="{{ route('manager.approval.index', ['status' => 'S']) }}"
                variant="{{ $status === 'S' ? 'primary' : 'secondary' }}"
                size="sm"
            >
                Disetujui
            </x-ui.button>
        </div>

        @if ($pengadaan->total() > 0 || filled($q))
            <x-ui.table-search
                placeholder="Cari ID, vendor, atau petugas..."
                label="Cari data pengadaan"
                :total="$pengadaan->total()"
                :action="route('manager.approval.index')"
                :value="$q"
            />
        @endif

        <div class="rs-table-wrap">
            <table class="rs-table">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Vendor</th>
                        <th scope="col">Petugas</th>
                        <th scope="col">Status</th>
                        <th scope="col" class="is-right">Total</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($pengadaan as $item)
                        <tr data-table-row>
                            <td>
                                <span class="rs-mono">#{{ $item->idpengadaan }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">
                                    {{ $item->created_at
                                        ? \Illuminate\Support\Carbon::parse($item->created_at)->translatedFormat('d M Y, H:i')
                                        : '-' }}
                                </span>
                            </td>

                            <td>
                                <span class="rs-cell-primary">{{ $item->nama_vendor }}</span>
                            </td>

                            <td>
                                <span class="rs-cell-muted">{{ $item->username }}</span>
                            </td>

                            <td>
                                @if ($item->status === 'P')
                                    <x-ui.badge variant="warning" dot>Menunggu Approval</x-ui.badge>
                                @elseif ($item->status === 'S')
                                    <x-ui.badge variant="success" dot>Disetujui</x-ui.badge>
                                @else
                                    <x-ui.badge variant="muted">-</x-ui.badge>
                                @endif
                            </td>

                            <td class="is-right">
                                <span class="rs-price">
                                    Rp {{ number_format((float) $item->total_nilai, 0, ',', '.') }}
                                </span>
                            </td>

                            <td>
                                <div class="rs-table-actions">
                                    <x-ui.button
                                        href="{{ route('manager.approval.detail', $item->idpengadaan) }}"
                                        variant="secondary"
                                        size="sm"
                                        icon="eye"
                                    >
                                        Detail
                                    </x-ui.button>

                                    @if ($item->status === 'P')
                                        <form
                                            action="{{ route('manager.approval.setujui', $item->idpengadaan) }}"
                                            method="POST"
                                            data-confirm="Pengadaan akan disetujui dan dapat diterima oleh petugas gudang."
                                            data-confirm-title="Setujui Pengadaan?"
                                            data-confirm-label="Ya, Setujui"
                                            data-confirm-variant="primary"
                                        >
                                            @csrf

                                            <x-ui.button type="submit" size="sm" icon="check">
                                                Setujui
                                            </x-ui.button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <x-ui.table-empty
                                    q="{{ $q }}"
                                    icon="check"
                                    title="Tidak ada pengadaan"
                                    description="Belum ada pengadaan yang sesuai dengan filter ini."
                                    search-title="Pengadaan tidak ditemukan"
                                />
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <x-slot:footer>
            <x-ui.table-footer :paginator="$pengadaan" label="pengadaan" />
        </x-slot:footer>
    </x-ui.panel>
</x-layouts.manager>
