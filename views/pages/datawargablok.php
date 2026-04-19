<!-- Drawer Modal: Form Data Warga -->
<div id="drawer-warga" class="modal-overlay hidden drawer-warga-overlay">
    <div class="drawer-panel glass-card">
        <div class="drawer-header">
            <div>
                <h2 class="ws-title">Tambah Data Warga</h2>
                <p class="text-secondary drawer-subtitle">Lengkapi form berikut untuk menambahkan warga ke dalam blok.</p>
            </div>
            <button class="modal-close-btn" onclick="closeFormWarga()"><i data-lucide="x"></i></button>
        </div>
        
        <div class="drawer-body hide-scrollbar">
            <form id="form-tambah-warga">
                <!-- Info Utama -->
                <h4 class="form-section-title">1. Informasi Utama</h4>
                <div class="form-grid">
                    <div class="form-group">
                        <label class="card-label">Workspace Blok</label>
                        <select id="warga_blok_id" class="input-field select-custom"></select>
                    </div>
                    <div class="form-group">
                        <label class="card-label">No Rumah (2 Digit)</label>
                        <input type="text" id="warga_norumah" class="input-field" placeholder="Cth: 01" maxlength="2" inputmode="numeric">
                    </div>
                    <div class="form-group grid-col-full">
                        <label class="card-label">No Kartu Keluarga (KK)</label>
                        <input type="number" id="warga_nokk" class="input-field" placeholder="16 Digit No KK">
                    </div>
                    <div class="form-group grid-col-full">
                        <label class="card-label">NIK Kepala Keluarga</label>
                        <input type="number" id="warga_nik_kepala" class="input-field" placeholder="16 Digit NIK">
                    </div>
                    <div class="form-group grid-col-full">
                        <label class="card-label">Kepala Keluarga</label>
                        <input type="text" id="warga_kepala" class="input-field" placeholder="Nama Lengkap Kepala Keluarga">
                    </div>
                    <div class="form-group grid-col-full">
                        <label class="card-label">Nomor WhatsApp</label>
                        <input type="number" id="warga_nowa" class="input-field" placeholder="Cth: 08123456789">
                    </div>
                    <div class="form-group">
                        <label class="card-label">Tempat Lahir</label>
                        <input type="text" id="warga_tempatlahir" class="input-field" placeholder="Kota Lahir">
                    </div>
                    <div class="form-group">
                        <label class="card-label">Tanggal Lahir</label>
                        <input type="date" id="warga_tgllahir" class="input-field input-left-20">
                    </div>
                </div>

                <!-- Status Pernikahan -->
                <h4 class="form-section-title">2. Status & Keluarga</h4>
                <div class="form-grid">
                    <div class="form-group grid-col-full">
                        <label class="card-label">Status Pernikahan</label>
                        <select id="warga_pernikahan" class="input-field select-custom" onchange="togglePasangan(this.value)">
                            <option value="Lajang">Lajang</option>
                            <option value="Menikah">Menikah</option>
                            <option value="Pisah">Pisah</option>
                        </select>
                    </div>
                </div>

                <!-- Dynamic: Pasangan -->
                <div id="section-pasangan" class="dynamic-section hidden">
                    <div class="form-grid">
                        <div class="form-group">
                            <label class="card-label">NIK Pasangan</label>
                            <input type="text" inputmode="numeric" id="pasangan_nik" class="input-field" placeholder="16 Digit NIK">
                        </div>
                        <div class="form-group">
                            <label class="card-label">Nama Pasangan</label>
                            <input type="text" id="pasangan_nama" class="input-field" placeholder="Nama Suami / Istri">
                        </div>
                        <div class="form-group">
                            <label class="card-label">Tempat Lahir Pasangan</label>
                            <input type="text" id="pasangan_tempat" class="input-field" placeholder="Kota Lahir">
                        </div>
                        <div class="form-group">
                            <label class="card-label">Tanggal Lahir Pasangan</label>
                            <input type="date" id="pasangan_tgl" class="input-field input-left-20">
                        </div>
                    </div>
                </div>

                <!-- Anak -->
                <div class="form-grid mt-16">
                    <div class="form-group grid-col-full">
                        <label class="card-label">Jumlah Anak</label>
                        <select id="warga_jumlah_anak" class="input-field select-custom" onchange="generateAnakFields(this.value)">
                            <option value="0">Tidak ada anak</option>
                            <?php for($i=1; $i<=10; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> Anak</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div id="container-anak" class="dynamic-section hidden"></div>

                <!-- Orang Lain / Penghuni Tambahan -->
                <div class="form-grid mt-16">
                    <div class="form-group grid-col-full">
                        <label class="card-label">Penghuni Lainnya (Selain Pasangan/Anak)</label>
                        <select id="warga_jumlah_orang" class="input-field select-custom" onchange="generateOrangLainFields(this.value)">
                            <option value="0">Tidak ada penghuni lain</option>
                            <?php for($i=1; $i<=5; $i++): ?>
                                <option value="<?= $i ?>"><?= $i ?> Orang</option>
                            <?php endfor; ?>
                        </select>
                    </div>
                </div>
                <div id="container-oranglain" class="dynamic-section hidden"></div>

                <!-- Status Warga & Info Tambahan -->
                <h4 class="form-section-title">3. Informasi Tambahan</h4>
                <div class="form-grid">
                    <div class="form-group grid-col-full">
                        <label class="card-label">Status Warga</label>
                        <select id="warga_status" class="input-field select-custom">
                            <option value="Tetap">Warga Tetap</option>
                            <option value="Kontrak">Warga Kontrak</option>
                            <option value="Weekend">Warga Weekend (Opsional)</option>
                        </select>
                    </div>
                </div>

                <!-- Kendaraan & Dokumen -->
                <div class="form-group dynamic-add-section">
                    <div class="dynamic-add-header"><label class="card-label">Kendaraan Pribadi</label><button type="button" class="button-link text-emerald font-bold" onclick="addKendaraanField()"><i data-lucide="plus"></i> Tambah</button></div>
                    <div id="container-kendaraan"></div>
                </div>
                <div class="form-group dynamic-add-section">
                    <div class="dynamic-add-header"><label class="card-label">Dokumen Pendukung</label><button type="button" class="button-link text-emerald font-bold" onclick="addDokumenField()"><i data-lucide="plus"></i> Tambah File</button></div>
                    <div id="container-dokumen"><input type="file" class="input-field file-input-modern dokumen-file"></div>
                </div>
            </form>
        </div>
        
        <div class="drawer-footer">
            <button type="button" class="button-secondary" onclick="closeFormWarga()">Batal</button>
            <button type="button" class="button-primary flex-grow" onclick="simpanDataWarga()"><i data-lucide="save" class="mr-[8px]"></i> Simpan Data</button>
        </div>
    </div>
</div>

<!-- Modal Detail Warga Full (Untuk melihat semua data diri & iuran) -->
<div id="modal-detail-warga-full" class="modal-overlay hidden detail-warga-overlay">
    <div class="glass-card hide-scrollbar detail-warga-card">
        <button class="modal-close-btn detail-warga-close" onclick="closeDetailWarga()"><i data-lucide="x"></i></button>
        <div id="modal-detail-warga-content">
            <!-- Konten detail akan di-load di sini via AJAX -->
        </div>
    </div>
</div>

<style>
.drawer-warga-overlay {
    z-index: 10010 !important;
    align-items: flex-end;
    justify-content: flex-end;
    padding: 0;
}
.detail-warga-overlay {
    z-index: 10020 !important;
    align-items: center;
    justify-content: center;
    padding: 16px;
}
.detail-warga-card {
    width: 100%;
    max-width: 700px;
    max-height: 90dvh;
    overflow-y: auto;
    padding: 32px;
    position: relative;
    border-radius: 24px;
}
.detail-warga-close {
    position: absolute;
    top: 16px;
    right: 16px;
    z-index: 10;
}
</style>