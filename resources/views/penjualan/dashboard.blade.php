<x-layouts.penjualan
    title="Dashboard Penjualan"
    breadcrumb="RedStock / <span>Dashboard Penjualan</span>"
    topbar-title="Panel Staff Penjualan"
>
    <x-ui.page-header
        eyebrow="Sales Operations"
        title="Dashboard Penjualan"
        description="Kelola aktivitas penjualan dan retur barang di lingkungan RedStock."
    >
        <x-slot:actions>
            <span class="rs-date-chip">
                <x-ui.icon name="calendar" :size="15" />
                {{ now()->translatedFormat('d F Y') }}
            </span>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.hero
        eyebrow="Panel Staff Penjualan"
        :title="'Selamat datang, ' . session('username', 'Staff') . '!'"
        description="Dukung kelancaran penjualan dan pencatatan retur. Modul penjualan akan tersedia secara bertahap."
        :delay="60"
    >
        <x-slot:aside>
            <span class="rs-hero-icon" aria-hidden="true">
                <x-ui.icon name="tag" />
            </span>

            <div>
                <div class="rs-hero-value">Standby</div>
                <div class="rs-hero-label">Status panel penjualan</div>
            </div>
        </x-slot:aside>
    </x-ui.hero>

    <section aria-labelledby="summary-title">
        <div class="rs-section-head">
            <div>
                <h2 id="summary-title">Ringkasan Sesi</h2>
                <p>Status akses dan kesiapan modul penjualan.</p>
            </div>
        </div>

        <div class="rs-stats">
            <x-ui.stat-card
                label="Sesi Penjualan"
                value="Online"
                note="Terautentikasi sebagai staff penjualan"
                icon="shield"
                tone="green"
                :delay="100"
            />

            <x-ui.stat-card
                label="Modul Tersedia"
                value="1"
                note="Dashboard aktif"
                icon="grid"
                tone="blue"
                :delay="160"
            />

            <x-ui.stat-card
                label="Modul Menunggu"
                value="3"
                note="Penjualan, retur, riwayat"
                icon="clock"
                tone="amber"
                :delay="220"
            />
        </div>
    </section>

    <section aria-labelledby="modules-title">
        <div class="rs-section-head">
            <div>
                <h2 id="modules-title">Modul Penjualan</h2>
                <p>Modul penjualan yang akan tersedia pada pembaruan berikutnya.</p>
            </div>
        </div>

        <div class="rs-modules">
            <x-ui.module-card
                title="Penjualan"
                description="Catat transaksi penjualan dan pantau status pemrosesannya."
                icon="receipt"
                :delay="100"
            />

            <x-ui.module-card
                title="Retur Penjualan"
                description="Kelola pengembalian barang dari pelanggan beserta alasannya."
                icon="rotate"
                :delay="160"
            />

            <x-ui.module-card
                title="Riwayat Penjualan"
                description="Telusuri riwayat transaksi penjualan yang telah tercatat."
                icon="clock"
                :delay="220"
            />
        </div>
    </section>

    <x-ui.panel
        title="Catatan Modul"
        description="Informasi kesiapan modul penjualan."
        badge="Roadmap"
        class="rs-mt-panel"
    >
        <x-ui.alert variant="info">
            Modul penjualan masih dalam tahap pengembangan. Sementara ini
            Anda tetap dapat mengakses dashboard dan keluar dari sesi dengan aman.
        </x-ui.alert>
    </x-ui.panel>
</x-layouts.penjualan>
