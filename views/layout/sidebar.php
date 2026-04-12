<!-- Sidebar (Left-side collapsible) -->
<aside id="sidebar" class="sidebar">
    <div class="sidebar-header">
        <a href="#" class="sidebar-brand"> <!-- Removed Tailwind classes -->
            <div class="brand-icon"> <!-- Custom class for icon container -->
                <i data-lucide="layout-dashboard"></i> <!-- Changed icon to layout-dashboard -->
            </div>
            <span class="brand-text">Si-SmaRT <span class="text-emerald">01</span></span> <!-- Custom class for text -->
        </a>
        <button id="desktop-sidebar-toggle" title="Toggle Sidebar"> <!-- Corrected ID -->
            <i data-lucide="chevron-left"></i>
        </button>
    </div>

    <nav class="sidebar-nav">
        <button onclick="showPage('dashboard')" id="nav-dashboard" class="active-tab" title="Beranda">
            <i data-lucide="home"></i> <!-- Changed icon to home -->
            <span>Beranda</span>
        </button>
        <button onclick="showPage('warga')" id="nav-warga" title="Workspace">
            <i data-lucide="layout-grid"></i>
            <span>Workspace</span>
        </button>
        
        <!-- Grup Warga & Laporan Warga -->
        <button onclick="toggleSubmenu('submenu-warga')" id="nav-group-warga" class="nav-group-toggle" title="Menu Warga">
            <i data-lucide="users"></i>
            <span>Warga</span>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
        </button>
        <div id="submenu-warga" class="submenu-items hidden">
            <button onclick="showPage('global-warga')" id="nav-global-warga" title="Direktori Warga">
                <i data-lucide="contact"></i>
                <span>Data Warga</span>
            </button>
            <button onclick="showPage('laporan-iuran-blok')" id="nav-laporan-iuran-blok" title="Laporan Iuran Blok">
                <i data-lucide="clipboard-list"></i>
                <span>Iuran Blok</span>
            </button>
            <button onclick="showPage('laporan-iuran-warga')" id="nav-laporan-iuran-warga" title="Detail Tunggakan">
                <i data-lucide="file-text"></i>
                <span>Tunggakan</span>
            </button>
            <!--<button onclick="showPage('rekonsiliasi')" id="nav-rekonsiliasi" title="Audit Tahunan">
                <i data-lucide="activity"></i>
                <span>Audit Iuran</span>
            </button>-->
        </div>
        
        <!-- Grup Keuangan & Pembukuan -->
        <button onclick="toggleSubmenu('submenu-keuangan')" id="nav-group-keuangan" class="nav-group-toggle" title="Menu Keuangan">
            <i data-lucide="circle-dollar-sign"></i>
            <span>Keuangan</span>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
        </button>
        <div id="submenu-keuangan" class="submenu-items hidden">
            <button onclick="showPage('keuangan')" id="nav-keuangan" title="Buku Kas Utama">
                <i data-lucide="wallet"></i>
                <span>Buku Kas</span>
            </button>
            <button onclick="showPage('detail-keuangan')" id="nav-detail-keuangan" title="Detail Keuangan">
                <i data-lucide="pie-chart"></i>
                <span>Detail</span>
            </button>
            <button onclick="showPage('pos-keuangan')" id="nav-pos-keuangan" title="Pos Anggaran">
                <i data-lucide="briefcase"></i>
                <span>Pos Anggaran</span>
            </button>
            <button onclick="showPage('pembukuan')" id="nav-pembukuan" title="Laporan Pembukuan">
                <i data-lucide="book-open"></i>
                <span>Pembukuan</span>
            </button>
        </div>

        <!-- Grup Informasi & Layanan -->
        <button onclick="toggleSubmenu('submenu-info')" id="nav-group-info" class="nav-group-toggle" title="Menu CMS">
            <i data-lucide="info"></i>
            <span>CMS</span>
            <i data-lucide="chevron-down" class="submenu-icon"></i>
        </button>
        <div id="submenu-info" class="submenu-items hidden">
            <button onclick="showPage('keamanan')" id="nav-keamanan" title="Keamanan Lingkungan">
                <i data-lucide="shield-check"></i>
                <span>Keamanan</span>
            </button>
            <button onclick="showPage('info')" id="nav-info" title="Pengaturan CMS">
                <i data-lucide="megaphone"></i>
                <span>Pengaturan CMS</span>
            </button>
            <button onclick="showPage('users')" id="nav-users" title="Manajemen Akses User">
                <i data-lucide="user-cog"></i>
                <span>Master User</span>
            </button>
            <button onclick="showPage('pasar')" id="nav-pasar" title="Kelola Produk & Toko">
                <i data-lucide="shopping-bag"></i>
                <span>UMKM</span>
            </button>
        </div>
    </nav>
</aside>