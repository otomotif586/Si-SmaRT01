window.initInfo = function() {
    if(typeof lucide !== 'undefined') lucide.createIcons();
    loadWebSettings();
}

window.switchInfoTab = function(tabId, btnElement) {
    document.querySelectorAll('.info-tab-content').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('active-tab');
    });
    document.querySelectorAll('#page-info .sub-nav-tab').forEach(el => el.classList.remove('active'));
    
    const target = document.getElementById(tabId);
    target.classList.remove('hidden');
    target.classList.add('active-tab');
    if (btnElement) btnElement.classList.add('active');
    
    if (tabId === 'info-umum') loadWebSettings();
    else if (tabId === 'info-menu') loadCmsMenus();
    else if (tabId === 'info-blog') loadCmsBlogs();
    else if (tabId === 'info-transparansi') loadWebSettings();
}

window.closeInfoModal = function(id) {
    document.getElementById(id).classList.add('hidden');
}

// ==========================================
// 1. CRUD PENGATURAN UMUM
// ==========================================
window.loadWebSettings = function() {
    fetch('api/info/get_settings.php')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                const data = res.data;
                if(document.getElementById('web_nama')) document.getElementById('web_nama').value = data.web_nama || '';
                if(document.getElementById('web_email')) document.getElementById('web_email').value = data.web_email || '';
                if(document.getElementById('web_telepon')) document.getElementById('web_telepon').value = data.web_telepon || '';
                if(document.getElementById('web_alamat')) document.getElementById('web_alamat').value = data.web_alamat || '';
                if(document.getElementById('web_visi')) document.getElementById('web_visi').value = data.web_visi || '';
                if(document.getElementById('web_misi')) document.getElementById('web_misi').value = data.web_misi || '';
                
                if(document.getElementById('web_title')) document.getElementById('web_title').value = data.web_title || '';
                if(document.getElementById('web_hero_title')) document.getElementById('web_hero_title').value = data.web_hero_title || '';
                if(document.getElementById('web_use_gallery')) document.getElementById('web_use_gallery').value = data.web_use_gallery || 'Ya';
                if(document.getElementById('web_transparansi_judul')) document.getElementById('web_transparansi_judul').value = data.web_transparansi_judul || '';
                if(document.getElementById('web_transparansi_deskripsi')) document.getElementById('web_transparansi_deskripsi').value = data.web_transparansi_deskripsi || '';

                // Tampilkan Tautan Preview Gambar jika sudah ada datanya
                if(document.getElementById('preview_web_logo') && data.web_logo) 
                    document.getElementById('preview_web_logo').innerHTML = `<a href="${data.web_logo}" target="_blank" class="badge bg-emerald-light text-emerald" style="font-size:0.6rem;">Lihat</a>`;
                if(document.getElementById('preview_web_favicon') && data.web_favicon) 
                    document.getElementById('preview_web_favicon').innerHTML = `<a href="${data.web_favicon}" target="_blank" class="badge bg-emerald-light text-emerald" style="font-size:0.6rem;">Lihat</a>`;
                if(document.getElementById('preview_web_hero_image') && data.web_hero_image) 
                    document.getElementById('preview_web_hero_image').innerHTML = `<a href="${data.web_hero_image}" target="_blank" class="badge bg-emerald-light text-emerald" style="font-size:0.6rem;">Lihat Banner</a>`;
                if(document.getElementById('preview_web_slider_images') && data.web_slider_images) {
                    try { const sliders = JSON.parse(data.web_slider_images); document.getElementById('preview_web_slider_images').innerHTML = `<span class="badge bg-blue-light text-blue" style="font-size:0.6rem;">${sliders.length} Gambar Tersimpan</span>`; } 
                    catch(e) {}
                }
                if(document.getElementById('preview_web_transparansi_file') && data.web_transparansi_file) 
                    document.getElementById('preview_web_transparansi_file').innerHTML = `<a href="${data.web_transparansi_file}" target="_blank" class="badge bg-emerald-light text-emerald" style="font-size:0.6rem;">Lihat Laporan</a>`;
            }
        }).catch(e => console.log('API Settings belum siap'));
}

window.saveWebSettings = function() {
    const fd = new FormData();
    fd.append('web_nama', document.getElementById('web_nama').value);
    fd.append('web_email', document.getElementById('web_email').value);
    fd.append('web_telepon', document.getElementById('web_telepon').value);
    fd.append('web_alamat', document.getElementById('web_alamat').value);
    fd.append('web_visi', document.getElementById('web_visi').value);
    fd.append('web_misi', document.getElementById('web_misi').value);
    
    fd.append('web_title', document.getElementById('web_title').value);
    fd.append('web_hero_title', document.getElementById('web_hero_title').value);
    fd.append('web_use_gallery', document.getElementById('web_use_gallery').value);
    if (document.getElementById('web_transparansi_judul')) fd.append('web_transparansi_judul', document.getElementById('web_transparansi_judul').value);
    if (document.getElementById('web_transparansi_deskripsi')) fd.append('web_transparansi_deskripsi', document.getElementById('web_transparansi_deskripsi').value);

    // Mengelola Input File
    const logoFile = document.getElementById('web_logo_file').files[0];
    if(logoFile) fd.append('web_logo', logoFile);
    
    const faviconFile = document.getElementById('web_favicon_file').files[0];
    if(faviconFile) fd.append('web_favicon', faviconFile);
    
    const heroFile = document.getElementById('web_hero_image_file').files[0];
    if(heroFile) fd.append('web_hero_image', heroFile);
    
    const sliderFiles = document.getElementById('web_slider_images_files').files;
    for(let i = 0; i < sliderFiles.length; i++) {
        fd.append('web_slider_images[]', sliderFiles[i]);
    }
    
    const transFile = document.getElementById('web_transparansi_file_input') ? document.getElementById('web_transparansi_file_input').files[0] : null;
    if(transFile) fd.append('web_transparansi_file', transFile);

    showLoading('Menyimpan Profil Web...');
    fetch('api/info/save_settings.php', { method: 'POST', body: fd })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                showToast("Pengaturan web & media berhasil disimpan!");
                // Bersihkan input file agar siap digunakan kembali dan memuat ulang preview
                document.querySelectorAll('input[type="file"]').forEach(el => el.value = '');
                loadWebSettings(); 
            }
            else showToast(res.message, 'error');
        });
}

// ==========================================
// 2. CRUD MENU NAVIGASI FRONTEND
// ==========================================
window.loadCmsMenus = function() {
    const tbody = document.getElementById('cms-menu-body');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5">Memuat data...</td></tr>';
    
    fetch('api/info/get_menus.php')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success' && res.data.length > 0) {
                let html = '';
                res.data.forEach(m => {
                    const statusClass = m.status === 'Aktif' ? 'bg-emerald-light text-emerald' : 'bg-secondary-light text-secondary';
                    html += `
                    <tr>
                        <td class="font-bold text-center">${m.urutan}</td>
                        <td class="font-bold">${m.nama_menu}</td>
                        <td class="text-blue" style="font-size:0.8rem;">${m.url}</td>
                        <td><span class="badge ${statusClass}">${m.status}</span></td>
                        <td class="text-right">
                            <button class="button-secondary button-sm" onclick="editMenu(${m.id}, '${m.nama_menu}', '${m.url}', ${m.urutan}, '${m.status}')"><i data-lucide="edit" style="width:14px; height:14px;"></i></button>
                            <button class="button-secondary button-sm" style="color: #ef4444;" onclick="deleteMenu(${m.id})"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                        </td>
                    </tr>`;
                });
                tbody.innerHTML = html;
                if(typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-secondary">Belum ada menu yang dibuat.</td></tr>';
            }
        });
}

window.addMenu = function() {
    document.getElementById('cms-menu-id').value = '0';
    document.getElementById('cms-menu-nama').value = '';
    document.getElementById('cms-menu-url').value = '';
    document.getElementById('cms-menu-urutan').value = '1';
    document.getElementById('cms-menu-status').value = 'Aktif';
    document.getElementById('modal-menu-title').innerText = 'Tambah Menu';
    document.getElementById('modal-cms-menu').classList.remove('hidden');
}

window.editMenu = function(id, nama, url, urutan, status) {
    document.getElementById('cms-menu-id').value = id;
    document.getElementById('cms-menu-nama').value = nama;
    document.getElementById('cms-menu-url').value = url;
    document.getElementById('cms-menu-urutan').value = urutan;
    document.getElementById('cms-menu-status').value = status;
    document.getElementById('modal-menu-title').innerText = 'Edit Menu';
    document.getElementById('modal-cms-menu').classList.remove('hidden');
}

window.saveCmsMenu = function() {
    const fd = new FormData();
    fd.append('id', document.getElementById('cms-menu-id').value);
    fd.append('nama_menu', document.getElementById('cms-menu-nama').value);
    fd.append('url', document.getElementById('cms-menu-url').value);
    fd.append('urutan', document.getElementById('cms-menu-urutan').value);
    fd.append('status', document.getElementById('cms-menu-status').value);

    fetch('api/info/save_menu.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res=>{
        if(res.status==='success') { showToast(res.message); closeInfoModal('modal-cms-menu'); loadCmsMenus(); } 
        else showToast(res.message, 'error'); 
    });
}

window.deleteMenu = function(id) {
    if(confirm('Hapus menu navigasi ini?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/info/delete_menu.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res=>{ if(res.status==='success') loadCmsMenus(); });
    }
}

// ==========================================
// 3. CRUD BLOG & ARTIKEL
// ==========================================
window.loadCmsBlogs = function() {
    const container = document.getElementById('cms-blog-list');
    container.innerHTML = '<p class="text-secondary col-span-full text-center py-5">Memuat artikel...</p>';
    
    fetch('api/info/get_blogs.php')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success' && res.data.length > 0) {
                let html = '';
                res.data.forEach(b => {
                    const statusClass = b.status === 'Publish' ? 'bg-blue-light text-blue' : 'bg-secondary-light text-secondary';
                    html += `
                    <div class="glass-card" style="padding: 20px; display: flex; flex-direction: column; gap: 12px;">
                        <div style="display:flex; justify-content:space-between; align-items:flex-start;">
                            <span class="badge ${statusClass}">${b.status}</span>
                            <span style="font-size:0.7rem; color:var(--text-secondary-color);"><i data-lucide="clock" style="width:12px; height:12px; display:inline;"></i> ${b.created_at}</span>
                        </div>
                        <h3 style="margin:0; font-size:1.1rem; line-height:1.4;">${b.judul}</h3>
                        <p style="margin:0; font-size:0.85rem; color:var(--text-secondary-color); line-height:1.5; display:-webkit-box; -webkit-line-clamp:3; -webkit-box-orient:vertical; overflow:hidden;">${b.konten}</p>
                        <div style="display: flex; gap: 8px; margin-top: auto; border-top: 1px dashed var(--border-color); padding-top: 16px;">
                            <button class="button-secondary flex-1" style="padding: 8px;" onclick="editBlog(${b.id}, '${encodeURIComponent(b.judul)}', '${encodeURIComponent(b.konten)}', '${b.status}')"><i data-lucide="edit" style="width:14px;height:14px;margin-right:6px;"></i> Edit</button>
                            <button class="button-secondary" style="padding: 8px; color: #ef4444;" onclick="deleteBlog(${b.id})"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                        </div>
                    </div>`;
                });
                container.innerHTML = html;
                if(typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                container.innerHTML = '<p class="text-secondary col-span-full text-center py-5">Belum ada artikel dipublikasikan.</p>';
            }
        });
}

window.addBlog = function() {
    document.getElementById('cms-blog-id').value = '0';
    document.getElementById('cms-blog-judul').value = '';
    document.getElementById('cms-blog-konten').value = '';
    document.getElementById('cms-blog-status').value = 'Publish';
    document.getElementById('modal-blog-title').innerText = 'Tulis Artikel Baru';
    document.getElementById('modal-cms-blog').classList.remove('hidden');
}

window.editBlog = function(id, judulEncoded, kontenEncoded, status) {
    document.getElementById('cms-blog-id').value = id;
    document.getElementById('cms-blog-judul').value = decodeURIComponent(judulEncoded);
    document.getElementById('cms-blog-konten').value = decodeURIComponent(kontenEncoded);
    document.getElementById('cms-blog-status').value = status;
    document.getElementById('modal-blog-title').innerText = 'Edit Artikel';
    document.getElementById('modal-cms-blog').classList.remove('hidden');
}

window.saveCmsBlog = function() {
    const fd = new FormData();
    fd.append('id', document.getElementById('cms-blog-id').value);
    fd.append('judul', document.getElementById('cms-blog-judul').value);
    fd.append('konten', document.getElementById('cms-blog-konten').value);
    fd.append('status', document.getElementById('cms-blog-status').value);

    showLoading('Memproses...');
    fetch('api/info/save_blog.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res=>{
        if(res.status==='success') { showToast(res.message); closeInfoModal('modal-cms-blog'); loadCmsBlogs(); } 
        else showToast(res.message, 'error'); 
    });
}

window.deleteBlog = function(id) {
    if(confirm('Hapus artikel ini selamanya?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/info/delete_blog.php', { method: 'POST', body: fd }).then(r=>r.json()).then(res=>{ if(res.status==='success') loadCmsBlogs(); });
    }
}