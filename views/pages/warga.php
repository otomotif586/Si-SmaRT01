<!-- Page: Warga -->
<div id="page-warga" class="page-content hidden page-section"> <!-- Added page-section class -->
    
    <div class="glass-card" style="padding: 20px; margin-bottom: 24px; border-radius: 20px;">
        <p class="text-secondary page-filter-desc text-secondary-sm">Pilih blok untuk mengelola data, iuran, dan agenda spesifik.</p>
    </div>

    <div class="grid-container">
        <?php
        // Ambil data blok beserta total warga per blok
        $current_month = date('n') - 1; // Array bulan JS (0-11)
        $current_year = date('Y');

        $stmt = $pdo->prepare("
            SELECT b.*, 
                   COUNT(w.id) as total_warga,
                   (SELECT SUM(p.total_tagihan) 
                    FROM pembayaran_iuran p 
                    JOIN warga w2 ON p.warga_id = w2.id 
                    WHERE w2.blok_id = b.id AND p.bulan = ? AND p.tahun = ? AND p.status = 'LUNAS'
                   ) as setor_bulan_ini
            FROM blok b 
            LEFT JOIN warga w ON b.id = w.blok_id 
            GROUP BY b.id
        ");
        $stmt->execute([$current_month, $current_year]);
        $bloks = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($bloks as $blok):
            $blok_id = $blok['id'];
            $nama_blok = htmlspecialchars($blok['nama_blok']);
            $koordinator = htmlspecialchars($blok['koordinator']);
            $total_warga = $blok['total_warga'];
            $kas_format = 'Rp ' . number_format($blok['kas_blok'], 0, ',', '.');
            $setor_bulan_ini = $blok['setor_bulan_ini'] ?? 0;
            $setor_format = 'Rp ' . number_format($setor_bulan_ini, 0, ',', '.');
            $logo_class = htmlspecialchars($blok['logo_class']);
            $logo_text = htmlspecialchars($blok['logo_text']);
            
            // Biarkan kosong jika tidak ada gambar agar memunculkan inisial NAMA BLOK
            $logo_image = isset($blok['logo_image']) && !empty($blok['logo_image']) ? htmlspecialchars(smart_asset($blok['logo_image']), ENT_QUOTES, 'UTF-8') : '';
        ?>
        <!-- Dynamic Workspace Card -->
        <div class="interactive-ws-card" onclick="openWorkspaceModal(<?= $blok_id ?>, '<?= $nama_blok ?>', '<?= $koordinator ?>', '<?= $total_warga ?>', '<?= $kas_format ?>', '<?= $logo_class ?>', '<?= $logo_text ?>', '<?= $logo_image ?>')">
            <div class="ws-hero <?= $logo_class ?>">
                <?php if ($logo_image): ?>
                    <img src="<?= $logo_image ?>" alt="Cover <?= $nama_blok ?>" class="ws-hero-img">
                <?php else: ?>
                    <span class="hero-letter"><?= $logo_text ?></span>
                <?php endif; ?>
            </div>
            <section>
                <h2><?= $nama_blok ?></h2>
                <p>Koordinator: <?= $koordinator ?><br>Jumlah Warga: <?= $total_warga ?> KK</p>
                <div class="card-footer">
                    <div class="meta-stack">
                        <span class="meta-label">Iuran Bulan Ini</span>
                        <span class="tag text-emerald font-bold tag-plain-value"><?= $setor_format ?></span>
                    </div>
                    <div class="inline-actions">
                        <button class="button-secondary button-sm button-secondary-accent" style="padding: 8px 16px; border-radius: 12px;" onclick="event.stopPropagation(); window.currentBlokId = <?= $blok_id ?>; openMasterIuran()"><i data-lucide="settings" class="icon-18 icon-mr-6"></i> <span class="hide-text-mobile">Master Iuran</span></button>
                        <button onclick="event.stopPropagation(); editBlok(<?= $blok_id ?>, '<?= addslashes($nama_blok) ?>', '<?= addslashes($koordinator) ?>', <?= $blok['periode_mulai_bulan'] ?? 'null' ?>, <?= $blok['periode_mulai_tahun'] ?? 'null' ?>)" title="Pengaturan Blok" class="icon-btn-round"><i data-lucide="settings" class="icon-16"></i></button>
                        <button onclick="event.stopPropagation(); hapusBlok(<?= $blok_id ?>, '<?= addslashes($nama_blok) ?>', <?= $total_warga ?>)" title="Hapus Blok" class="icon-btn-round icon-btn-round-danger"><i data-lucide="trash-2" class="icon-16"></i></button>
                        <button class="ws-action-btn">Buka</button>
                    </div>
                </div>
            </section>
        </div>
        <?php endforeach; ?>

        <!-- Tambah Blok Baru -->
        <div class="interactive-ws-card" onclick="openAddBlockModal()">
            <div class="ws-hero logo-new">
                <i data-lucide="plus" class="hero-letter" style="color: var(--text-secondary-color);"></i>
            </div>
            <section>
                <h2>Tambah Blok</h2>
                <p>Buat workspace blok baru<br>untuk mengelola warga.</p>
                <div class="card-footer">
                    <span class="tag text-secondary font-bold">Baru</span>
                    <button class="ws-action-btn">Buat</button>
                </div>
            </section>
        </div>
    </div>

</div>

<!-- Full-Screen Modal Workspace (System in a System) -->
<div id="workspace-modal" class="modal-overlay hidden">
    <div class="fullscreen-modal glass-card">
        
        <div class="modal-header">
            <div class="modal-header-info">
                <div id="modal-block-logo" class="ws-logo-container logo-a">A</div>
                <div>
                    <h2 id="modal-block-title" class="ws-title">Nama Blok</h2>
                    <p id="modal-block-coord" class="text-secondary text-secondary-sm" style="margin-top: 4px;">Koordinator: -</p>
                </div>
            </div>
            <button class="modal-close-btn" onclick="closeWorkspaceModal()"><i data-lucide="x"></i></button>
        </div>

        <!-- Modal Body & Sidebar Internal -->
        <div class="modal-body">
            <!-- Sidebar Menu Internal -->
            <div id="modal-sidebar" class="modal-nav hide-scrollbar">
                <button class="modal-tab active" onclick="switchModalTab('modal-dash', this)"><i data-lucide="pie-chart"></i> <span>Ringkasan</span></button>
                <button class="modal-tab" onclick="switchModalTab('modal-warga-list', this)"><i data-lucide="users"></i> <span>Data Warga</span></button>
                <button class="modal-tab" onclick="switchModalTab('modal-keuangan', this)"><i data-lucide="wallet"></i> <span>Iuran Blok</span></button>
                <button class="modal-tab" onclick="switchModalTab('modal-agenda', this)"><i data-lucide="calendar"></i> <span>Agenda & Laporan</span></button>
                <button class="modal-tab" onclick="switchModalTab('modal-laporan-relasi', this)"><i data-lucide="line-chart"></i> <span>Laporan & Relasi</span></button>
            </div>

            <!-- Konten Dinamis Internal -->
            <div class="modal-content-area">
                
                <!-- Tab 1: Ringkasan / Dashboard -->
                <div id="modal-dash" class="modal-tab-content">
                    
                    <!-- Pencarian Cepat & Quick Actions -->
                    <div style="margin-bottom: 24px; position: relative;">
                        <div class="input-with-icon mb-16">
                            <i data-lucide="search" class="icon-20" style="color: var(--accent-color);"></i>
                            <input type="text" id="quick-search-input" class="input-field quick-search-input" placeholder="Pencarian Cepat (Ketik nama warga/NIK lalu Enter...)" onkeypress="handleQuickSearch(event)">
                        </div>
                        <div class="quick-action-hub">
                            <button class="quick-action-btn" onclick="switchModalTab('modal-warga-list', document.querySelectorAll('.modal-tab')[1]); setTimeout(openFormWarga, 300);"><i data-lucide="user-plus" class="text-emerald"></i> Tambah Warga</button>
                            <button class="quick-action-btn" onclick="switchModalTab('modal-keuangan', document.querySelectorAll('.modal-tab')[2]);"><i data-lucide="wallet" class="text-orange"></i> Catat Iuran</button>
                            <button class="quick-action-btn" onclick="switchModalTab('modal-agenda', document.querySelectorAll('.modal-tab')[3]); setTimeout(openFormLaporanDrawer, 300);"><i data-lucide="flag" class="text-red"></i> Lapor Masalah</button>
                            <button class="quick-action-btn" onclick="openMasterIuran();"><i data-lucide="settings" class="text-blue"></i> Master Iuran</button>
                        </div>
                    </div>

                    <h3 class="section-title mb-16">Overview Utama</h3>
                    <div class="summary-3-grid">
                        <div class="glass-card-deluxe stagger-item stagger-delay-1">
                            <div class="card-icon-deluxe icon-tone-info">
                                <i data-lucide="users"></i>
                            </div>
                            <p class="card-label">Total Penghuni</p>
                            <h3 id="dash-stat-warga" class="card-value text-color card-value-lg">0 KK</h3>
                            <div class="card-sub-info">Data terdaftar di blok</div>
                        </div>
                        <div class="glass-card-deluxe stagger-item stagger-delay-2">
                            <div class="card-icon-deluxe icon-tone-success">
                                <i data-lucide="banknote"></i>
                            </div>
                            <p class="card-label">Saldo Kas Internal</p>
                            <h3 id="dash-stat-kas" class="card-value text-emerald card-value-lg">Rp 0</h3>
                            <div class="card-sub-info">Dana kelolaan blok</div>
                        </div>
                        <div class="glass-card-deluxe stagger-item stagger-delay-3">
                            <div class="card-icon-deluxe icon-tone-warning">
                                <i data-lucide="activity"></i>
                            </div>
                            <p class="card-label">Status Lingkungan</p>
                            <h3 id="dash-stat-status-main" class="card-value text-color card-value-lg">Aman</h3>
                            <div class="card-sub-info" id="dash-stat-status-sub">0 Laporan / 0 Agenda</div>
                        </div>
                    </div>
                    
                    <!-- Area Grafik Statistik (Charts) -->
                    <div class="grid-container-2-col chart-grid">
                        <div class="glass-card chart-card">
                            <h4 class="section-title chart-title">Demografi Warga</h4>
                            <div class="chart-frame chart-frame-center">
                                <canvas id="chartDemografi"></canvas>
                            </div>
                        </div>
                        <div class="glass-card chart-card">
                            <h4 class="section-title chart-title">Pemasukan Kas (6 Bulan Terakhir)</h4>
                            <div class="chart-frame">
                                <canvas id="chartPemasukan"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Tab 2: Data Warga Khusus Blok -->
                <div id="modal-warga-list" class="modal-tab-content hidden">
                    <div class="section-header page-filter-row mb-16 w-full">
                        <h3 class="section-title m-0">Daftar Warga Blok</h3>
                        <div class="inline-wrap-8">
                            <button class="button-secondary button-sm compact-btn" onclick="downloadTemplateWarga()" title="Download Template Import"><i data-lucide="file-spreadsheet" class="icon-18"></i> <span class="hide-text-mobile">Template</span></button>
                            <button class="button-secondary button-sm compact-btn" onclick="exportWargaCSV()" title="Export ke Excel (CSV)"><i data-lucide="download" class="icon-18"></i> <span class="hide-text-mobile">Export</span></button>
                            <label class="button-secondary button-sm compact-btn" style="cursor: pointer; margin: 0;" title="Import dari Excel (CSV)">
                                <i data-lucide="upload" class="icon-18"></i> <span class="hide-text-mobile">Import</span>
                                <input type="file" id="import-warga-csv" accept=".csv" class="hidden" onchange="importWargaCSV(this)">
                            </label>
                            <button class="button-primary button-sm compact-btn" onclick="openFormWarga()"><i data-lucide="user-plus" style="margin-right: 6px; width: 18px; height: 18px;"></i> <span class="hide-text-mobile">Tambah Warga</span></button>
                        </div>
                    </div>
                    
                    <!-- SUMMARY Warga (Deluxe 3-Across) -->
                    <div class="summary-3-grid">
                        <div class="glass-card-deluxe stagger-item stagger-delay-1">
                            <div class="card-icon-deluxe icon-tone-info">
                                <i data-lucide="users"></i>
                            </div>
                            <p class="card-label">Total Warga</p>
                            <h3 id="sum-warga-total" class="card-value text-color card-value-lg">0</h3>
                            <div class="card-sub-info">Data setelah filter</div>
                        </div>
                        <div class="glass-card-deluxe stagger-item stagger-delay-2">
                            <div class="card-icon-deluxe icon-tone-success">
                                <i data-lucide="user-check"></i>
                            </div>
                            <p class="card-label">Warga Tetap</p>
                            <h3 id="sum-warga-tetap" class="card-value text-emerald card-value-lg">0</h3>
                            <div class="card-sub-info">Domisili permanen</div>
                        </div>
                        <div class="glass-card-deluxe stagger-item stagger-delay-3">
                            <div class="card-icon-deluxe icon-tone-warning">
                                <i data-lucide="user-minus"></i>
                            </div>
                            <p class="card-label">Warga Kontrak</p>
                            <h3 id="sum-warga-kontrak" class="card-value text-orange card-value-lg">0</h3>
                            <div class="card-sub-info">Domisili sementara</div>
                        </div>
                    </div>
                        
                    <!-- Pencarian & Filter -->
                    <div class="inline-wrap-12 w-full mb-24">
                        <div class="input-with-icon input-grow-wide">
                            <i data-lucide="search"></i>
                            <input type="text" id="search-warga-input" placeholder="Cari nama atau NIK..." class="input-field input-field-compact" oninput="filterWargaList()">
                        </div>
                        <select id="filter-pernikahan" class="input-field select-custom filter-mobile-flex compact-select flex-1 minw-120" onchange="filterWargaList()">
                            <option value="">Pernikahan (Semua)</option>
                            <option value="Lajang">Lajang</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Pisah">Pisah</option>
                        </select>
                        <select id="filter-status" class="input-field select-custom filter-mobile-flex compact-select flex-1 minw-120" onchange="filterWargaList()">
                            <option value="">Status (Semua)</option>
                            <option value="Tetap">Tetap</option>
                            <option value="Kontrak">Kontrak</option>
                            <option value="Weekend">Weekend</option>
                        </select>
                    </div>
                    <div class="list-container" id="modal-warga-list-container">
                        <!-- Data Warga Akan Dimuat di Sini via AJAX -->
                        <p class="text-secondary text-center py-4">Memuat data...</p>
                    </div>
                    
                    <div id="warga-pagination" class="pagination-bar">
                        <span id="warga-page-info" class="text-secondary pagination-bar-info text-secondary-sm">Menampilkan 0-0 dari 0</span>
                        <div class="pagination-actions">
                            <button class="button-secondary button-sm compact-btn" onclick="prevPageWarga()">Sebelumnya</button>
                            <button class="button-secondary button-sm compact-btn" onclick="nextPageWarga()">Selanjutnya</button>
                        </div>
                    </div>
                </div>

                <!-- Tab 3 & 4 (Placeholder) -->
                <div id="modal-keuangan" class="modal-tab-content hidden">
                    <div class="section-header page-filter-row mb-16 w-full">
                        <h3 class="section-title m-0">Kelola Kas & Iuran Blok</h3>
                        <div class="inline-wrap-8">
                            <button class="button-secondary button-sm compact-btn" style="font-weight: 600;" onclick="openRekonsiliasi()"><i data-lucide="activity" class="icon-18 icon-mr-6"></i> <span class="hide-text-mobile">Rekonsiliasi</span></button>
                            <button class="button-secondary button-sm button-secondary-accent compact-btn" onclick="bayarTerpilihIuran()"><i data-lucide="check-square" class="icon-18 icon-mr-6"></i> <span class="hide-text-mobile">Bayar Terpilih</span></button>
                            <button class="button-secondary button-sm compact-btn" onclick="bayarSemuaIuran()"><i data-lucide="check-circle" class="icon-18 icon-mr-6"></i> <span class="hide-text-mobile">Bayar Semua</span></button>
                            <button class="button-primary button-sm compact-btn" style="box-shadow: 0 4px 12px rgba(59, 130, 246, 0.2);" onclick="setorKeRT()"><i data-lucide="send" class="icon-18 icon-mr-6"></i> <span class="hide-text-mobile">Setor ke RT Pusat</span></button>
                        </div>
                    </div>

                    <!-- Summary Iuran Deluxe (3-Across Adaptive) -->
                    <div id="iuran-summary" class="summary-3-grid">
                        <div class="glass-card-deluxe stagger-item stagger-delay-15">
                            <div class="card-icon-deluxe icon-tone-success">
                                <i data-lucide="check-circle"></i>
                            </div>
                            <p class="card-label">Sudah Bayar</p>
                            <h3 id="summary-lunas" class="card-value text-emerald card-value-md">Rp 0</h3>
                            <div id="summary-count-lunas" class="card-sub-info">0 Warga Terdata</div>
                        </div>

                        <div class="glass-card-deluxe stagger-item stagger-delay-25">
                            <div class="card-icon-deluxe icon-tone-danger">
                                <i data-lucide="alert-circle"></i>
                            </div>
                            <p class="card-label">Belum Bayar</p>
                            <h3 id="summary-menunggak" class="card-value text-red card-value-md">Rp 0</h3>
                            <div id="summary-count-menunggak" class="card-sub-info">0 Warga Tertunggak</div>
                        </div>

                        <div class="glass-card-deluxe stagger-item stagger-delay-35">
                            <div class="card-icon-deluxe icon-tone-info">
                                <i data-lucide="send"></i>
                            </div>
                            <p class="card-label">Setoran RT</p>
                            <h3 id="summary-setoran-status" class="card-value text-blue card-value-md">Ready</h3>
                            <div class="card-sub-info">Status antrean setor</div>
                        </div>
                    </div>
                    
                    <!-- Filter Tagihan -->
                    <div class="inline-wrap-12 w-full mb-24">
                        <div class="input-with-icon input-grow">
                            <i data-lucide="search"></i>
                            <input type="text" id="search-iuran-input" placeholder="Cari nama warga..." class="input-field input-field-compact" oninput="filterIuranList()">
                        </div>
                        <div class="inline-wrap-8">
                            <button class="button-secondary button-sm compact-btn-icon" onclick="prevMonthIuran()" title="Bulan Sebelumnya"><i data-lucide="chevron-left" class="icon-16"></i></button>
                            <select id="filter-bulan-iuran" class="input-field select-custom compact-select compact-select-month" onchange="loadDataIuran()">
                                <!-- Diisi dinamis oleh JS -->
                            </select>
                            <button class="button-secondary button-sm compact-btn-icon" onclick="nextMonthIuran()" title="Bulan Selanjutnya"><i data-lucide="chevron-right" class="icon-16"></i></button>
                        </div>
                        <select id="filter-status-iuran" class="input-field select-custom compact-select compact-select-auto" onchange="filterIuranList()">
                            <option value="">Semua Status</option>
                            <option value="LUNAS">Sudah Bayar</option>
                            <option value="MENUNGGAK">Belum Bayar</option>
                        </select>
                    </div>

                    <div class="list-container" id="modal-iuran-list-container">
                        <p class="text-secondary text-center py-4">Memuat data iuran...</p>
                    </div>
                    
                    <div id="iuran-pagination" class="pagination-bar">
                        <span id="iuran-page-info" class="text-secondary pagination-bar-info text-secondary-sm">Menampilkan 0-0 dari 0</span>
                        <div class="pagination-actions">
                            <button class="button-secondary button-sm compact-btn" onclick="prevPageIuran()"><i data-lucide="chevron-left" class="icon-16"></i></button>
                            <div id="iuran-page-numbers" style="display: flex; gap: 4px;"></div>
                            <button class="button-secondary button-sm compact-btn" onclick="nextPageIuran()"><i data-lucide="chevron-right" class="icon-16"></i></button>
                        </div>
                    </div>
                </div>

                <div id="modal-agenda" class="modal-tab-content hidden">
                    <div class="section-header mb-16">
                        <h3 class="section-title">Agenda & Laporan</h3>
                        <button class="button-primary button-sm compact-btn" onclick="openFormAgenda()"><i data-lucide="plus" class="icon-mr-6"></i> Buat Baru</button>
                    </div>
                    
                    <!-- SUMMARY AGENDA & LAPORAN -->
                    <div class="summary-wrapper">
                        <div class="summary-card-modern">
                            <div class="summary-icon-wrapper bg-purple-light text-purple"><i data-lucide="calendar"></i></div>
                            <p class="card-label m-0">Total Agenda</p>
                            <div class="summary-foot">
                                <h3 id="sum-agenda-total" class="card-value m-0">0</h3>
                                <span id="sum-agenda-selesai" class="badge bg-purple-light text-purple badge-mini">0 Selesai</span>
                            </div>
                        </div>
                        <div class="summary-card-modern">
                            <div class="summary-icon-wrapper bg-orange-light text-orange"><i data-lucide="flag"></i></div>
                            <p class="card-label m-0">Laporan Masalah</p>
                            <div class="summary-foot">
                                <h3 id="sum-laporan-total" class="card-value m-0">0</h3>
                                <span id="sum-laporan-selesai" class="badge bg-emerald-light text-emerald badge-mini">0 Selesai</span>
                            </div>
                        </div>
                    </div>

                    <!-- Sub-navigation -->
                    <div class="sub-nav-container">
                        <button class="sub-nav-tab active" onclick="switchSubTab(this, 'sub-tab-agenda')">
                            <i data-lucide="calendar-days"></i> Agenda Kegiatan
                        </button>
                        <button class="sub-nav-tab" onclick="switchSubTab(this, 'sub-tab-laporan')">
                            <i data-lucide="flag"></i> Laporan Masalah
                        </button>
                    </div>

                    <!-- Sub-tab Content -->
                    <div id="sub-tab-agenda" class="sub-tab-content">
                        <!-- Pencarian & Filter Agenda -->
                        <div class="inline-wrap-12 w-full mb-24">
                            <div class="input-with-icon input-grow">
                                <i data-lucide="search"></i>
                                <input type="text" id="search-agenda-input" placeholder="Cari judul atau keterangan..." class="input-field input-field-compact" oninput="filterAgendaList()">
                            </div>
                            <select id="filter-status-agenda" class="input-field select-custom compact-select compact-select-auto" onchange="filterAgendaList()">
                                <option value="">Semua Status</option>
                                <option value="Direncanakan">Direncanakan</option>
                                <option value="Selesai">Selesai</option>
                                <option value="Dibatalkan">Dibatalkan</option>
                            </select>
                        </div>
                        
                        <div id="agenda-list-container"></div>
                        
                        <div id="agenda-pagination" class="pagination-bar">
                            <span id="agenda-page-info" class="text-secondary pagination-bar-info text-secondary-sm">Menampilkan 0-0 dari 0</span>
                            <div class="pagination-actions">
                                <button class="button-secondary button-sm compact-btn" onclick="prevPageAgenda()">Sebelumnya</button>
                                <button class="button-secondary button-sm compact-btn" onclick="nextPageAgenda()">Selanjutnya</button>
                            </div>
                        </div>
                    </div>
                    <div id="sub-tab-laporan" class="sub-tab-content hidden">
                        <!-- Pencarian & Filter Laporan -->
                        <div class="inline-wrap-12 w-full mb-24">
                            <div class="input-with-icon input-grow">
                                <i data-lucide="search"></i>
                                <input type="text" id="search-laporan-input" placeholder="Cari judul laporan..." class="input-field input-field-compact" oninput="filterLaporanList()">
                            </div>
                            <select id="filter-status-laporan" class="input-field select-custom compact-select compact-select-auto" onchange="filterLaporanList()">
                                <option value="">Semua Status</option>
                                <option value="Baru">Baru</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                        
                        <div id="laporan-list-container"></div>
                        
                        <div id="laporan-pagination" class="pagination-bar">
                            <span id="laporan-page-info" class="text-secondary pagination-bar-info text-secondary-sm">Menampilkan 0-0 dari 0</span>
                            <div class="pagination-actions">
                                <button class="button-secondary button-sm compact-btn" onclick="prevPageLaporan()">Sebelumnya</button>
                                <button class="button-secondary button-sm compact-btn" onclick="nextPageLaporan()">Selanjutnya</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Tab 5: Laporan & Relasi (Spesifik Blok) -->
                <div id="modal-laporan-relasi" class="modal-tab-content hidden">
                    <div class="section-header page-filter-row" style="margin-bottom: 24px; width: 100%; gap: 12px;">
                        <h3 class="section-title m-0">Laporan & Relasi Iuran Blok</h3>
                        <div class="page-filter-actions" style="gap: 12px;">
                            <div class="input-with-icon" style="min-width: 200px;">
                                <i data-lucide="search"></i>
                                <input type="text" id="search-ws-laporan-warga" placeholder="Cari warga..." class="input-field input-field-compact text-size-sm rounded-12" oninput="filterWsLaporanWarga()">
                            </div>
                            <label class="text-secondary" style="font-size: 0.8rem; font-weight: 600;">Tahun:</label>
                            <input type="number" id="ws-relasi-year" class="input-field compact-control input-year-center" value="<?= date('Y') ?>" onchange="loadLaporanWargaWorkspace()">
                            <button class="button-secondary button-sm compact-btn btn-compact-rounded" style="padding: 10px 16px;" onclick="exportWsLaporanWargaCSV()"><i data-lucide="download" class="icon-18"></i> Export</button>
                        </div>
                    </div>

                    <!-- SUMMARY Laporan Warga -->
                    <div class="summary-wrapper" id="ws-relasi-summary-wrapper">
                        <div class="summary-card-modern">
                            <div class="summary-icon-wrapper bg-blue-light text-blue"><i data-lucide="users"></i></div>
                            <p class="card-label m-0">Total Warga</p>
                            <h3 id="ws-laporan-warga-total" class="card-value m-0">0</h3>
                        </div>
                        <div class="summary-card-modern">
                            <div class="summary-icon-wrapper bg-emerald-light text-emerald"><i data-lucide="check-circle"></i></div>
                            <p class="card-label m-0">Lunas 1 Tahun</p>
                            <h3 id="ws-laporan-warga-lunas" class="card-value m-0">0</h3>
                        </div>
                        <div class="summary-card-modern">
                            <div class="summary-icon-wrapper bg-red-light text-red"><i data-lucide="alert-circle"></i></div>
                            <p class="card-label m-0">Menunggak</p>
                            <h3 id="ws-laporan-warga-menunggak" class="card-value m-0">0</h3>
                        </div>
                    </div>

                    <div class="glass-card" style="padding: 0; border-radius: 20px; position: relative; overflow: hidden;">
                        <div class="table-responsive" style="overflow-x: auto; position: relative;">
                            <div id="ws-laporan-scroll-wrapper" style="position: relative; min-width: 1100px; padding-bottom: 40px;">
                                <svg id="ws-svg-relations" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 10;">
                                    <defs>
                                        <marker id="ws-arrowhead" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                                            <polygon points="0 0, 10 3.5, 0 7" fill="var(--accent-color)" opacity="0.6" />
                                        </marker>
                                        <marker id="ws-arrowhead-advance" markerWidth="10" markerHeight="7" refX="9" refY="3.5" orient="auto">
                                            <polygon points="0 0, 10 3.5, 0 7" fill="var(--status-info)" opacity="0.6" />
                                        </marker>
                                    </defs>
                                </svg>

                                <table id="ws-laporan-warga-table" class="modern-table rekon-table ws-relasi-table">
                                    <thead class="ws-relasi-thead">
                                        <tr>
                                            <th class="ws-col-warga">Nama Warga</th>
                                            <th class="ws-col-blok">NO/Blok</th>
                                            <th class="text-center ws-col-status">Status</th>
                                            <th class="text-center ws-col-month">Jan</th>
                                            <th class="text-center ws-col-month">Feb</th>
                                            <th class="text-center ws-col-month">Mar</th>
                                            <th class="text-center ws-col-month">Apr</th>
                                            <th class="text-center ws-col-month">Mei</th>
                                            <th class="text-center ws-col-month">Jun</th>
                                            <th class="text-center ws-col-month">Jul</th>
                                            <th class="text-center ws-col-month">Agu</th>
                                            <th class="text-center ws-col-month">Sep</th>
                                            <th class="text-center ws-col-month">Okt</th>
                                            <th class="text-center ws-col-month">Nov</th>
                                            <th class="text-center ws-col-month">Des</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ws-laporan-warga-table-body">
                                        <!-- Dynamic Content -->
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                        <div id="ws-laporan-pagination" class="glass-card" style="margin: 12px; padding: 12px 24px; border-radius: 12px; display: none; align-items: center; justify-content: space-between; gap: 16px; background: rgba(255,255,255,0.02); border: none;">
                            <div id="ws-laporan-page-info" class="text-secondary pagination-bar-info text-size-08">Menampilkan 1-20 data</div>
                            <div style="display: flex; gap: 8px;">
                                <button onclick="prevWsLaporanPage()" class="button-secondary button-sm compact-btn"><i data-lucide="chevron-left" class="icon-16"></i></button>
                                <button onclick="nextWsLaporanPage()" class="button-secondary button-sm compact-btn"><i data-lucide="chevron-right" class="icon-16"></i></button>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Modal Rekonsiliasi & Periode -->
<div id="modal-rekonsiliasi" class="modal-overlay hidden overlay-z10005">
    <div class="glass-card modal-shell modal-shell-md modal-shell-scroll">
        <button class="modal-close-btn modal-close-top-right" onclick="document.getElementById('modal-rekonsiliasi').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h2 class="section-title mb-8">Audit & Rekonsiliasi Kas</h2>
        <p class="text-secondary modal-desc">Deteksi otomatis warga yang menunggak berdasarkan periode pencatatan awal.</p>
        
        <div style="background: var(--hover-bg); padding: 16px; border-radius: 16px; margin-bottom: 24px; display: flex; gap: 12px; align-items: flex-end; flex-wrap: wrap;">
            <div class="flex-1 minw-120">
                <label class="card-label">Bulan Mulai Mencatat</label>
                <select id="rekon-bulan" class="input-field select-custom mt-8 select-left-16">
                    <option value="0">Januari</option><option value="1">Februari</option><option value="2">Maret</option>
                    <option value="3">April</option><option value="4">Mei</option><option value="5">Juni</option>
                    <option value="6">Juli</option><option value="7">Agustus</option><option value="8">September</option>
                    <option value="9">Oktober</option><option value="10">November</option><option value="11">Desember</option>
                </select>
            </div>
            <div class="flex-1 minw-100">
                <label class="card-label">Tahun</label>
                <input type="number" id="rekon-tahun" class="input-field mt-8 select-left-16">
            </div>
            <button class="button-primary btn-rekon-save" onclick="simpanPeriodeRekon(this)"><i data-lucide="save"></i></button>
        </div>

        <div class="section-divider-row">
            <span class="font-bold text-color">Daftar Penunggak (Diurutkan Terlama)</span>
            <span id="rekon-total-warga" class="badge bg-red-light text-red text-size-075">Memuat...</span>
        </div>

        <div id="rekonsiliasi-list" class="hide-scrollbar" style="overflow-y: auto; flex: 1; display: flex; flex-direction: column; gap: 12px; padding-bottom: 16px;">
            <!-- Data akan diisi oleh JS -->
            <p class="text-center text-secondary py-4">Memuat data rekonsiliasi...</p>
        </div>
    </div>
</div>

<!-- Modal Bayar Iuran -->
<div id="modal-bayar-iuran" class="modal-overlay hidden overlay-z10005">
    <div class="glass-card modal-shell modal-shell-sm">
        <button class="modal-close-btn modal-close-top-right" onclick="document.getElementById('modal-bayar-iuran').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h2 class="section-title mb-8">Tandai Dibayar</h2>
        <p class="text-secondary modal-desc">Konfirmasi pembayaran iuran untuk bulan ini.</p>
        
        <input type="hidden" id="bayar-iuran-id">
        <div class="mb-16">
            <label class="card-label">Tanggal Pembayaran</label>
            <input type="date" id="bayar-tanggal" class="input-field mt-8 input-left-20">
        </div>
        <div class="mb-32">
            <label class="card-label">Metode Pembayaran</label>
            <select id="bayar-metode" class="input-field select-custom mt-8">
                <option value="Cash">Tunai (Cash)</option>
                <option value="Transfer">Transfer Bank / E-Wallet</option>
            </select>
        </div>
        <button class="button-primary button-full-center" onclick="submitBayarIuran(this)"><i data-lucide="check-circle" class="icon-mr-8"></i> Konfirmasi Pembayaran</button>
    </div>
</div>

<!-- Modal Setor ke Kas RT -->
<div id="modal-setor-rt" class="modal-overlay hidden overlay-z10005">
    <div class="glass-card modal-shell modal-shell-sm">
        <button class="modal-close-btn modal-close-top-right" onclick="document.getElementById('modal-setor-rt').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h2 class="section-title mb-8">Setor ke Kas RT</h2>
        <p class="text-secondary modal-desc">Setorkan semua dana iuran yang sudah <b class="text-emerald">LUNAS</b> di bulan ini ke Kas Pusat RT.</p>
        
        <div class="mb-32">
            <label class="card-label">Tanggal Setor</label>
            <input type="date" id="setor-tanggal" class="input-field mt-8 input-left-20">
        </div>
        <div class="glass-card" style="padding: 12px 16px; background: rgba(59, 130, 246, 0.1); border-color: #3b82f6; margin-bottom: 24px; font-size: 0.8rem; color: var(--text-color);">
            <i data-lucide="info" style="display:inline; width:16px; height:16px; margin-right:4px; color: #3b82f6;"></i> Hanya tagihan berstatus LUNAS yang akan disetorkan.
        </div>
        <button class="button-primary button-full-center" onclick="submitSetorRT(this)"><i data-lucide="send" class="icon-mr-8"></i> Konfirmasi Setoran</button>
    </div>
</div>

<!-- Modal Edit Iuran -->
<div id="modal-edit-iuran" class="modal-overlay hidden overlay-z10005">
    <div class="glass-card modal-shell modal-shell-sm">
        <button class="modal-close-btn modal-close-top-right" onclick="document.getElementById('modal-edit-iuran').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h2 class="section-title mb-8">Edit Tagihan</h2>
        <p class="text-secondary modal-desc">Ubah nominal atau status tagihan.</p>
        
        <input type="hidden" id="edit-iuran-id">
        <div class="mb-16">
            <label class="card-label">Total Tagihan (Rp)</label>
            <input type="number" id="edit-iuran-nominal" class="input-field mt-8 input-left-20">
        </div>
        <div class="mb-16">
            <label class="card-label">Status</label>
            <select id="edit-iuran-status" class="input-field select-custom mt-8" onchange="toggleEditIuranDates(this.value)">
                <option value="MENUNGGAK">Belum Bayar</option>
                <option value="LUNAS">Lunas</option>
            </select>
        </div>
        <div class="mb-16">
            <label class="card-label">Metode Pembayaran</label>
            <select id="edit-iuran-metode" class="input-field select-custom mt-8">
                <option value="Cash">Tunai (Cash)</option>
                <option value="Transfer">Transfer Bank / E-Wallet</option>
            </select>
        </div>
        <div id="edit-iuran-dates-container" style="display: none;">
            <div class="mb-16">
                <label class="card-label">Tanggal Bayar</label>
                <input type="date" id="edit-iuran-tgl-bayar" class="input-field mt-8 input-left-20">
            </div>
            <div class="mb-32">
                <label class="card-label">Tanggal Setor RT (Opsional/Kosongkan jika belum)</label>
                <input type="date" id="edit-iuran-tgl-setor" class="input-field mt-8 input-left-20">
            </div>
        </div>
        <button class="button-primary button-full-center" onclick="submitEditIuran(this)">Simpan Perubahan</button>
    </div>
</div>

<!-- Modal Detail Iuran -->
<div id="modal-detail-iuran" class="modal-overlay hidden overlay-z10005">
    <div class="glass-card modal-shell modal-shell-sm">
        <button class="modal-close-btn modal-close-top-right" onclick="document.getElementById('modal-detail-iuran').classList.add('hidden')"><i data-lucide="x"></i></button>
        <h2 class="section-title mb-8">Rincian Tagihan</h2>
        <p class="text-secondary modal-desc">Rincian alokasi dana iuran bulan ini.</p>
        
        <div id="detail-iuran-list" style="display: flex; flex-direction: column; gap: 12px; margin-bottom: 24px;">
            <!-- Diisi oleh JS -->
        </div>
        <div style="border-top: 2px dashed var(--border-color); padding-top: 16px; display: flex; justify-content: space-between; align-items: center;">
            <span class="font-bold text-color">Total Tagihan</span>
            <h3 id="detail-iuran-total" class="text-emerald m-0">Rp 0</h3>
        </div>
    </div>
</div>

<!-- Drawer Modal: Master Iuran -->
<div id="drawer-master-iuran" class="modal-overlay hidden overlay-z10010-drawer">
    <div class="drawer-panel glass-card">
        <div class="drawer-header">
            <div>
                <h2 class="ws-title">Master Pembayaran</h2>
                <p class="text-secondary drawer-subtitle">Kelola komponen iuran wajib bulanan blok.</p>
            </div>
            <button class="modal-close-btn" onclick="closeMasterIuran()"><i data-lucide="x"></i></button>
        </div>
        
        <div class="drawer-body hide-scrollbar" style="padding-top: 16px;">
            <div class="glass-card" style="padding: 16px; background: rgba(16, 185, 129, 0.1); border-color: var(--accent-color); margin-bottom: 24px;">
                <p class="text-emerald font-bold m-0 text-size-sm"><i data-lucide="info" class="icon-inline icon-16 icon-mr-4"></i> Total Tagihan Per Bulan: <span id="total-master-iuran" style="font-size: 1.2rem; float:right;">Rp 0</span></p>
            </div>

            <div id="master-iuran-list" class="list-container" style="gap: 12px;">
                <!-- List Komponen dari JS -->
            </div>

            <div class="dynamic-add-section">
                <button type="button" class="button-secondary button-full-width" style="border-style: dashed; color: var(--accent-color); border-color: var(--accent-color);" onclick="addMasterIuranField()"><i data-lucide="plus"></i> Tambah Komponen Baru</button>
            </div>
        </div>
        
        <div class="drawer-footer">
            <button type="button" class="button-secondary" onclick="closeMasterIuran()">Tutup</button>
            <button type="button" class="button-primary flex-grow" onclick="simpanMasterIuran()"><i data-lucide="save" class="icon-mr-8"></i> Simpan Pengaturan</button>
        </div>
    </div>
</div>

<!-- Include Drawer Form Data Warga -->
<?php include 'views/pages/datawargablok.php'; ?>

<!-- Drawer Form Agenda -->
<div id="drawer-agenda" class="modal-overlay hidden overlay-z10010-drawer">
    <div class="drawer-panel glass-card">
        <div class="drawer-header">
            <div>
                <h2 id="drawer-agenda-title" class="ws-title">Tambah Agenda</h2>
                <p class="text-secondary drawer-subtitle">Kelola jadwal dan kegiatan blok.</p>
            </div>
            <button class="modal-close-btn" onclick="closeFormAgendaDrawer()"><i data-lucide="x"></i></button>
        </div>
        
        <div class="drawer-body hide-scrollbar">
            <input type="hidden" id="agenda_id" value="0">
            <div class="form-group mb-20">
                <label class="card-label">Unggah Dokumen Warga</label>
                <div class="upload-premium-container">
                    <input type="file" class="upload-premium-input dokumen-file">
                    <div class="upload-premium-label upload-label-pad">
                        <i data-lucide="file-text" class="text-secondary mb-2 icon-24"></i>
                        <span class="text-color font-bold text-compact">Pilih File (PDF/Gambar)</span>
                    </div>
                </div>
                <div id="container-dokumen"></div>
            </div>
            <div class="form-group mb-16">
                <label class="card-label">Tanggal & Waktu</label>
                <input type="datetime-local" id="agenda_tanggal" class="input-field mt-8 input-left-20">
            </div>
            <div class="form-group mb-16">
                <label class="card-label">Estimasi Biaya (Rp)</label>
                <input type="number" id="agenda_biaya" class="input-field mt-8 input-left-20">
            </div>
            <div class="form-group mb-16">
                <label class="card-label">Keterangan</label>
                <textarea id="agenda_keterangan" class="input-field mt-8 textarea-compact textarea-100"></textarea>
            </div>
            <div class="form-group mb-16">
                <label class="card-label">Status</label>
                <select id="agenda_status" class="input-field select-custom mt-8" onchange="toggleAgendaGallery(this.value)">
                    <option value="Direncanakan">Direncanakan</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Dibatalkan">Dibatalkan</option>
                </select>
            </div>
            
            <div class="form-group" style="margin-bottom: 20px; padding-top: 16px; border-top: 1px dashed var(--border-color);">
                <label class="card-label">Lampiran Berkas Agenda</label>
                <div class="upload-premium-container">
                    <input type="file" id="agenda_lampiran_files" multiple class="upload-premium-input">
                    <div class="upload-premium-label upload-label-pad">
                        <i data-lucide="file-plus" class="text-secondary mb-2 icon-24"></i>
                        <span class="text-color font-bold text-compact">Klik untuk Unggah Berkas</span>
                    </div>
                </div>
                <div id="agenda-lampiran-preview" class="mt-1"></div>
                <div id="agenda_existing_lampiran" class="list-col-gap-sm"></div>
            </div>
            
            <div id="agenda_gallery_section" class="hidden section-top-divider">
                <label class="card-label">Unggah Galeri (Foto & Video Sekaligus)</label>
                <input type="file" id="agenda_gallery_files" accept="image/*,video/mp4,video/webm" multiple class="input-field file-input-modern" style="margin-top: 8px; width: 100%;" onchange="previewAgendaGallery(this)">
                <div id="agenda_gallery_preview" class="list-wrap-gap-sm"></div>
                <div id="agenda_existing_gallery" class="list-wrap-gap-sm"></div>
            </div>
        </div>
        
        <div class="drawer-footer">
            <button type="button" class="button-secondary" onclick="closeFormAgendaDrawer()">Batal</button>
            <button type="button" class="button-primary flex-grow" onclick="simpanAgenda()"><i data-lucide="save" class="icon-mr-8"></i> Simpan Agenda</button>
        </div>
    </div>
</div>

<!-- Drawer Form Laporan -->
<div id="drawer-laporan" class="modal-overlay hidden overlay-z10010-drawer">
    <div class="drawer-panel glass-card">
        <div class="drawer-header">
            <div>
                <h2 id="drawer-laporan-title" class="ws-title">Buat Laporan</h2>
                <p class="text-secondary drawer-subtitle">Catat permasalahan atau keluhan di lingkungan.</p>
            </div>
            <button class="modal-close-btn" onclick="closeFormLaporanDrawer()"><i data-lucide="x"></i></button>
        </div>
        
        <div class="drawer-body hide-scrollbar">
            <input type="hidden" id="laporan_id" value="0">
            <div class="form-group mb-16">
                <label class="card-label">Judul Laporan</label>
                <input type="text" id="laporan_judul" class="input-field mt-8">
            </div>
            <div class="form-group mb-16">
                <label class="card-label">Tanggal Laporan</label>
                <input type="datetime-local" id="laporan_tanggal" class="input-field mt-8 input-left-20">
            </div>
            <div class="form-group mb-16">
                <label class="card-label">Keterangan / Detail Masalah</label>
                <textarea id="laporan_keterangan" class="input-field mt-8 textarea-compact textarea-120"></textarea>
            </div>
            <div class="form-group mb-16">
                <label class="card-label">Status</label>
                <select id="laporan_status" class="input-field select-custom mt-8" onchange="toggleLaporanSelesai(this.value)">
                    <option value="Baru">Baru</option>
                    <option value="Diproses">Diproses</option>
                    <option value="Selesai">Selesai</option>
                </select>
            </div>
            <div id="laporan_tanggal_selesai_section" class="form-group hidden mb-16">
                <label class="card-label">Tanggal Selesai</label>
                <input type="datetime-local" id="laporan_tanggal_selesai" class="input-field mt-8 input-left-20">
            </div>
            
            <div class="form-group" style="margin-bottom: 20px; padding-top: 16px; border-top: 1px dashed var(--border-color);">
                <label class="card-label">Lampiran Bukti Kejadian</label>
                <div class="upload-premium-container">
                    <input type="file" id="laporan_lampiran_files" multiple class="upload-premium-input">
                    <div class="upload-premium-label upload-label-pad">
                        <i data-lucide="camera" class="text-secondary mb-2 icon-24"></i>
                        <span class="text-color font-bold text-compact">Foto atau Video Kejadian</span>
                    </div>
                </div>
                <div id="laporan_existing_lampiran" class="list-col-gap-sm"></div>
            </div>
        </div>
        
        <div class="drawer-footer">
            <button type="button" class="button-secondary" onclick="closeFormLaporanDrawer()">Batal</button>
            <button type="button" class="button-primary flex-grow" onclick="simpanLaporan()"><i data-lucide="save" class="icon-mr-8"></i> Simpan Laporan</button>
        </div>
    </div>
</div>

<!-- Modal Edit Workspace -->
<div id="edit-block-modal" class="modal-overlay hidden overlay-z10005">
    <div class="glass-card modal-shell modal-shell-sm">
        <button class="modal-close-btn modal-close-top-right" onclick="closeEditBlockModal()"><i data-lucide="x"></i></button>
        <h2 class="section-title mb-8">Pengaturan Blok</h2>
        <p class="text-secondary modal-desc">Perbarui profil blok dan periode awal pembukuan (General Settings).</p>
        
        <input type="hidden" id="edit-blok-id">
        <div class="mb-16">
            <label class="card-label">Nama Blok</label>
            <input type="text" id="edit-nama-blok" class="input-field mt-8 input-left-20">
        </div>
        <div class="mb-16">
            <label class="card-label">Koordinator</label>
            <input type="text" id="edit-koordinator-blok" class="input-field mt-8 input-left-20">
        </div>
        <div class="mb-16 inline-form-row">
            <div style="flex: 1;">
                <label class="card-label">Bulan Mulai Iuran</label>
                <select id="edit-periode-bulan" class="input-field select-custom mt-8 input-left-20">
                    <option value="0">Januari</option><option value="1">Februari</option><option value="2">Maret</option>
                    <option value="3">April</option><option value="4">Mei</option><option value="5">Juni</option>
                    <option value="6">Juli</option><option value="7">Agustus</option><option value="8">September</option>
                    <option value="9">Oktober</option><option value="10">November</option><option value="11">Desember</option>
                </select>
            </div>
            <div style="flex: 1;">
                <label class="card-label">Tahun Mulai</label>
                <input type="number" id="edit-periode-tahun" class="input-field mt-8 input-left-20" placeholder="Cth: 2026">
            </div>
        </div>
        <div class="mb-32">
            <label class="card-label">Ubah Logo Blok (Opsional)</label>
            <input type="file" id="edit-logo-blok" accept="image/*" class="input-field mt-8 input-file-pad">
        </div>
        <button class="button-primary button-full-center" onclick="submitEditBlock(this)">Simpan Pengaturan</button>
    </div>
</div>

<!-- Modal Add Workspace (Card Stack Form) dipindah ke luar agar tidak nyangkut -->
<div id="add-block-modal" class="modal-overlay hidden modal-stack-bg overlay-z10005">
    <button class="modal-close-btn" style="position: absolute; top: 24px; right: 24px; z-index: 10001;" onclick="closeAddBlockModal()"><i data-lucide="x"></i></button>

    <div class="stack-container" id="form-stack">
        <!-- Step 1: Nama Blok -->
        <div class="stack-card variant-1">
            <div class="stack-card-header">
                <span class="stack-chip">Langkah 1</span>
                <span class="stack-card-number">01/04</span>
            </div>
            <div>
                <h2>Blok Baru</h2>
                <p>Tentukan nama blok atau area yang ingin Anda tambahkan ke dalam sistem SmaRT.</p>
                <input type="text" id="input-nama-blok" class="input-field input-stack" placeholder="Contoh: Blok C">
            </div>
            <div class="stack-card-footer">
                <button class="button-secondary next-stack-btn rounded-12">Lanjut <i data-lucide="arrow-right" class="icon-16 icon-ml-8"></i></button>
            </div>
        </div>

        <!-- Step 2: Koordinator -->
        <div class="stack-card variant-2">
            <div class="stack-card-header">
                <span class="stack-chip">Langkah 2</span>
                <span class="stack-card-number">02/04</span>
            </div>
            <div>
                <h2>Koordinator</h2>
                <p>Siapa yang akan mengelola dan bertanggung jawab penuh atas blok ini?</p>
                <input type="text" id="input-koordinator-blok" class="input-field input-stack" placeholder="Nama Lengkap...">
            </div>
            <div class="stack-card-footer">
                <button class="button-secondary next-stack-btn rounded-12">Lanjut <i data-lucide="arrow-right" class="icon-16 icon-ml-8"></i></button>
            </div>
        </div>

        <!-- Step 3: Upload Gambar / Logo (Baru) -->
        <div class="stack-card variant-4">
            <div class="stack-card-header">
                <span class="stack-chip">Langkah 3</span>
                <span class="stack-card-number">03/04</span>
            </div>
            <div>
                <h2>Logo Blok</h2>
                <p>Unggah foto visual blok/gedung. (Jika dilewati, gambar default akan digunakan).</p>
                <div class="upload-box-container">
                    <input type="file" id="blok-image-upload" accept="image/*" class="upload-input-hidden">
                    <div class="upload-box-visual">
                        <i data-lucide="upload-cloud" class="upload-icon"></i>
                        <p id="upload-text-main" class="upload-text-main">Klik untuk Unggah Foto</p>
                        <p id="upload-text-sub" class="upload-text-sub">JPG, PNG maks 2MB</p>
                    </div>
                </div>
            </div>
            <div class="stack-card-footer">
                <button class="button-secondary next-stack-btn rounded-12">Lewati / Lanjut <i data-lucide="arrow-right" class="icon-16 icon-ml-8"></i></button>
            </div>
        </div>

        <!-- Step 4: Confirmation -->
        <div class="stack-card variant-3">
            <div class="stack-card-header">
                <span class="stack-chip">Langkah 4</span>
                <span class="stack-card-number">04/04</span>
            </div>
            <div>
                <h2>Selesai!</h2>
                <p>Periksa kembali data yang dimasukkan. Simpan data workspace baru ke dalam database?</p>
            </div>
            <div class="stack-card-footer">
                <button class="button-primary button-full-center" onclick="submitNewBlock(this)">Simpan Blok <i data-lucide="check-circle" class="icon-16 icon-ml-8"></i></button>
            </div>
        </div>
    </div>
</div>

<!-- GSAP Infinity Gallery Modal -->
<div id="gsap-gallery-modal" class="hidden">
    <div class="gsap-bg-container">
        <div id="gsap-bg1" class="gsap-bg-image active"></div>
        <div id="gsap-bg2" class="gsap-bg-image"></div>
    </div>
    <div class="gsap-bg-noise"></div>
    
    <!-- Area Scroll Palsu (Untuk memicu ScrollTrigger) -->
    <div class="gsap-scroll-container hide-scrollbar"><div class="gsap-scroll-spacer"></div></div>

    <div class="gsap-ui-layer">
        <div class="gsap-header">
            <div class="gsap-brand"><i data-lucide="image"></i> Galeri Kegiatan</div>
            <button class="gsap-close-btn" onclick="closeGsapGallery()"><i data-lucide="x"></i></button>
        </div>
        <div class="gsap-controls">
            <button class="gsap-nav-btn gsap-prev"><i data-lucide="chevron-left"></i></button>
            <button class="gsap-nav-btn gsap-next"><i data-lucide="chevron-right"></i></button>
        </div>
    </div>
    <div class="gsap-gallery"><ul class="gsap-cards"></ul></div>
    <div class="gsap-loader">MEMUAT...</div>
</div>