<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
require_once 'koneksi.php';

// AUTO-PATCH: Buat tabel log_pencarian jika belum ada
mysqli_query($conn, "CREATE TABLE IF NOT EXISTS log_pencarian (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tanggal DATE,
    jam TIME,
    no_permohonan VARCHAR(100),
    ip_address VARCHAR(50)
)");

// 1. Ambil Total Keseluruhan
$q_total = mysqli_query($conn, "SELECT COUNT(*) as total FROM permohonan");
$total_data = mysqli_fetch_assoc($q_total)['total'];

// 2. Ambil Data Status untuk Card & Grafik
$q_status = mysqli_query($conn, "SELECT status_saat_ini, COUNT(*) as jumlah FROM permohonan GROUP BY status_saat_ini");

$label_status = [];
$data_status = [];

// Siapkan variabel penampung untuk setiap kartu (default 0)
$c_masuk = 0;
$c_wawancara = 0;
$c_alokasi = 0;
$c_cetak = 0;
$c_selesai = 0;

while($row = mysqli_fetch_assoc($q_status)) {
    $status = $row['status_saat_ini'];
    $jumlah = $row['jumlah'];
    
    // Untuk Grafik
    $label_status[] = $status;
    $data_status[] = $jumlah;
    
    // Untuk Kartu Tahapan
    if(strpos($status, 'Masuk') !== false) $c_masuk = $jumlah;
    if(strpos($status, 'Wawancara') !== false) $c_wawancara = $jumlah;
    if(strpos($status, 'Alokasi') !== false) $c_alokasi = $jumlah;
    if(strpos($status, 'Dicetak') !== false) $c_cetak = $jumlah;
    if(strpos($status, 'Diambil') !== false) $c_selesai = $jumlah;
}

// 3. Ambil Data Jenis Permohonan untuk Bar Chart
$q_jenis = mysqli_query($conn, "SELECT jenis_permohonan, COUNT(*) as jumlah FROM permohonan WHERE jenis_permohonan IS NOT NULL AND jenis_permohonan != '' GROUP BY jenis_permohonan ORDER BY jumlah DESC");
$label_jenis = [];
$data_jenis = [];
while($row = mysqli_fetch_assoc($q_jenis)) {
    $label_jenis[] = $row['jenis_permohonan'];
    $data_jenis[] = $row['jumlah'];
}

// 4. Statistik Pencarian / Akses Warga
$tgl_hari_ini = date('Y-m-d');
$q_akses_hari_ini = mysqli_query($conn, "SELECT COUNT(*) as hit FROM log_pencarian WHERE tanggal = '$tgl_hari_ini'");
$akses_hari_ini = $q_akses_hari_ini ? mysqli_fetch_assoc($q_akses_hari_ini)['hit'] : 0;

$q_akses_total = mysqli_query($conn, "SELECT COUNT(*) as hit FROM log_pencarian");
$akses_total = $q_akses_total ? mysqli_fetch_assoc($q_akses_total)['hit'] : 0;

// 5. Ambil Data Tren Permohonan Harian (Line Chart)
$q_tren = mysqli_query($conn, "SELECT tgl_permohonan, COUNT(*) as jumlah FROM permohonan WHERE tgl_permohonan IS NOT NULL AND tgl_permohonan != '' GROUP BY tgl_permohonan ORDER BY tgl_permohonan ASC LIMIT 15");
$label_tren = [];
$data_tren = [];
if($q_tren) {
    while($row = mysqli_fetch_assoc($q_tren)) {
        // Format tanggal agar lebih cantik (misal: 15 Jun)
        $tgl_format = date('d M', strtotime($row['tgl_permohonan']));
        $label_tren[] = $tgl_format;
        $data_tren[] = $row['jumlah'];
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Statistik - Admin Imigrasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    
    <!-- Memanggil Library Chart.js untuk Grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js">
        // Render Grafik Garis (Tren Permohonan)
        const ctxTren = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTren, {
            type: 'line',
            data: {
                labels: labelTren,
                datasets: [{
                    label: 'Jumlah Pemohon',
                    data: dataTren,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4, // Membuat garisnya melengkung halus (spline)
                    fill: true,
                    pointBackgroundColor: '#002D62',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } 
                },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { family: 'Outfit' } } },
                    x: { ticks: { font: { family: 'Outfit' } } }
                }
            }
        });
    </script>

    <style>
        body { font-family: 'Outfit', sans-serif; }
        
        /* GRID KARTU TAHAPAN */
        .dashboard-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        
        .card-stat {
            padding: 20px;
            border-radius: 12px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s;
        }
        .card-stat:hover { transform: translateY(-5px); }
        
        .card-stat h4 { margin: 0; font-size: 0.9rem; font-weight: 500; opacity: 0.9; position: relative; z-index: 2; }
        .card-stat h2 { margin: 10px 0 0; font-size: 2.2rem; font-weight: 800; position: relative; z-index: 2; }
        
        /* Ikon Background transparan */
        .card-stat i.bg-icon { 
            position: absolute; 
            right: -10px; 
            bottom: -20px; 
            font-size: 6rem; 
            opacity: 0.2; 
            z-index: 1; 
            transform: rotate(-10deg);
        }

        /* Warna Gradien Tiap Tahapan */
        .bg-total     { background: linear-gradient(135deg, #0f172a, #1e293b); } /* Hitam/Navy Gelap */
        .bg-masuk     { background: linear-gradient(135deg, #64748b, #94a3b8); } /* Abu-abu */
        .bg-wawancara { background: linear-gradient(135deg, #3b82f6, #60a5fa); } /* Biru Terang */
        .bg-alokasi   { background: linear-gradient(135deg, #8b5cf6, #a78bfa); } /* Ungu */
        .bg-cetak     { background: linear-gradient(135deg, #f59e0b, #fbbf24); } /* Oranye */
        .bg-selesai   { background: linear-gradient(135deg, #10b981, #34d399); } /* Hijau */

        
        /* LAYOUT GRAFIK KANAN-KIRI */
        .chart-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr; /* Kiri lebih lebar sedikit dari Kanan */
            gap: 20px;
            margin-bottom: 30px;
        }
        .chart-grid .full-width {
            grid-column: 1 / -1; /* Membuat chart bawah membentang penuh */
        }
        @media (max-width: 992px) {
            .chart-grid { grid-template-columns: 1fr; } /* Jadi atas-bawah di HP */
        }

        .chart-box {
            background: white; 
            padding: 20px; border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            border-top: 5px solid #002D62;
        }
        .chart-title { font-size: 1.05rem; font-weight: 700; color: #002D62; margin-bottom: 15px; text-align: center; }
</style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h2>Dashboard Statistik Permohonan</h2>
        </div>

        <!-- 1. KOTAK INFORMASI TAHAPAN (CARDS) -->
        <div class="dashboard-cards">
            <div class="card-stat bg-total">
                <h4>Total Permohonan</h4>
                <h2><?= number_format($total_data, 0, ',', '.') ?></h2>
                <i class="fas fa-users bg-icon"></i>
            </div>
            
            <div class="card-stat" style="background: linear-gradient(135deg, #e11d48, #f43f5e);">
                <h4>Akses E-Tracking (Hari Ini)</h4>
                <h2><?= number_format($akses_hari_ini, 0, ',', '.') ?> <span style="font-size:0.9rem; font-weight:normal; opacity:0.8;">kali</span></h2>
                <i class="fas fa-search bg-icon"></i>
            </div>

            <div class="card-stat" style="background: linear-gradient(135deg, #0284c7, #38bdf8);">
                <h4>Total Akses E-Tracking</h4>
                <h2><?= number_format($akses_total, 0, ',', '.') ?> <span style="font-size:0.9rem; font-weight:normal; opacity:0.8;">kali</span></h2>
                <i class="fas fa-globe bg-icon"></i>
            </div>
            
            <div class="card-stat bg-masuk">
                <h4>Permohonan Masuk</h4>
                <h2><?= number_format($c_masuk, 0, ',', '.') ?></h2>
                <i class="fas fa-file-import bg-icon"></i>
            </div>
            
            <div class="card-stat bg-wawancara">
                <h4>Selesai Wawancara</h4>
                <h2><?= number_format($c_wawancara, 0, ',', '.') ?></h2>
                <i class="fas fa-camera bg-icon"></i>
            </div>
            
            <div class="card-stat bg-alokasi">
                <h4>Tahap Alokasi</h4>
                <h2><?= number_format($c_alokasi, 0, ',', '.') ?></h2>
                <i class="fas fa-clipboard-check bg-icon"></i>
            </div>
            
            <div class="card-stat bg-cetak">
                <h4>Proses Cetak</h4>
                <h2><?= number_format($c_cetak, 0, ',', '.') ?></h2>
                <i class="fas fa-print bg-icon"></i>
            </div>
            
            <div class="card-stat bg-selesai">
                <h4>Siap Diambil</h4>
                <h2><?= number_format($c_selesai, 0, ',', '.') ?></h2>
                <i class="fas fa-passport bg-icon"></i>
            </div>
        </div>

        <!-- 2. KOTAK GRAFIK (CHART.JS) -->
                <!-- 2. KOTAK GRAFIK (Grid Kanan-Kiri) -->
        <div class="chart-grid">
            
            <!-- KIRI: Tren Harian (Line Chart) -->
            <div class="chart-box">
                <div class="chart-title"><i class="fas fa-chart-line"></i> Tren Permohonan (15 Hari)</div>
                <div style="position: relative; height: 230px; width: 100%;">
                    <canvas id="trendChart"></canvas>
                </div>
            </div>

            <!-- KANAN: Status Paspor (Donut Chart) -->
            <div class="chart-box">
                <div class="chart-title"><i class="fas fa-chart-pie"></i> Progres Status Saat Ini</div>
                <div class="pie-container" style="position: relative; height: 230px; width: 100%; display: flex; justify-content: center;">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- BAWAH (Memanjang): Jenis Permohonan (Bar Chart) -->
            <div class="chart-box full-width">
                <div class="chart-title"><i class="fas fa-chart-bar"></i> Distribusi Jenis Permohonan</div>
                <div style="position: relative; height: 230px; width: 100%;">
                    <canvas id="jenisChart"></canvas>
                </div>
            </div>

        </div>
    </main>

    <script>
        const labelStatus = <?= json_encode($label_status) ?>;
        const dataStatus = <?= json_encode($data_status) ?>;
        
        const labelTren = <?= json_encode($label_tren) ?>;
        const dataTren = <?= json_encode($data_tren) ?>;
        
        const labelJenis = <?= json_encode($label_jenis) ?>;
        const dataJenis = <?= json_encode($data_jenis) ?>;

        const colorPalette = ['#0f172a', '#10b981', '#f59e0b', '#8b5cf6', '#3b82f6', '#64748b', '#ef4444'];

        const ctxStatus = document.getElementById('statusChart').getContext('2d');
        new Chart(ctxStatus, {
            type: 'doughnut',
            data: {
                labels: labelStatus,
                datasets: [{
                    data: dataStatus,
                    backgroundColor: colorPalette,
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { family: 'Outfit', size: 11 } } }
                },
                cutout: '65%'
            }
        });

        const ctxJenis = document.getElementById('jenisChart').getContext('2d');
        new Chart(ctxJenis, {
            type: 'bar',
            data: {
                labels: labelJenis,
                datasets: [{
                    label: 'Jumlah Pemohon',
                    data: dataJenis,
                    backgroundColor: '#002D62', // Biru Imigrasi
                    borderColor: '#0f172a',
                    borderWidth: 1,
                    borderRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } 
                },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { family: 'Outfit' } } },
                    x: { ticks: { font: { family: 'Outfit' } } }
                }
            }
        });
    
        // Render Grafik Garis (Tren Permohonan)
        const ctxTren = document.getElementById('trendChart').getContext('2d');
        new Chart(ctxTren, {
            type: 'line',
            data: {
                labels: labelTren,
                datasets: [{
                    label: 'Jumlah Pemohon',
                    data: dataTren,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    tension: 0.4, // Membuat garisnya melengkung halus (spline)
                    fill: true,
                    pointBackgroundColor: '#002D62',
                    pointBorderColor: '#ffffff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false } 
                },
                scales: {
                    y: { beginAtZero: true, ticks: { font: { family: 'Outfit' } } },
                    x: { ticks: { font: { family: 'Outfit' } } }
                }
            }
        });
    </script>
</body>
</html>