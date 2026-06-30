<?php 
// Logika presisi pembaca tab aktif (Aman dari jebakan LiteSpeed)
$current_page = basename($_SERVER['PHP_SELF'], '.php'); 
$nama_super = $_SESSION['nama_petugas'] ?? 'Master Admin';
?>

<aside class="super-sidebar">
    <!-- ATAP: Logo Imigrasi -->
    <div class="super-brand">
        <img src="../assets/images/Logo Ditjen Imigrasi.png" alt="Logo Imigrasi" onerror="this.onerror=null; this.src='../assets/images/logo-imigrasi.png';">
        <div class="super-brand-text">
            <h2>MASTER PANEL</h2>
            <small>IT Control Room</small>
        </div>
    </div>
    
    <!-- TENGAH: Menu Navigasi Terkategori -->
    <ul class="super-nav">
        
        <li class="nav-group-label">OTORITAS UTAMA</li>
        <li>
            <a href="index" class="<?= ($current_page == 'index') ? 'active' : ''; ?>">
                <i class="fas fa-users-gear"></i> Kelola Hak Akses
            </a>
        </li>
        <li>
            <a href="master_data" class="<?= ($current_page == 'master_data') ? 'active' : ''; ?>">
                <i class="fas fa-folder-tree"></i> Master Data Paspor
            </a>
        </li>

        <li class="nav-group-label">AUDIT & KEAMANAN</li>
        <li>
            <a href="audit_trail" class="<?= ($current_page == 'audit_trail') ? 'active' : ''; ?>">
                <i class="fas fa-file-shield"></i> System Audit Log
            </a>
        </li>
        <li>
            <a href="session_monitor" class="<?= ($current_page == 'session_monitor') ? 'active' : ''; ?>">
                <i class="fas fa-user-clock"></i> Monitor Sesi Aktif
            </a>
        </li>

        <li class="nav-group-label">MAINTENANCE IT</li>
        <li>
            <a href="pengaturan_web" class="<?= ($current_page == 'pengaturan_web') ? 'active' : ''; ?>">
                <i class="fas fa-sliders"></i> Konfigurasi Web
            </a>
        </li>
        <li>
            <a href="backup_db" class="<?= ($current_page == 'backup_db') ? 'active' : ''; ?>">
                <i class="fas fa-database"></i> Backup Database
            </a>
        </li>

        <div class="super-nav-divider"></div>

        <li class="nav-group-label">PORTAL SWITCHER</li>
        <li>
            <a href="../dashboard" class="btn-switch-loket">
                <i class="fas fa-desktop"></i> Ke Mode Petugas
            </a>
        </li>
        <li>
            <a href="../index" target="_blank">
                <i class="fas fa-arrow-up-right-from-square"></i> Lihat Web Publik
            </a>
        </li>

    </ul>
    
    <!-- BAWAH: Kapsul Profil Root + Logout -->
    <div class="super-bottom">
        <div class="master-user-badge">
            <div class="master-avatar"><i class="fas fa-user-secret"></i></div>
            <div class="master-user-info">
                <strong><?= htmlspecialchars($nama_super); ?></strong>
                <span><i class="fas fa-circle" style="color:#10b981; font-size:0.45rem;"></i> Root Access</span>
            </div>
        </div>

        <a href="../logout" class="btn-logout-super" onclick="return confirm('Keluar dari sistem E-Tracking secara keseluruhan?')">
            <i class="fas fa-power-off"></i> Logout Sistem
        </a>
    </div>
</aside>