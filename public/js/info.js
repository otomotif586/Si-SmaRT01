let quillBlog;

function smartAssetUrl(path) {
    const value = String(path || '').trim();
    if (!value) return '';
    if (/^(?:https?:)?\/\//i.test(value) || /^(?:data|mailto|tel):/i.test(value)) {
        return value;
    }

    const basePath = String(window.__SMART_ASSET_BASE_PATH__ || '').replace(/\/+$/, '');
    const cleanPath = value.replace(/^\/+/, '');
    return `${basePath}/${cleanPath}`.replace(/([^:]\/)\/+/g, '$1');
}

window.initInfo = function () {
    if (typeof lucide !== 'undefined') lucide.createIcons();
    loadWebSettings();
    setupCmsPreviews();

    // Initialize Quill
    if (document.getElementById('cms-blog-editor')) {
        quillBlog = new Quill('#cms-blog-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{ 'header': [1, 2, 3, false] }],
                    ['bold', 'italic', 'underline', 'strike'],
                    [{ 'list': 'ordered' }, { 'list': 'bullet' }],
                    ['link', 'blockquote', 'code-block'],
                    ['clean']
                ]
            }
        });
    }
}

// Menangani Preview Gambar Real-time saat Unggah
function setupCmsPreviews() {
    const pairs = [
        { input: 'web_logo_file', preview: 'preview_web_logo' },
        { input: 'web_favicon_file', preview: 'preview_web_favicon' },
        { input: 'web_hero_image_file', preview: 'preview_web_hero_image' }
    ];

    // Tambah untuk slider 1-3
    for (let i = 1; i <= 3; i++) {
        pairs.push({ input: `web_slider_${i}_image_file`, preview: `preview_web_slider_${i}_image` });
    }

    // Tambah untuk wisata 1-2
    for (let i = 1; i <= 2; i++) {
        pairs.push({ input: `web_wisata_${i}_image_file`, preview: `preview_web_wisata_${i}_image` });
    }

    // Tambah untuk blog
    pairs.push({ input: 'cms-blog-thumbnail-file', preview: 'preview_blog_thumbnail' });
    pairs.push({ input: 'cms-blog-video-file', preview: 'preview_blog_video' });

    pairs.forEach(pair => {
        const input = document.getElementById(pair.input);
        const preview = document.getElementById(pair.preview);
        if (input && preview) {
            input.addEventListener('change', function () {
                if (this.files && this.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        preview.innerHTML = `<img src="${e.target.result}" style="height:20px; width:20px; object-fit:contain; border-radius:4px; margin-right:8px; vertical-align:middle; border:1px solid var(--accent-color);"><span class="badge bg-blue-light text-blue" style="font-size:0.5rem;">Pratinjau</span>`;
                    }
                    reader.readAsDataURL(this.files[0]);
                }
            });
        }
    });
}

window.switchInfoTab = function (tabId, btnElement) {
    document.querySelectorAll('.info-tab-content').forEach(el => {
        el.classList.add('hidden');
        el.classList.remove('active-tab');
    });
    document.querySelectorAll('#page-info .info-tab-btn').forEach(el => el.classList.remove('active'));

    const target = document.getElementById(tabId);
    if (target) {
        target.classList.remove('hidden');
        target.classList.add('active-tab');
    }
    if (btnElement) btnElement.classList.add('active');

    if (tabId === 'info-umum') loadWebSettings();
    else if (tabId === 'info-menu') loadCmsMenus();
    else if (tabId === 'info-blog') loadCmsBlogs();
    else if (tabId === 'info-transparansi') loadWebSettings();
    else if (tabId === 'info-struktur') loadCmsPengurus();
}

window.closeInfoModal = function (id) {
    const modal = document.getElementById(id);
    if (modal) modal.classList.add('hidden');
}

// ==========================================
// 1. CRUD PENGATURAN UMUM
// ==========================================
window.loadWebSettings = function () {
    fetch('api/cms_get_settings.php')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                const data = res.data;
                if (document.getElementById('web_nama')) document.getElementById('web_nama').value = data.web_nama || '';
                if (document.getElementById('web_email')) document.getElementById('web_email').value = data.web_email || '';
                if (document.getElementById('web_telepon')) document.getElementById('web_telepon').value = data.web_telepon || '';
                if (document.getElementById('web_alamat')) document.getElementById('web_alamat').value = data.web_alamat || '';
                if (document.getElementById('web_visi')) document.getElementById('web_visi').value = data.web_visi || '';
                if (document.getElementById('web_misi')) document.getElementById('web_misi').value = data.web_misi || '';

                if (document.getElementById('web_title')) document.getElementById('web_title').value = data.web_title || '';
                if (document.getElementById('web_hero_title')) document.getElementById('web_hero_title').value = data.web_hero_title || '';
                if (document.getElementById('web_use_gallery')) document.getElementById('web_use_gallery').value = data.web_use_gallery || 'Ya';
                if (document.getElementById('web_transparansi_deskripsi')) document.getElementById('web_transparansi_deskripsi').value = data.web_transparansi_deskripsi || '';

                // Info Penting Warga
                if (document.getElementById('web_info_penting_judul')) document.getElementById('web_info_penting_judul').value = data.web_info_penting_judul || '';
                if (document.getElementById('web_info_penting_deskripsi')) document.getElementById('web_info_penting_deskripsi').value = data.web_info_penting_deskripsi || '';
                for (let i = 1; i <= 4; i++) {
                    if (document.getElementById(`web_info_item_${i}_icon`)) document.getElementById(`web_info_item_${i}_icon`).value = data[`web_info_item_${i}_icon`] || '';
                    if (document.getElementById(`web_info_item_${i}_title`)) document.getElementById(`web_info_item_${i}_title`).value = data[`web_info_item_${i}_title`] || '';
                    if (document.getElementById(`web_info_item_${i}_desc`)) document.getElementById(`web_info_item_${i}_desc`).value = data[`web_info_item_${i}_desc`] || '';
                }

                // Tampilkan Tautan Preview Gambar jika sudah ada datanya
                if (document.getElementById('preview_web_logo') && data.web_logo) {
                    const webLogo = smartAssetUrl(data.web_logo);
                    document.getElementById('preview_web_logo').innerHTML = `<img src="${webLogo}" style="height:20px; width:20px; object-fit:contain; border-radius:4px; margin-right:8px; vertical-align:middle; border:1px solid var(--border-color);"><a href="${webLogo}" target="_blank" class="badge bg-emerald-light text-emerald" style="font-size:0.6rem;">Lihat</a>`;
                }
                if (document.getElementById('preview_web_favicon') && data.web_favicon) {
                    const webFavicon = smartAssetUrl(data.web_favicon);
                    document.getElementById('preview_web_favicon').innerHTML = `<img src="${webFavicon}" style="height:20px; width:20px; object-fit:contain; border-radius:4px; margin-right:8px; vertical-align:middle; border:1px solid var(--border-color);"><a href="${webFavicon}" target="_blank" class="badge bg-emerald-light text-emerald" style="font-size:0.6rem;">Lihat</a>`;
                }
                if (document.getElementById('preview_web_hero_image') && data.web_hero_image) {
                    const heroImage = smartAssetUrl(data.web_hero_image);
                    document.getElementById('preview_web_hero_image').innerHTML = `<a href="${heroImage}" target="_blank" class="badge bg-emerald-light text-emerald" style="font-size:0.6rem;">Lihat Banner</a>`;
                }
                if (document.getElementById('preview_web_slider_images') && data.web_slider_images) {
                    try { const sliders = JSON.parse(data.web_slider_images); document.getElementById('preview_web_slider_images').innerHTML = `<span class="badge bg-blue-light text-blue" style="font-size:0.6rem;">${sliders.length} Gambar Tersimpan</span>`; }
                    catch (e) { }
                }
                if (document.getElementById('preview_web_transparansi_file') && data.web_transparansi_file) {
                    const transparansiFile = smartAssetUrl(data.web_transparansi_file);
                    document.getElementById('preview_web_transparansi_file').innerHTML = `<a href="${transparansiFile}" target="_blank" class="badge bg-emerald-light text-emerald" style="font-size:0.6rem;">Lihat Laporan</a>`;
                }

                // Parallax Slider Data
                for (let i = 1; i <= 3; i++) {
                    if (document.getElementById(`web_slider_${i}_title`)) document.getElementById(`web_slider_${i}_title`).value = data[`web_slider_${i}_title`] || '';
                    if (document.getElementById(`web_slider_${i}_subtitle`)) document.getElementById(`web_slider_${i}_subtitle`).value = data[`web_slider_${i}_subtitle`] || '';
                    if (document.getElementById(`web_slider_${i}_description`)) document.getElementById(`web_slider_${i}_description`).value = data[`web_slider_${i}_description`] || '';
                    if (document.getElementById(`preview_web_slider_${i}_image`) && data[`web_slider_${i}_image`]) {
                        const sliderImage = smartAssetUrl(data[`web_slider_${i}_image`]);
                        document.getElementById(`preview_web_slider_${i}_image`).innerHTML = `<a href="${sliderImage}" target="_blank" class="badge bg-emerald-light text-emerald" style="font-size:0.5rem;">Lihat</a>`;
                    }
                }

                // Wisata Data
                for (let i = 1; i <= 2; i++) {
                    if (document.getElementById(`web_wisata_${i}_title`)) document.getElementById(`web_wisata_${i}_title`).value = data[`web_wisata_${i}_title`] || '';
                    if (document.getElementById(`web_wisata_${i}_category`)) document.getElementById(`web_wisata_${i}_category`).value = data[`web_wisata_${i}_category`] || '';
                    if (document.getElementById(`web_wisata_${i}_description`)) document.getElementById(`web_wisata_${i}_description`).value = data[`web_wisata_${i}_description`] || '';
                    if (document.getElementById(`preview_web_wisata_${i}_image`) && data[`web_wisata_${i}_image`]) {
                        const wisataImage = smartAssetUrl(data[`web_wisata_${i}_image`]);
                        document.getElementById(`preview_web_wisata_${i}_image`).innerHTML = `<a href="${wisataImage}" target="_blank" class="badge bg-emerald-light text-emerald" style="font-size:0.5rem;">Lihat</a>`;
                    }
                }
            }
        }).catch(e => console.log('API Settings belum siap'));
}

window.saveWebSettings = function () {
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
    if (document.getElementById('web_transparansi_deskripsi')) fd.append('web_transparansi_deskripsi', document.getElementById('web_transparansi_deskripsi').value);

    // Info Penting Warga
    if (document.getElementById('web_info_penting_judul')) fd.append('web_info_penting_judul', document.getElementById('web_info_penting_judul').value);
    if (document.getElementById('web_info_penting_deskripsi')) fd.append('web_info_penting_deskripsi', document.getElementById('web_info_penting_deskripsi').value);
    for (let i = 1; i <= 4; i++) {
        if (document.getElementById(`web_info_item_${i}_icon`)) fd.append(`web_info_item_${i}_icon`, document.getElementById(`web_info_item_${i}_icon`).value);
        if (document.getElementById(`web_info_item_${i}_title`)) fd.append(`web_info_item_${i}_title`, document.getElementById(`web_info_item_${i}_title`).value);
        if (document.getElementById(`web_info_item_${i}_desc`)) fd.append(`web_info_item_${i}_desc`, document.getElementById(`web_info_item_${i}_desc`).value);
    }

    // Mengelola Input File
    const logoFile = document.getElementById('web_logo_file').files[0];
    if (logoFile) fd.append('web_logo', logoFile);

    const faviconFile = document.getElementById('web_favicon_file').files[0];
    if (faviconFile) fd.append('web_favicon', faviconFile);

    const heroFile = document.getElementById('web_hero_image_file').files[0];
    if (heroFile) fd.append('web_hero_image', heroFile);

    const sliderFiles = document.getElementById('web_slider_images_files').files;
    for (let i = 0; i < sliderFiles.length; i++) {
        fd.append('web_slider_images[]', sliderFiles[i]);
    }

    const transFile = document.getElementById('web_transparansi_file_input') ? document.getElementById('web_transparansi_file_input').files[0] : null;
    if (transFile) fd.append('web_transparansi_file', transFile);

    // Parallax Slider Files & Text
    for (let i = 1; i <= 3; i++) {
        const title = document.getElementById(`web_slider_${i}_title`);
        const subtitle = document.getElementById(`web_slider_${i}_subtitle`);
        const desc = document.getElementById(`web_slider_${i}_description`);
        const fileInput = document.getElementById(`web_slider_${i}_image_file`);

        if (title) fd.append(`web_slider_${i}_title`, title.value);
        if (subtitle) fd.append(`web_slider_${i}_subtitle`, subtitle.value);
        if (desc) fd.append(`web_slider_${i}_description`, desc.value);
        if (fileInput && fileInput.files[0]) fd.append(`web_slider_${i}_image`, fileInput.files[0]);
    }

    // Wisata Files & Text
    for (let i = 1; i <= 2; i++) {
        const title = document.getElementById(`web_wisata_${i}_title`);
        const cat = document.getElementById(`web_wisata_${i}_category`);
        const desc = document.getElementById(`web_wisata_${i}_description`);
        const fileInput = document.getElementById(`web_wisata_${i}_image_file`);

        if (title) fd.append(`web_wisata_${i}_title`, title.value);
        if (cat) fd.append(`web_wisata_${i}_category`, cat.value);
        if (desc) fd.append(`web_wisata_${i}_description`, desc.value);
        if (fileInput && fileInput.files[0]) fd.append(`web_wisata_${i}_image`, fileInput.files[0]);
    }

    showLoading('Menyimpan Profil Web...');
    fetch('api/cms_save_settings.php', { method: 'POST', body: fd })
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
window.loadCmsMenus = function () {
    const tbody = document.getElementById('cms-menu-body');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5">Memuat data...</td></tr>';

    fetch('api/cms_get_menus.php')
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
                if (typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-secondary">Belum ada menu yang dibuat.</td></tr>';
            }
        });
}

window.addMenu = function () {
    document.getElementById('cms-menu-id').value = '0';
    document.getElementById('cms-menu-nama').value = '';
    document.getElementById('cms-menu-url').value = '';
    document.getElementById('cms-menu-urutan').value = '1';
    document.getElementById('cms-menu-status').value = 'Aktif';
    document.getElementById('modal-menu-title').innerText = 'Tambah Menu';
    document.getElementById('modal-cms-menu').classList.remove('hidden');
}

window.editMenu = function (id, nama, url, urutan, status) {
    document.getElementById('cms-menu-id').value = id;
    document.getElementById('cms-menu-nama').value = nama;
    document.getElementById('cms-menu-url').value = url;
    document.getElementById('cms-menu-urutan').value = urutan;
    document.getElementById('cms-menu-status').value = status;
    document.getElementById('modal-menu-title').innerText = 'Edit Menu';
    document.getElementById('modal-cms-menu').classList.remove('hidden');
}

window.saveCmsMenu = function () {
    const fd = new FormData();
    fd.append('id', document.getElementById('cms-menu-id').value);
    fd.append('nama_menu', document.getElementById('cms-menu-nama').value);
    fd.append('url', document.getElementById('cms-menu-url').value);
    fd.append('urutan', document.getElementById('cms-menu-urutan').value);
    fd.append('status', document.getElementById('cms-menu-status').value);

    fetch('api/cms_save_menu.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.status === 'success') { showToast(res.message); closeInfoModal('modal-cms-menu'); loadCmsMenus(); }
        else showToast(res.message, 'error');
    });
}

window.deleteMenu = function (id) {
    if (confirm('Hapus menu navigasi ini?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/cms_delete_menu.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => { if (res.status === 'success') loadCmsMenus(); });
    }
}

// ==========================================
// 3. CRUD BLOG & ARTIKEL
// ==========================================
window.loadCmsBlogs = function () {
    const container = document.getElementById('cms-blog-list');
    container.innerHTML = '<p class="text-secondary col-span-full text-center py-5">Memuat artikel...</p>';

    fetch('api/cms_get_blogs.php')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success' && res.data.length > 0) {
                let html = '';
                res.data.forEach(b => {
                    const statusClass = b.status === 'Publish' ? 'badge bg-blue-light text-blue' : 'badge bg-secondary-light text-secondary';
                    const mediaHtml = b.thumbnail ? `<img src="${smartAssetUrl(b.thumbnail)}" alt="">` : (b.video_url ? `<video src="${smartAssetUrl(b.video_url)}" muted loop></video>` : '<img src="https://images.unsplash.com/photo-1516245834210-c4c142787335?q=80&w=800" alt="">');
                    html += `
                    <div class="premium-card">
                        ${mediaHtml}
                        <div class="absolute top-4 left-4 z-10">
                            <span class="${statusClass}">${b.status}</span>
                        </div>
                        <section>
                            <h2 style="font-size:1.2rem;">${b.judul}</h2>
                            <p style="overflow: hidden; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">${b.konten.replace(/<[^>]*>?/gm, '')}</p>
                            <div>
                                <span class="tag">${b.created_at}</span>
                                <div style="display:flex; gap:8px; z-index:30;">
                                    <button class="button-secondary button-sm" style="width:40px; padding:8px;" onclick="editBlog(${b.id}, '${encodeURIComponent(b.judul)}', '${encodeURIComponent(b.konten)}', '${b.status}', '${b.youtube_url || ''}', '${b.thumbnail || ''}', '${b.video_url || ''}')"><i data-lucide="edit" style="width:14px;height:14px;"></i></button>
                                    <button class="button-secondary button-sm" style="width:40px; padding:8px; color:#ef4444;" onclick="deleteBlog(${b.id})"><i data-lucide="trash-2" style="width:14px;height:14px;"></i></button>
                                </div>
                            </div>
                        </section>
                    </div>`;
                });
                container.innerHTML = html;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            } else {
                container.innerHTML = '<p class="text-secondary col-span-full text-center py-5">Belum ada artikel dipublikasikan.</p>';
            }
        });
}

window.addBlog = function () {
    document.getElementById('cms-blog-id').value = '0';
    document.getElementById('cms-blog-judul').value = '';
    document.getElementById('cms-blog-youtube').value = '';
    if (quillBlog) quillBlog.root.innerHTML = '';
    document.getElementById('cms-blog-status').value = 'Publish';
    document.getElementById('preview_blog_thumbnail').innerHTML = '';
    document.getElementById('preview_blog_video').innerHTML = '';
    document.getElementById('modal-blog-title').innerText = 'Tulis Artikel Baru';
    document.getElementById('modal-cms-blog').classList.remove('hidden');
}

window.editBlog = function (id, judulEncoded, kontenEncoded, status, youtube, thumb, video) {
    document.getElementById('cms-blog-id').value = id;
    document.getElementById('cms-blog-judul').value = decodeURIComponent(judulEncoded);
    document.getElementById('cms-blog-youtube').value = youtube;
    if (quillBlog) quillBlog.root.innerHTML = decodeURIComponent(kontenEncoded);
    document.getElementById('cms-blog-status').value = status;
    document.getElementById('preview_blog_thumbnail').innerHTML = thumb ? `<a href="${smartAssetUrl(thumb)}" target="_blank" class="badge bg-blue-light text-blue" style="font-size:0.5rem;">Lihat</a>` : '';
    document.getElementById('preview_blog_video').innerHTML = video ? `<a href="${smartAssetUrl(video)}" target="_blank" class="badge bg-blue-light text-blue" style="font-size:0.5rem;">Lihat</a>` : '';
    document.getElementById('modal-blog-title').innerText = 'Edit Artikel';
    document.getElementById('modal-cms-blog').classList.remove('hidden');
}

window.saveCmsBlog = function () {
    const fd = new FormData();
    fd.append('id', document.getElementById('cms-blog-id').value);
    fd.append('judul', document.getElementById('cms-blog-judul').value);
    fd.append('youtube_url', document.getElementById('cms-blog-youtube').value);
    fd.append('konten', quillBlog ? quillBlog.root.innerHTML : ''); // WordPress Style HTML
    fd.append('status', document.getElementById('cms-blog-status').value);

    const thumb = document.getElementById('cms-blog-thumbnail-file').files[0];
    if (thumb) fd.append('thumbnail', thumb);
    const video = document.getElementById('cms-blog-video-file').files[0];
    if (video) fd.append('video', video);

    showLoading('Memproses...');
    fetch('api/cms_save_blog.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => {
        if (res.status === 'success') {
            showToast(res.message);
            closeInfoModal('modal-cms-blog');
            loadCmsBlogs();
            // Reset files
            document.getElementById('cms-blog-thumbnail-file').value = '';
            document.getElementById('cms-blog-video-file').value = '';
        }
        else showToast(res.message, 'error');
    });
}

window.deleteBlog = function (id) {
    if (confirm('Hapus artikel ini selamanya?')) {
        const fd = new FormData(); fd.append('id', id);
        fetch('api/cms_delete_blog.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => { if (res.status === 'success') loadCmsBlogs(); });
    }
}

// ==========================================
// 4. CRUD STRUKTUR PENGURUS
// ==========================================
window.loadCmsPengurus = function () {
    const tbody = document.getElementById('cms-pengurus-body');
    tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5">Memuat data pengurus...</td></tr>';

    fetch('api/cms_get_pengurus.php')
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success' && res.data.length > 0) {
                let html = '';
                res.data.forEach(p => {
                    const fotoHtml = p.foto ? `<img src="${smartAssetUrl(p.foto)}" style="width:32px;height:32px;border-radius:50%;object-fit:cover;">` : `<div style="width:32px;height:32px;border-radius:50%;background:var(--accent-color);color:white;display:flex;align-items:center;justify-content:center;font-weight:bold;font-size:0.8rem;">${p.nama.charAt(0)}</div>`;
                    html += `<tr>
                        <td class="font-bold text-center"><span class="badge bg-emerald-light text-emerald" style="padding:4px 8px;">Lv. ${p.urutan}</span></td>
                        <td class="text-center">${fotoHtml}</td>
                        <td class="font-bold">${p.nama}</td>
                        <td class="text-secondary">${p.jabatan}</td>
                        <td class="text-right">
                            <button class="button-secondary button-sm" onclick="editPengurus(${p.id}, '${encodeURIComponent(p.nama)}', '${encodeURIComponent(p.jabatan)}', ${p.urutan}, '${p.foto || ''}')"><i data-lucide="edit" style="width:14px; height:14px;"></i></button>
                            <button class="button-secondary button-sm" style="color: #ef4444;" onclick="deletePengurus(${p.id})"><i data-lucide="trash-2" style="width:14px; height:14px;"></i></button>
                        </td>
                    </tr>`;
                });
                tbody.innerHTML = html;
                if (typeof lucide !== 'undefined') lucide.createIcons();
            } else { tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-secondary">Belum ada pengurus yang ditambahkan.</td></tr>'; }
        }).catch(() => { tbody.innerHTML = '<tr><td colspan="5" class="text-center py-5 text-secondary">Siap digunakan. Silakan tambahkan anggota pertama.</td></tr>'; });
}

window.addPengurus = function () {
    document.getElementById('cms-pengurus-id').value = '0'; document.getElementById('cms-pengurus-nama').value = '';
    document.getElementById('cms-pengurus-jabatan').value = ''; document.getElementById('cms-pengurus-urutan').value = '1';
    document.getElementById('cms-pengurus-foto').value = ''; document.getElementById('preview_pengurus_foto').innerHTML = '';
    document.getElementById('modal-pengurus-title').innerText = 'Tambah Pengurus'; document.getElementById('modal-cms-pengurus').classList.remove('hidden');
}

window.editPengurus = function (id, namaEncoded, jabatanEncoded, urutan, foto) {
    document.getElementById('cms-pengurus-id').value = id; document.getElementById('cms-pengurus-nama').value = decodeURIComponent(namaEncoded);
    document.getElementById('cms-pengurus-jabatan').value = decodeURIComponent(jabatanEncoded); document.getElementById('cms-pengurus-urutan').value = urutan;
    document.getElementById('cms-pengurus-foto').value = '';
    document.getElementById('preview_pengurus_foto').innerHTML = foto ? `<a href="${smartAssetUrl(foto)}" target="_blank" class="badge bg-blue-light text-blue" style="font-size:0.6rem;">Lihat Foto</a>` : '';
    document.getElementById('modal-pengurus-title').innerText = 'Edit Pengurus'; document.getElementById('modal-cms-pengurus').classList.remove('hidden');
}

window.saveCmsPengurus = function () {
    const fd = new FormData(); fd.append('id', document.getElementById('cms-pengurus-id').value); fd.append('nama', document.getElementById('cms-pengurus-nama').value);
    fd.append('jabatan', document.getElementById('cms-pengurus-jabatan').value); fd.append('urutan', document.getElementById('cms-pengurus-urutan').value);
    const file = document.getElementById('cms-pengurus-foto').files[0]; if (file) fd.append('foto', file);
    showLoading('Menyimpan...'); fetch('api/cms_save_pengurus.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => { if (res.status === 'success') { showToast(res.message); closeInfoModal('modal-cms-pengurus'); loadCmsPengurus(); } else showToast(res.message, 'error'); });
}

window.deletePengurus = function (id) {
    if (confirm('Hapus anggota pengurus ini?')) {
        const fd = new FormData(); fd.append('id', id); fetch('api/cms_delete_pengurus.php', { method: 'POST', body: fd }).then(r => r.json()).then(res => { if (res.status === 'success') loadCmsPengurus(); });
    }
}