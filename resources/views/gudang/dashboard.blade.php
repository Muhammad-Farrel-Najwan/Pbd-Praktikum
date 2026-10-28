<x-layouts.gudang
    title="Dashboard Gudang"
    breadcrumb="RedStock / <span>Dashboard Gudang</span>"
    topbar-title="Panel Petugas Gudang"
>
    <x-ui.page-header
        eyebrow="Inventory Control"
        title="Dashboard Gudang"
        description="Pantau pergerakan stok dan kesiapan operasional gudang RedStock."
    >
        <x-slot:actions>
            <span class="rs-date-chip">
                <x-ui.icon name="calendar" :size="15" />
                {{ now()->translatedFormat('d F Y') }}
            </span>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.hero
        eyebrow="Panel Petugas Gudang"
        :title="'Selamat datang, ' . session('username', 'Petugas') . '!'"
        description="Jaga akurasi stok dan kelancaran arus barang. Modul inventori akan tersedia secara bertahap."
        :delay="60"
    >
        <x-slot:aside>
            <span class="rs-hero-icon" aria-hidden="true">
                <x-ui.icon name="warehouse" />
            </span>

            <div>
                <div class="rs-hero-value">Standby</div>
                <div class="rs-hero-label">Status gudang</div>
            </div>
        </x-slot:aside>
    </x-ui.hero>

    <section aria-labelledby="summary-title">
        <div class="rs-section-head">
            <div>
                <h2 id="summary-title">Ringkasan Sesi</h2>
                <p>Status akses dan kesiapan modul gudang.</p>
            </div>
        </div>

        <div class="rs-stats">
            <x-ui.stat-card
                label="Sesi Gudang"
                value="Online"
                note="Terautentikasi sebagai petugas gudang"
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
                note="Penerimaan, kartu stok, pengadaan"
                icon="clock"
                tone="amber"
                :delay="220"
            />
        </div>
    </section>

    <section aria-labelledby="modules-title">
        <div class="rs-section-head">
            <div>
                <h2 id="modules-title">Modul Gudang</h2>
                <p>Modul inventori yang akan tersedia pada pembaruan berikutnya.</p>
            </div>
        </div>

        <div class="rs-modules">
            <x-ui.module-card
                title="Penerimaan Barang"
                description="Catat barang masuk dari vendor beserta jumlah dan kondisi fisiknya."
                icon="truck"
                :delay="100"
            />

            <x-ui.module-card
                title="Kartu Stok"
                description="Pantau mutasi stok masuk dan keluar untuk setiap barang."
                icon="layers"
                :delay="160"
            />

            <x-ui.module-card
                title="Pengadaan Barang"
                description="Ajukan permintaan pengadaan saat persediaan menipis."
                icon="package"
                :delay="220"
            />
        </div>
    </section>

    <x-ui.panel
        title="Catatan Modul"
        description="Informasi kesiapan modul gudang."
        badge="Roadmap"
        class="rs-mt-panel"
    >
        <x-ui.alert variant="info">
            Modul inventori gudang masih dalam tahap pengembangan. Sementara ini
            Anda tetap dapat mengakses dashboard dan keluar dari sesi dengan aman.
        </x-ui.alert>
    </x-ui.panel>
</x-layouts.gudang>
