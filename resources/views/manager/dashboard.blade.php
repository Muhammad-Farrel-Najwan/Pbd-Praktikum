<x-layouts.manager
    title="Dashboard Manager"
    breadcrumb="RedStock / <span>Dashboard Manager</span>"
    topbar-title="Panel Manager"
>
    <x-ui.page-header
        eyebrow="Business Intelligence"
        title="Dashboard Manager"
        description="Pantau performa penjualan dan kesehatan persediaan RedStock dari satu panel."
    >
        <x-slot:actions>
            <span class="rs-date-chip">
                <x-ui.icon name="calendar" :size="15" />
                {{ now()->translatedFormat('d F Y') }}
            </span>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.hero
        eyebrow="Panel Manager"
        :title="'Selamat datang, ' . session('username', 'Manager') . '!'"
        description="Gunakan data operasional untuk mengambil keputusan. Modul analitik akan tersedia secara bertahap."
        :delay="60"
    >
        <x-slot:aside>
            <span class="rs-hero-icon" aria-hidden="true">
                <x-ui.icon name="chart" />
            </span>

            <div>
                <div class="rs-hero-value">Standby</div>
                <div class="rs-hero-label">Status panel manajerial</div>
            </div>
        </x-slot:aside>
    </x-ui.hero>

    <section aria-labelledby="summary-title">
        <div class="rs-section-head">
            <div>
                <h2 id="summary-title">Ringkasan Sesi</h2>
                <p>Status akses dan kesiapan modul manajerial.</p>
            </div>
        </div>

        <div class="rs-stats">
            <x-ui.stat-card
                label="Sesi Manager"
                value="Online"
                note="Terautentikasi sebagai manager"
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
                note="Laporan penjualan, stok, approval"
                icon="clock"
                tone="amber"
                :delay="220"
            />
        </div>
    </section>

    <section aria-labelledby="modules-title">
        <div class="rs-section-head">
            <div>
                <h2 id="modules-title">Modul Manager</h2>
                <p>Modul analitik yang akan tersedia pada pembaruan berikutnya.</p>
            </div>
        </div>

        <div class="rs-modules">
            <x-ui.module-card
                title="Laporan Penjualan"
                description="Lihat rekap omzet dan performa penjualan dalam periode tertentu."
                icon="chart"
                :delay="100"
            />

            <x-ui.module-card
                title="Laporan Stok"
                description="Pantau nilai persediaan serta barang yang mendekati batas minimum."
                icon="database"
                :delay="160"
            />

            <x-ui.module-card
                title="Approval Pengadaan"
                description="Tinjau dan setujui permintaan pengadaan dari petugas gudang."
                icon="check"
                :delay="220"
            />
        </div>
    </section>

    <x-ui.panel
        title="Catatan Modul"
        description="Informasi kesiapan modul manajerial."
        badge="Roadmap"
        class="rs-mt-panel"
    >
        <x-ui.alert variant="info">
            Modul analitik manajerial masih dalam tahap pengembangan. Sementara ini
            Anda tetap dapat mengakses dashboard dan keluar dari sesi dengan aman.
        </x-ui.alert>
    </x-ui.panel>
</x-layouts.manager>
