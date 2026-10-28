<x-layouts.kasir
    title="Dashboard Kasir"
    breadcrumb="RedStock / <span>Dashboard Kasir</span>"
    topbar-title="Panel Kasir"
>
    <x-ui.page-header
        eyebrow="Point of Sale"
        title="Dashboard Kasir"
        description="Pusat operasional transaksi penjualan dan retur barang di gerai RedStock."
    >
        <x-slot:actions>
            <span class="rs-date-chip">
                <x-ui.icon name="calendar" :size="15" />
                {{ now()->translatedFormat('d F Y') }}
            </span>
        </x-slot:actions>
    </x-ui.page-header>

    <x-ui.hero
        eyebrow="Panel Kasir"
        :title="'Selamat datang, ' . session('username', 'Kasir') . '!'"
        description="Siap melayani transaksi pelanggan. Modul operasional kasir akan tersedia secara bertahap."
        :delay="60"
    >
        <x-slot:aside>
            <span class="rs-hero-icon" aria-hidden="true">
                <x-ui.icon name="receipt" />
            </span>

            <div>
                <div class="rs-hero-value">Standby</div>
                <div class="rs-hero-label">Status terminal kasir</div>
            </div>
        </x-slot:aside>
    </x-ui.hero>

    <section aria-labelledby="summary-title">
        <div class="rs-section-head">
            <div>
                <h2 id="summary-title">Ringkasan Sesi</h2>
                <p>Status akses dan kesiapan modul kasir.</p>
            </div>
        </div>

        <div class="rs-stats">
            <x-ui.stat-card
                label="Sesi Kasir"
                value="Online"
                note="Terautentikasi sebagai kasir"
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
                note="Transaksi, retur, riwayat"
                icon="clock"
                tone="amber"
                :delay="220"
            />
        </div>
    </section>

    <section aria-labelledby="modules-title">
        <div class="rs-section-head">
            <div>
                <h2 id="modules-title">Modul Kasir</h2>
                <p>Modul operasional yang akan tersedia pada pembaruan berikutnya.</p>
            </div>
        </div>

        <div class="rs-modules">
            <x-ui.module-card
                title="Transaksi Penjualan"
                description="Proses transaksi pelanggan, hitung total, dan cetak bukti pembayaran."
                icon="receipt"
                :delay="100"
            />

            <x-ui.module-card
                title="Retur Penjualan"
                description="Catat pengembalian barang dari pelanggan beserta alasan retur."
                icon="rotate"
                :delay="160"
            />

            <x-ui.module-card
                title="Riwayat Transaksi"
                description="Telusuri transaksi yang pernah diproses beserta detailnya."
                icon="clock"
                :delay="220"
            />
        </div>
    </section>

    <x-ui.panel
        title="Catatan Modul"
        description="Informasi kesiapan modul kasir."
        badge="Roadmap"
        class="rs-mt-panel"
    >
        <x-ui.alert variant="info">
            Modul transaksi kasir masih dalam tahap pengembangan. Sementara ini
            Anda tetap dapat mengakses dashboard dan keluar dari sesi dengan aman.
        </x-ui.alert>
    </x-ui.panel>
</x-layouts.kasir>
