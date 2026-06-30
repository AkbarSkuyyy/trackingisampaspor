<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Permohonan Paspor - Kantor Imigrasi Sampit</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <style> body, .imig-popup-content, .imig-widget-title { font-family: 'Outfit', sans-serif !important; } </style>
</head>
<body>

    <header id="header" class="fixed-top d-print-none">
        <div class="topbar-resmi">
            <div class="nav-container-fluid">
                <div class="sub-header-row">
                    <p id="text_sub_header">Situs Web Resmi Imigrasi Republik Indonesia</p>
                    <div>
                        <a id="accordion-trigger" href="javascript:void(0);" class="accordion-trigger">
                            <i class="fas fa-info-circle text_link"></i>
                        </a>
                    </div>
                </div>
                
                <div id="panel-info" class="panel-keamanan">
                    <div class="panel-row">
                        <div class="info-wrapper">
                            <div class="info-icon"><i class="fas fa-circle"></i></div>
                            <div class="info-content">
                                <div class="info-title">Direktorat Jenderal Imigrasi merupakan Unit Eselon I di bawah <a href="#" target="_blank">Kementerian Imigrasi dan Pemasyarakatan</a></div>
                                <p class="info-desc">Secara umum, situs web resmi kementerian/lembaga Pemerintah RI berakhiran .go.id</p>
                            </div>
                        </div>
                        <div class="info-wrapper">
                            <div class="info-icon"><i class="fas fa-circle"></i></div>
                            <div class="info-content">
                                <div class="info-title">Situs web yang aman menggunakan HTTPS menampilkan icon (<i class="fas fa-lock"></i>)</div>
                                <p class="info-desc">Alamat situs web berawalan https:// merupakan salah satu bentuk pengamanan aliran komunikasi data.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-biru-tua">
            <div class="nav-container-fluid navbar-main-row">
                <div class="left-side-toggle">
                    <button id="navToggle" class="btn-toggle-burger"><i class="fas fa-bars"></i><span class="menu-text-desktop">Menu</span></button>
                </div>
                <div class="logo-center-block" onclick="window.location.href='/'">
                    <a href="/">
                        <strong>
                            Kantor Imigrasi Kelas II TPI Sampit<br>
                            Kantor Wilayah Ditjenim Kalimantan Tengah
                        </strong>
                    </a>
                </div>
                <div class="header-side-right"></div>
            </div>
        </div>
    </header>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>
    <aside id="sidebarMenuFront" class="sidebar d-print-none imigrasi-left-navbar">
        <div class="sidebar-header-custom" id="closeSidebarBtn">
            <span>Menu Navigasi</span><i class="fas fa-times" style="cursor: pointer;"></i>
        </div>
        <ul class="sidebar-nav" id="sidebar-nav" style="padding: 10px 0; margin: 0; list-style: none;">
            <li class="nav-item"><a class="nav-link collapsed text-decoration-none" href="/home"><span class="ps-3">Beranda</span></a></li>
            <li class="nav-item has-submenu">
                <a class="nav-link collapsed" href="#"><span class="ps-3">Layanan Warga Negara Indonesia</span><i class="bi bi-chevron-right ms-auto"></i></a>
                <div class="submenu dropdown-box">
                    <ul class="nav-content">
                        <li class="submenu-item"><a href="/layanan-wni/paspor-republik-indonesia"><span class="ps-4">Paspor Republik Indonesia</span></a></li>
                        <li class="submenu-item"><a href="/layanan-wni/kartu-perjalanan-pebisnis-apec"><span class="ps-4">Kartu Perjalanan Bisnis APEC</span></a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item has-submenu">
                <a class="nav-link collapsed" href="#"><span class="ps-3">Layanan Warga Negara Asing</span><i class="bi bi-chevron-right ms-auto"></i></a>
                <div class="submenu dropdown-box">
                    <ul class="nav-content">
                        <li class="submenu-item"><a href="/wna/daftar-visa-indonesia"><span class="ps-4">Daftar Visa Indonesia</span></a></li>
                        <li class="submenu-item"><a href="/wna/daftar-negara-voa-bvk-calling-visa"><span class="ps-4">Daftar Subjek VoA, BVK &amp; Calling Visa</span></a></li>
                        <li class="submenu-item"><a href="/wna/izin-tinggal-keimigrasian"><span class="ps-4">Izin Tinggal Keimigrasian</span></a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item"><a class="text-decoration-none nav-link collapsed" href="/component/sppagebuilder/page/1"><span class="ps-3">Beranda Utama</span></a></li>
            <li class="nav-item has-submenu">
                <a class="nav-link collapsed" href="#"><span class="ps-3">Layanan Publik</span><i class="bi bi-chevron-right ms-auto"></i></a>
                <div class="submenu dropdown-box">
                    <ul class="nav-content">
                        <li class="submenu-item"><a href="/component/sppagebuilder/page/473"><span class="ps-4">Warga Negara Indonesia (WNI)</span></a></li>
                        <li class="submenu-item"><a href="/component/sppagebuilder/page/474"><span class="ps-4">Warga Negara Asing (WNA)</span></a></li>
                        <li class="submenu-item"><a href="/component/sppagebuilder/page/493"><span class="ps-4">Biaya Keimigrasian</span></a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item has-submenu">
                <a class="nav-link collapsed" href="#"><span class="ps-3">Informasi Publik</span><i class="bi bi-chevron-right ms-auto"></i></a>
                <div class="submenu dropdown-box">
                    <ul class="nav-content">
                        <li class="submenu-item"><a href="/informasi-publik/daftar-isian-pelaksanaan-anggaran-satuan-kerja-dipa-satker"><span class="ps-4">Daftar Isian Pelaksanaan Anggaran (DIPA)</span></a></li>
                        <li class="submenu-item"><a href="/informasi-publik/laporan-akuntabilitas-kinerja-instansi-pemerintah"><span class="ps-4">Laporan Akuntabilitas Kinerja</span></a></li>
                        <li class="submenu-item"><a href="/informasi-publik/standar-pelayanan-kantor-imigrasi-sampit-dan-maklumat-pelayanan"><span class="ps-4">Standar Pelayanan & Maklumat</span></a></li>
                        <li class="submenu-item"><a href="/informasi-publik/survey-ipk-ikm"><span class="ps-4">Survey IPK-IKM</span></a></li>
                        <li class="submenu-item"><a href="http://sampit.imigrasi.go.id/berita-utama/"><span class="ps-4">Berita</span></a></li>
                        <li class="submenu-item"><a href="/component/sppagebuilder/page/495"><span class="ps-4">Majalah</span></a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item has-submenu">
                <a class="nav-link collapsed" href="#"><span class="ps-3">Zona Integritas</span><i class="bi bi-chevron-right ms-auto"></i></a>
                <div class="submenu dropdown-box">
                    <ul class="nav-content">
                        <li class="submenu-item"><a href="/zona-integritas/sk-tim-pengelola-pengaduan"><span class="ps-4">SK Tim Pengelola Pengaduan</span></a></li>
                        <li class="submenu-item"><a href="https://upg.kemenkumham.go.id"><span class="ps-4">Tindak Gratifikasi</span></a></li>
                        <li class="submenu-item"><a href="https://sp4n.lapor.go.id/"><span class="ps-4">Aplikasi LAPOR!</span></a></li>
                        <li class="submenu-item"><a href="https://wbs.kemenimipas.go.id/aduan/create"><span class="ps-4">Whistle Blowing System</span></a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item has-submenu">
                <a class="nav-link collapsed" href="#"><span class="ps-3">Tentang Kami</span><i class="bi bi-chevron-right ms-auto"></i></a>
                <div class="submenu dropdown-box">
                    <ul class="nav-content">
                        <li class="submenu-item"><a href="/component/sppagebuilder/page/494"><span class="ps-4">Profil Kantor</span></a></li>
                        <li class="submenu-item"><a href="/component/sppagebuilder/page/498"><span class="ps-4">Hubungi Kami</span></a></li>
                        <li class="submenu-item"><a href="/component/sppagebuilder/page/499"><span class="ps-4">FAQ</span></a></li>
                    </ul>
                </div>
            </li>
            <li class="nav-item"><a class="text-decoration-none nav-link collapsed" href="/cari"><span class="ps-3">Cari</span></a></li>
            <li class="nav-item"><a class="text-decoration-none nav-link collapsed" href="index.php" style="color: #f9c74f;"><span class="ps-3">E-Tracking Passport</span></a></li>
        </ul>
    </aside>

    <div id="widget-status-wrapper">
        <div class="imig-widget-card-container">
            <div class="imig-widget-card">
                <div class="imig-widget-header">
                    <h2 class="imig-widget-title">Cek Status Permohonan</h2>
                    <p class="imig-widget-desc">Silakan masukkan nomor permohonan Paspor RI Anda untuk melacak progres berkas</p>
                </div>

                <form action="#" id="formCekImigrasi">
                    <div class="imig-form-group">
                        <label for="no_permohonan">Nomor Permohonan</label>
                        <input type="text" id="no_permohonan" placeholder="Contoh: 106xxxxxxxxx atau 310xxxxxxxxx" required autocomplete="off" />
                    </div>

                    <div class="imig-form-group">
                        <label>Verifikasi Keamanan</label>
                        <div class="imig-captcha-row">
                            <div class="imig-captcha-display" id="layar_captcha"></div>
                            <button type="button" class="imig-btn-refresh" id="tombol_refresh" title="Muat Ulang Kode"><i class="fas fa-sync-alt"></i></button>
                            <input type="text" id="input_kode_captcha" placeholder="Ketik kode keamanan" required autocomplete="off" />
                        </div>
                    </div>

                    <button type="submit" class="imig-btn-submit" id="tombol_cek" disabled>Cek Status Sekarang</button>
                    
                    <div class="imig-notes">
                        <i class="fas fa-info-circle"></i>
                        <span>Gunakan 16 digit Nomor Permohonan. <strong>Pengguna M-Paspor</strong> dapat melihat nomor ini pada file PDF Bukti Pengantar (di bawah Barcode).</span>
                    </div>
                </form>

                <div class="mpaspor-promo-box">
                    <div class="mpaspor-icon"><i class="fas fa-mobile-screen-button"></i></div>
                    <div class="mpaspor-content">
                        <h4>Pengguna Layanan M-Paspor?</h4>
                        <p>Daftar antrean dan unggah berkas paspor menjadi lebih mudah dari rumah. Unduh aplikasi resmi M-Paspor sekarang:</p>
                        <div class="store-buttons">
                            <a href="https://play.google.com/store/apps/details?id=id.go.imigrasi.paspor_online" target="_blank" class="btn-store"><i class="fab fa-google-play"></i> Google Play</a>
                            <a href="https://apps.apple.com/id/app/m-paspor/id1576336459" target="_blank" class="btn-store"><i class="fab fa-apple"></i> App Store</a>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="imig-popup-overlay" id="modal_proses">
        <div class="imig-popup-box" style="border-top: 5px solid #0284c7;">
            <div class="imig-popup-icon"><i class="fas fa-spinner fa-spin" style="color: #0284c7;"></i></div>
            <div class="imig-popup-content">
                <h3 class="imig-popup-title" style="color: #0284c7;">Paspor Sedang Diproses</h3>
                
                <div class="imig-data-list">
                    <div class="imig-data-item">
                        <span class="imig-data-label">Nomor Permohonan</span>
                        <span class="imig-data-value" id="p_res_nomor"></span>
                    </div>
                    <div class="imig-data-item">
                        <span class="imig-data-label">Nama Pemohon</span>
                        <span class="imig-data-value" id="p_res_nama"></span>
                    </div>
                    <div class="imig-data-item" style="border-bottom:none; padding-bottom:0;">
                        <span class="imig-data-label">Status Saat Ini</span>
                        <span class="imig-data-value status-highlight" id="p_res_status" style="background: rgba(2, 132, 199, 0.1); color: #0284c7;"></span>
                    </div>
                </div>

                <div class="tracking-timeline-wrapper" style="margin-top: 20px; border-top: 1px dashed #cbd5e1; padding-top: 15px;">
                    <h4 style="font-size: 0.8rem; color: #64748b; margin: 0 0 15px 0; text-transform: uppercase; text-align: left; padding: 0 10px;">Progres Tahapan</h4>
                    <div class="tracking-timeline">
                        <div class="timeline-step" id="p_step_1"><div class="timeline-icon"><i class="fas fa-file-alt"></i></div><div class="timeline-text">Berkas Masuk</div></div>
                        <div class="timeline-step" id="p_step_2"><div class="timeline-icon"><i class="fas fa-camera"></i></div><div class="timeline-text">Wawancara</div></div>
                        <div class="timeline-step" id="p_step_3"><div class="timeline-icon"><i class="fas fa-wallet"></i></div><div class="timeline-text">Menunggu Bayar</div></div>
                        <div class="timeline-step" id="p_step_4"><div class="timeline-icon"><i class="fas fa-clipboard-check"></i></div><div class="timeline-text">Alokasi</div></div>
                        <div class="timeline-step" id="p_step_5"><div class="timeline-icon"><i class="fas fa-print"></i></div><div class="timeline-text">Proses Cetak</div></div>
                        <div class="timeline-step" id="p_step_6"><div class="timeline-icon"><i class="fas fa-check"></i></div><div class="timeline-text">Selesai</div></div>
                    </div>
                </div>
            </div>
            <button type="button" class="imig-popup-btn" style="background: #0284c7;" onclick="tutupSemuaModal()">Tutup & Cek Lagi</button>
        </div>
    </div>


    <div class="imig-popup-overlay" id="modal_selesai">
        <div class="imig-popup-box" style="border-top: 5px solid #10b981;">
            <div class="imig-popup-icon"><i class="fas fa-check-circle" style="color: #10b981;"></i></div>
            <div class="imig-popup-content">
                <h3 class="imig-popup-title" style="color: #10b981;">Siap Diambil!</h3>
                
                <div class="imig-data-list">
                    <div class="imig-data-item">
                        <span class="imig-data-label">Nomor Permohonan</span>
                        <span class="imig-data-value" id="s_res_nomor"></span>
                    </div>
                    <div class="imig-data-item">
                        <span class="imig-data-label">Nama Pemohon</span>
                        <span class="imig-data-value" id="s_res_nama"></span>
                    </div>
                    
                    <div class="imig-data-item">
                        <span class="imig-data-label">Tanggal Lahir</span>
                        <span class="imig-data-value" id="s_res_tgl"></span>
                    </div>
                    
                    <div class="imig-data-item" id="baris_batas_waktu" style="display:none;">
                        <span class="imig-data-label">Batas Pengambilan</span>
                        <span class="imig-data-value" id="s_res_batas"></span>
                    </div>

                    <div class="imig-data-item" style="border-bottom:none; padding-bottom:0;">
                        <span class="imig-data-label">Status Saat Ini</span>
                        <span class="imig-data-value status-highlight" id="s_res_status" style="background: rgba(16, 185, 129, 0.1); color: #10b981;"></span>
                    </div>
                </div>

                <div class="tracking-timeline-wrapper" style="margin-top: 20px; border-top: 1px dashed #cbd5e1; padding-top: 15px;">
                    <div class="tracking-timeline">
                        <div class="timeline-step completed"><div class="timeline-icon"><i class="fas fa-file-alt"></i></div></div>
                        <div class="timeline-step completed"><div class="timeline-icon"><i class="fas fa-camera"></i></div></div>
                        <div class="timeline-step completed"><div class="timeline-icon"><i class="fas fa-wallet"></i></div></div>
                        <div class="timeline-step completed"><div class="timeline-icon"><i class="fas fa-clipboard-check"></i></div></div>
                        <div class="timeline-step completed"><div class="timeline-icon"><i class="fas fa-print"></i></div></div>
                        <div class="timeline-step completed active"><div class="timeline-icon"><i class="fas fa-check"></i></div><div class="timeline-text">Bisa<br>Diambil</div></div>
                    </div>
                </div>
            </div>
            <button type="button" class="imig-popup-btn" style="background: #10b981;" onclick="tutupSemuaModal()">Tutup & Cek Lagi</button>
        </div>
    </div>


    <div class="imig-popup-overlay" id="modal_gagal">
        <div class="imig-popup-box" style="border-top: 5px solid #ef4444;">
            <div class="imig-popup-icon"><i class="fas fa-exclamation-circle" style="color: #ef4444;"></i></div>
            <div class="imig-popup-content">
                <h3 class="imig-popup-title" id="err_title" style="color:#ef4444;">Tidak Ditemukan</h3>
                <p class="imig-popup-message" id="err_message" style="margin: 15px 0; color: #475569; font-size: 0.95rem; line-height:1.5;"></p>
                
                <div style="background: #f8fafc; border: 1px solid #e2e8f0; padding: 12px 15px; border-radius: 8px; font-size: 0.8rem; color: #64748b; text-align: left; margin-top:10px;">
                    <strong style="color:#0f172a;"><i class="fas fa-lightbulb text-warning"></i> Tips Pengecekan:</strong><br>
                    1. Pastikan nomor berjumlah <strong>16 digit angka</strong>.<br>
                    2. Periksa kembali potensi salah ketik.<br>
                    3. Berkas baru pasca foto butuh sinkronisasi sistem pusat 1x24 jam.
                </div>
            </div>
            <button type="button" class="imig-popup-btn" style="background: #ef4444;" onclick="tutupSemuaModal()">Mengerti, Coba Lagi</button>
        </div>
    </div>


    <footer id="sp-footer">
        <div class="container">
            <div class="row">
                <div id="sp-footer1" class="col-lg-12">
                    <span class="sp-copyright">
                        <span style="color:#ffd000; font-weight: 600; line-height: 1.6; display:block;">
                            Laman Resmi Kantor Imigrasi Kelas II TPI Sampit<br>
                            Kantor Wilayah Direktorat Jenderal Imigrasi<br>
                            Provinsi Kalimantan Tengah<br>
                            <span style="color:white; font-weight: 400; font-size: 0.9em; display:block; margin-top:5px;">Copyright &copy; 2026 Direktorat Jenderal Imigrasi</span>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </footer>

    <script src="assets/js/app.js"></script>
    
</body>
</html>