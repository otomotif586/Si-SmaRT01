<!-- Page: Manajemen Akses User Si-SmaRT -->
<div id="page-users" class="page-content hidden page-section stagger-ready">
    <!-- Modern Header for Page -->
    <div class="page-header-premium mb-8 stagger-item stagger-delay-1">
        <h2 class="text-3xl font-bold text-slate-900 font-space">Master User</h2>
        <p class="text-slate-500 font-medium">Manajemen Akses & Kredensial Sistem Si-SmaRT</p>
    </div>

    <div class="glass-card card-section stagger-item stagger-delay-2">
        <div class="section-header">
            <div>
                <h4 class="section-title text-2xl font-bold text-slate-800">Manajemen Akun Sistem</h4>
                <p class="text-slate-500 text-sm">Kelola administrator atau petugas yang berhak login ke dalam sistem.</p>
            </div>
            <button class="button-primary px-8" onclick="openUserModal()">
                <i data-lucide="user-plus" class="mr-2"></i> Tambah Pengguna
            </button>
        </div>

        <div class="table-responsive mt-8">
            <table class="modern-table w-full">
                <thead class="bg-slate-50">
                    <tr>
                        <th class="py-4 px-6 text-left">Nama Lengkap</th>
                        <th class="py-4 px-6 text-left">Username</th>
                        <th class="py-4 px-6 text-left">Role / Hak Akses</th>
                        <th class="py-4 px-6 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody id="cms-users-body">
                    <!-- Diisi via AJAX -->
                    <tr>
                        <td colspan="4" class="text-center py-12">
                            <div class="flex flex-col items-center gap-2">
                                <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                                <span class="text-slate-400 font-medium">Memuat data user...</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- MODAL MANAJEMEN USER -->
    <div id="modal-cms-user" class="fixed inset-0 w-full h-full hidden items-center justify-center p-4 sm:p-6 overlay-z10025">
        <div class="absolute inset-0 bg-slate-800/30 backdrop-blur-sm transition-opacity" onclick="closeUserModal()"></div>
        <div class="glass-card relative w-full max-w-md flex flex-col overflow-hidden shadow-2xl users-modal-shell">
            <div class="users-modal-head">
                <div>
                    <h2 id="modal-user-title" class="text-2xl font-black text-slate-800">Tambah Pengguna</h2>
                    <p class="text-slate-500 font-medium text-sm mt-1">Buat kredensial login baru untuk akses sistem.</p>
                </div>
                <button class="absolute top-8 right-8 w-10 h-10 rounded-[1rem] bg-slate-50 text-slate-400 flex items-center justify-center hover:bg-red-50 hover:text-red-500 transition-all" onclick="closeUserModal()"><i data-lucide="x" class="w-5 h-5"></i></button>
            </div>
            
            <div class="hide-scrollbar users-modal-body">
                <input type="hidden" id="cms-user-id" value="0">
                <div class="space-y-6">
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Nama Lengkap</label>
                        <input type="text" id="cms-user-nama" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="Nama Karyawan/Pengurus">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Username Login</label>
                        <div class="relative">
                            <span class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 font-bold">@</span>
                            <input type="text" id="cms-user-username" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 pl-12 pr-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="username">
                        </div>
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Password <span class="text-red-400 font-normal lowercase ml-1 text-[0.65rem]">(kosongkan jika tidak diubah)</span></label>
                        <input type="password" id="cms-user-password" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner" placeholder="••••••••">
                    </div>
                    <div>
                        <label class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-2 ml-1">Role Akses</label>
                        <select id="cms-user-role" class="w-full bg-slate-50 border border-slate-100 text-slate-800 rounded-[1.5rem] py-4 px-5 font-semibold focus:outline-none focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all text-sm shadow-inner appearance-none cursor-pointer">
                            <option value="Admin">Super Admin (Akses Penuh)</option>
                            <option value="Bendahara">Bendahara (Keuangan)</option>
                            <option value="Keamanan">Petugas Keamanan</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="users-modal-foot">
                <button type="button" class="px-6 py-4 rounded-[1.5rem] font-bold text-slate-500 bg-slate-50 hover:bg-slate-200 transition-all" onclick="closeUserModal()">Batal</button>
                <button type="button" class="flex-grow px-6 py-4 rounded-[1.5rem] font-bold text-white bg-emerald-600 hover:bg-emerald-700 shadow-xl shadow-emerald-200 transition-all flex items-center justify-center gap-2" onclick="saveCmsUser()"><i data-lucide="save" class="w-[18px] h-[18px]"></i> Simpan Pengguna</button>
            </div>
        </div>
    </div>
</div>

<style>
.users-modal-shell {
    border-radius: 2.5rem;
    max-height: 90vh;
    background: #fff;
    border: 1px solid rgba(255, 255, 255, 0.9);
}
.users-modal-head { padding: 32px 32px 24px; border-bottom: 1px solid #f1f5f9; }
.users-modal-body { padding: 24px 32px; overflow-y: auto; }
.users-modal-foot { padding: 24px 32px; border-top: 1px solid #f1f5f9; display: flex; gap: 12px; background: #fff; }
</style>

<script>
    // JS logic untuk Manajemen User
    function initUserPage() {
        if(typeof loadCmsUsers !== 'undefined') loadCmsUsers();
    }
    
    // Auto-load if this page is shown (Observer as fallback)
    document.addEventListener('DOMContentLoaded', () => {
        const observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                    const page = document.getElementById('page-users');
                    if (page && !page.classList.contains('hidden')) {
                        loadCmsUsers();
                    }
                }
            });
        });
        const target = document.getElementById('page-users');
        if(target) observer.observe(target, { attributes: true });
    });

    function loadCmsUsers() {
        const tbody = document.getElementById('cms-users-body');
        if(!tbody) return;
        
        // Show loading state first
        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-12">
            <div class="flex flex-col items-center gap-2">
                <div class="w-8 h-8 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
                <span class="text-slate-400 font-medium">Memuat data user...</span>
            </div>
        </td></tr>`;

        fetch('views/pages/get_users.php')
            .then(r => r.json())
            .then(res => {
                if(res.status === 'success') {
                    let html = '';
                    res.data.forEach(u => {
                        const badgeColor = u.role === 'Admin' ? 'bg-red-100 text-red-700' : 
                                         (u.role === 'Bendahara' ? 'bg-emerald-100 text-emerald-700' : 
                                         'bg-blue-100 text-blue-700');
                        
                        html += `
                            <tr class="hover:bg-slate-50 transition-colors border-b border-slate-100 last:border-0">
                                <td class="py-5 px-6 font-bold text-slate-800">${u.nama_lengkap}</td>
                                <td class="py-5 px-6"><span class="text-slate-300 font-medium">@</span>${u.username}</td>
                                <td class="py-5 px-6"><span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider ${badgeColor}">${u.role}</span></td>
                                <td class="py-5 px-6 text-right whitespace-nowrap">
                                    <button onclick='editCmsUser(${JSON.stringify(u).replace(/'/g, "&#39;")})' class="p-3 text-blue-600 hover:bg-blue-50 rounded-2xl transition-all mr-2" title="Edit User"><i data-lucide="edit-3" class="w-5 h-5"></i></button>
                                    <button onclick="deleteCmsUser(${u.id})" class="p-3 text-red-600 hover:bg-red-50 rounded-2xl transition-all" title="Hapus User"><i data-lucide="trash-2" class="w-5 h-5"></i></button>
                                </td>
                            </tr>`;
                    }); 
                    tbody.innerHTML = html || '<tr><td colspan="4" class="text-center py-12 text-slate-400 font-medium">Belum ada user sistem terdaftar</td></tr>'; 
                    if(typeof lucide !== 'undefined') lucide.createIcons();
                }
            })
            .catch(err => {
                tbody.innerHTML = '<tr><td colspan="4" class="text-center py-12 text-red-500 font-medium">Gagal memuat data user</td></tr>';
            });
    }

    function closeUserModal() {
        const modal = document.getElementById('modal-cms-user');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function openUserModal() { 
        document.getElementById('cms-user-id').value = 0; 
        document.getElementById('cms-user-nama').value = ''; 
        document.getElementById('cms-user-username').value = ''; 
        document.getElementById('cms-user-password').value = ''; 
        document.getElementById('modal-user-title').innerText = 'Tambah Pengguna';
        const modal = document.getElementById('modal-cms-user');
        document.body.appendChild(modal);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function editCmsUser(u) { 
        document.getElementById('cms-user-id').value = u.id; 
        document.getElementById('cms-user-nama').value = u.nama_lengkap; 
        document.getElementById('cms-user-username').value = u.username; 
        document.getElementById('cms-user-password').value = ''; 
        document.getElementById('cms-user-role').value = u.role; 
        document.getElementById('modal-user-title').innerText = 'Edit Pengguna';
        const modal = document.getElementById('modal-cms-user');
        document.body.appendChild(modal);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function saveCmsUser() { 
        const fd = new FormData(); 
        fd.append('id', document.getElementById('cms-user-id').value); 
        fd.append('nama_lengkap', document.getElementById('cms-user-nama').value); 
        fd.append('username', document.getElementById('cms-user-username').value); 
        fd.append('password', document.getElementById('cms-user-password').value); 
        fd.append('role', document.getElementById('cms-user-role').value); 
        
        if(typeof showLoading === 'function') showLoading('Menyimpan...'); 
        
        fetch('views/pages/save_user.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(res => { 
                if(res.status === 'success') {
                    if(typeof showToast === 'function') showToast('Kredensial Tersimpan'); 
                    closeUserModal(); 
                    loadCmsUsers();
                } else {
                    if(typeof showToast === 'function') showToast(res.message, 'error');
                } 
            }); 
    }

    function deleteCmsUser(id) { 
        if(typeof Swal === 'undefined') return;
        Swal.fire({
            title: 'Hapus Akses?',
            text: "User ini tidak akan bisa login lagi ke sistem.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                const fd = new FormData(); 
                fd.append('id', id); 
                fetch('views/pages/delete_user.php', { method: 'POST', body: fd })
                    .then(r => r.json())
                    .then(res => { 
                        if(res.status === 'success') {
                            if(typeof showToast === 'function') showToast('User dihapus'); 
                            loadCmsUsers();
                        } 
                    });
            }
        });
    }
</script>
