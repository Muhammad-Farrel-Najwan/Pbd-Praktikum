@php
    $currentMargin = null;

    foreach ($margins as $item) {
        if ((string) $item->idmargin_penjualan === (string) $activeMargin) {
            $currentMargin = $item;
            break;
        }
    }
@endphp

<x-layouts.admin
    title="Dashboard Admin"
    breadcrumb="RedStock / <span>Dashboard Admin</span>"
    topbar-title="Panel Administrator"
>
    <x-ui.page-header
        eyebrow="Control Center"
        title="Dashboard Admin"
        description="Kelola data master dan konfigurasi sistem inventaris RedStock dari satu tempat."
    >
        <x-slot:actions>
            <span class="rs-date-chip">
                <x-ui.icon name="calendar" :size="15" />
                {{ now()->translatedFormat('d F Y') }}
            </span>

            <x-ui.button href="{{ route('admin.dashboard.barang') }}" variant="secondary" icon="box">
                Data Barang
            </x-ui.button>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.hero
        eyebrow="Panel Administrator"
        :title="'Selamat datang, ' . session('username', 'Admin') . '!'"
        description="Pantau pengaturan sistem dan pastikan konfigurasi inventaris tetap sesuai kebutuhan operasional."
        :delay="60"
    >
        <x-slot:aside>
            <span class="rs-hero-icon" aria-hidden="true">
                <x-ui.icon name="sliders" />
            </span>

            <div>
                <div class="rs-hero-value">
                    {{ $currentMargin ? $currentMargin->persen . '%' : '—' }}
                </div>
                <div class="rs-hero-label">Margin aktif saat ini</div>
            </div>
        </x-slot:aside>
    </x-ui.hero>

    <section aria-labelledby="summary-title">
        <div class="rs-section-head">
            <div>
                <h2 id="summary-title">Ringkasan Pengaturan</h2>
                <p>Informasi konfigurasi margin penjualan saat ini.</p>
            </div>
        </div>

        <div class="rs-stats">
            <x-ui.stat-card
                label="Pilihan Margin"
                :value="count($margins)"
                note="Total opsi margin di database"
                icon="sliders"
                tone="red"
                :delay="100"
            />

            <x-ui.stat-card
                label="Margin Aktif"
                :value="$currentMargin?->persen ?? '—'"
                :suffix="$currentMargin ? '%' : ''"
                note="Persentase margin yang berlaku"
                icon="check"
                tone="green"
                :delay="160"
            />

            <x-ui.stat-card
                label="Status Konfigurasi"
                :value="$activeMargin !== null ? 'Tersedia' : 'Perlu Atur'"
                :note="$activeMargin !== null ? 'Margin aktif ditemukan' : 'Belum ada margin aktif'"
                icon="activity"
                tone="blue"
                :delay="220"
            />
        </div>
    </section>

    <x-ui.panel
        id="pengaturan-margin"
        title="Pengaturan Margin Penjualan"
        description="Pilih margin yang akan digunakan sistem. Hanya satu margin yang boleh aktif."
        :badge="count($margins) . ' Opsi Margin'"
        :flush="true"
        class="rs-margin-panel"
    >
        @if (session('success'))
            <x-ui.alert variant="success">
                {{ session('success') }}
            </x-ui.alert>
        @endif

        @if ($errors->has('idmargin_penjualan'))
            <x-ui.alert variant="error">
                {{ $errors->first('idmargin_penjualan') }}
            </x-ui.alert>
        @endif

        <form method="POST" action="{{ route('admin.dashboard.margin.update') }}">
            @csrf

            <div class="rs-table-wrap">
                <table class="rs-table">
                    <thead>
                        <tr>
                            <th class="rs-cell-radio" scope="col">Pilih</th>
                            <th scope="col">Persentase Margin</th>
                            <th scope="col">Status</th>
                            <th scope="col">Terakhir Diperbarui</th>
                        </tr>
                    </thead>

                    <tbody>
                        @forelse ($margins as $margin)
                            <tr @class(['rs-margin-row', 'is-selected' => (int) $margin->status === 1]) data-margin-row>
                                <td class="rs-cell-radio">
                                    <input class="rs-radio" type="radio" name="idmargin_penjualan"
                                        value="{{ $margin->idmargin_penjualan }}"
                                        aria-label="Pilih margin {{ $margin->persen }} persen"
                                        @checked((string) old('idmargin_penjualan', $activeMargin) === (string) $margin->idmargin_penjualan)
                                        required>
                                </td>

                                <td>
                                    <div class="rs-margin-percent">
                                        {{ number_format((float) $margin->persen, 0, ',', '.') }}%
                                    </div>
                                    <div class="rs-margin-subtitle">
                                        ID Margin: {{ $margin->idmargin_penjualan }}
                                    </div>
                                </td>

                                <td>
                                    @if ((int) $margin->status === 1)
                                        <x-ui.badge variant="success" dot>Aktif</x-ui.badge>
                                    @else
                                        <x-ui.badge variant="red" dot>Tidak Aktif</x-ui.badge>
                                    @endif
                                </td>

                                <td>
                                    <span class="rs-cell-muted">
                                        {{ $margin->updated_at
                                            ? \Illuminate\Support\Carbon::parse($margin->updated_at)->format('d/m/Y H:i')
                                            : 'Belum tersedia' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">
                                    <x-ui.empty-state
                                        icon="sliders"
                                        title="Belum ada data margin"
                                        description="Tambahkan data margin ke database terlebih dahulu."
                                    />
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if (count($margins) > 0)
                <div class="rs-panel-foot">
                    <p>
                        <strong>Perhatian:</strong> margin yang dipilih akan menjadi margin aktif.
                        Perubahan diterapkan setelah tombol simpan ditekan.
                    </p>

                    <x-ui.button type="submit" icon="check">
                        Simpan Perubahan
                    </x-ui.button>
                </div>
            @endif
        </form>
    </x-ui.panel>
</x-layouts.admin>
