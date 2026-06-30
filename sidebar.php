<?php 
// 1. TRIK PRESISI: Mengambil nama file TANPA akhiran .php (.php-nya kita potong paksa)
$current_page = basename($_SERVER['PHP_SELF'], '.php'); 

// 2. Mengambil nama petugas (mendukung berbagai variasi nama session yang pernah kita buat)
$nama_admin = $_SESSION['nama_petugas'] ?? $_SESSION['nama_admin'] ?? $_SESSION['admin_nama'] ?? 'Administrator';
?>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
    div:where(.swal2-container) { font-family: 'Outfit', sans-serif !important; }
    
    .nav-divider { height: 1px; background: rgba(255, 255, 255, 0.05); margin: 15px 20px; }
    
    /* Kapsul Profil di Bawah */
    .sidebar-user-capsule {
        padding: 12px 15px;
        margin: 0 15px 10px 15px;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.05);
        display: flex; align-items: center; gap: 12px;
        transition: all 0.2s ease;
        text-decoration: none;
    }
    .sidebar-user-capsule:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(249, 199, 79, 0.3);
    }
    .admin-avatar {
        width: 38px; height: 38px; background: #f9c74f; color: #002D62;
        border-radius: 50%; display: flex; align-items: center; justify-content: center;
        font-size: 1.1rem; flex-shrink: 0;
    }
    .admin-info strong { color: #fff; font-size: 0.9rem; font-weight: 600; display: block; }
    .admin-info small { color: #10b981; font-size: 0.75rem; font-weight: 600; }
</style>

<!-- Header Mobile -->
<div class="mobile-admin-header">
    <div class="mobile-brand">
        <img src="assets/images/Logo%20Ditjen%20Imigrasi.png" alt="Logo" onerror="this.onerror=null; this.src='assets/images/logo-imigrasi.png';">
        <span>Admin E-Tracking</span>
    </div>
    <button id="adminNavToggle"><i class="fas fa-bars"></i></button>
</div>

<div class="admin-sidebar-overlay" id="adminSidebarOverlay"></div>

<aside class="admin-sidebar" id="adminSidebar" style="display: flex; flex-direction: column; height: 100vh;">
    
    <!-- ATAP: Bersih hanya Logo -->
    <div class="sidebar-brand">
        <img src="assets/images/Logo%20Ditjen%20Imigrasi.png" alt="Logo Imigrasi" onerror="this.onerror=null; this.src='assets/images/logo-imigrasi.png';">
        <div class="brand-text-admin">
            <span style="font-size: 0.95rem; line-height: 1.2;">Kantor Imigrasi<br>TPI Sampit</span>
            <small>Admin Panel</small>
        </div>
        <button id="adminCloseBtn" class="admin-close-btn"><i class="fas fa-times"></i></button>
    </div>
    
    <!-- TENGAH: Menu Navigasi (Sudah Presisi Tanpa .php) -->
    <ul class="admin-nav" style="flex: 1;">
        <li>
            <a href="dashboard" class="<?= ($current_page == 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-chart-pie"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="input_data" class="<?= ($current_page == 'input_data') ? 'active' : ''; ?>">
                <i class="fas fa-edit"></i> Input Permohonan
            </a>
        </li>
        <li>
            <a href="laporan_pengunjung" class="<?= ($current_page == 'laporan_pengunjung') ? 'active' : ''; ?>">
                <i class="fas fa-user-clock"></i> Laporan Pengunjung
            </a>
        </li>
        <li>
            <a href="import_data" class="<?= ($current_page == 'import_data') ? 'active' : ''; ?>">
                <i class="fas fa-file-csv"></i> Import Excel/CSV
            </a>
        </li>
        <li>
            <a href="log_aktivitas" class="<?= ($current_page == 'log_aktivitas') ? 'active' : ''; ?>">
                <i class="fas fa-history"></i> Log Aktivitas
            </a>
        </li>
        
        <!-- MENU BARU: PROFIL PETUGAS -->
        <li>
            <a href="profile" class="<?= ($current_page == 'profile') ? 'active' : ''; ?>">
                <i class="fas fa-user-cog"></i> Profil Saya
            </a>
        </li>

        <div class="nav-divider"></div>
        <li>
            <a href="index" target="_blank">
                <i class="fas fa-external-link-alt"></i> Lihat Website
            </a>
        </li>
    </ul>
    
    <!-- BAWAH: Kapsul Profil (Klik untuk ke Profile) + Tombol Logout -->
    <div class="sidebar-bottom-area" style="padding-bottom: 15px;">
        <a href="profile" class="sidebar-user-capsule" title="Klik untuk edit profil">
            <div class="admin-avatar"><i class="fas fa-user-shield"></i></div>
            <div class="admin-info">
                <strong><?= htmlspecialchars($nama_admin) ?></strong>
                <small><i class="fas fa-circle" style="font-size: 0.45rem;"></i> Online</small>
            </div>
        </a>

        <div class="sidebar-footer" style="padding: 0 15px;">
            <a href="#" onclick="confirmLogout(event)" style="border-radius: 8px; justify-content: center; background: rgba(239, 68, 68, 0.1); color: #ef4444;">
                <i class="fas fa-sign-out-alt"></i> Logout Sistem
            </a>
        </div>
    </div>

</aside>

<script>
    const adminNavToggle = document.getElementById('adminNavToggle');
    const adminSidebar = document.getElementById('adminSidebar');
    const adminOverlay = document.getElementById('adminSidebarOverlay');
    const adminCloseBtn = document.getElementById('adminCloseBtn');

    function toggleAdminSidebar() {
        adminSidebar.classList.toggle('open');
        adminOverlay.classList.toggle('open');
    }

    if(adminNavToggle) adminNavToggle.addEventListener('click', toggleAdminSidebar);
    if(adminCloseBtn) adminCloseBtn.addEventListener('click', toggleAdminSidebar);
    if(adminOverlay) adminOverlay.addEventListener('click', toggleAdminSidebar);

    function confirmLogout(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Akhiri Sesi Admin?',
            text: "Anda akan keluar dari sistem dan kembali ke halaman Login.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#ef4444',
            cancelButtonColor: '#002D62',
            confirmButtonText: 'Ya, Keluar!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) window.location.href = 'logout';
        });
    }
</script>