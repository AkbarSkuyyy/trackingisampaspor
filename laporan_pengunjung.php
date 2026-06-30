<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
require_once 'koneksi.php';

// Atur Default Filter (Dari awal bulan ini s.d Hari ini)
$tgl_mulai   = isset($_GET['mulai']) ? $_GET['mulai'] : date('Y-m-01');
$tgl_selesai = isset($_GET['selesai']) ? $_GET['selesai'] : date('Y-m-d');

$mulai_safe   = mysqli_real_escape_string($conn, $tgl_mulai);
$selesai_safe = mysqli_real_escape_string($conn, $tgl_selesai);

$where_clause = "WHERE tanggal BETWEEN '$mulai_safe' AND '$selesai_safe'";

// =========================================================================
// 1. MESIN EXPORT KE EXCEL / CSV (Sangat Ringan & Anti-Rusak Format)
// =========================================================================
if (isset($_GET['action']) && $_GET['action'] == 'export') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="Laporan_Akses_ETracking_'.$mulai_safe.'_sd_'.$selesai_safe.'.csv"');
    
    $output = fopen('php://output', 'w');
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF)); // BOM UTF-8 untuk Excel
    
    fputcsv($output, ['No', 'Tanggal', 'Jam Akses', 'Nomor Permohonan Dicari', 'Alamat IP Pengunjung']);
    
    $q_exp = mysqli_query($conn, "SELECT * FROM log_pencarian $where_clause ORDER BY tanggal DESC, jam DESC");
    $no = 1;
    while($row = mysqli_fetch_assoc($q_exp)) {
        // Trik: Tambahkan petik tunggal (') di awal nomor agar Excel membaca sebagai Teks, bukan Rumus Matematika
        $no_paspor_aman = "'" . $row['no_permohonan'];
        fputcsv($output, [$no++, $row['tanggal'], $row['jam'], $no_paspor_aman, $row['ip_address']]);
    }
    fclose($output);
    exit;
}
// =========================================================================

// 2. Ambil Statistik Untuk Kartu Atas
$q_hits = mysqli_query($conn, "SELECT COUNT(*) as jml FROM log_pencarian $where_clause");
$tot_hits = $q_hits ? mysqli_fetch_assoc($q_hits)['jml'] : 0;

$q_ip = mysqli_query($conn, "SELECT COUNT(DISTINCT ip_address) as jml FROM log_pencarian $where_clause");
$tot_ip = $q_ip ? mysqli_fetch_assoc($q_ip)['jml'] : 0;

$q_top = mysqli_query($conn, "SELECT no_permohonan, COUNT(*) as c FROM log_pencarian $where_clause GROUP BY no_permohonan ORDER BY c DESC LIMIT 1");
$top_paspor = "Belum Ada";
$top_hit = 0;
if($q_top && mysqli_num_rows($q_top) > 0) {
    $r_top = mysqli_fetch_assoc($q_top);
    $top_paspor = $r_top['no_permohonan'];
    $top_hit = $r_top['c'];
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Pengunjung - Admin Imigrasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .filter-card { background: #ffffff; padding: 20px; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); margin-bottom: 25px; border-left: 5px solid #002D62; }
        .filter-form { display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap; }
        .filter-group { display: flex; flex-direction: column; gap: 5px; }
        .filter-group label { font-size: 0.85rem; font-weight: 600; color: #002D62; }
        .filter-group input { padding: 10px 15px; border: 2px solid #cbd5e1; border-radius: 8px; font-family: 'Outfit'; }
        
        .btn-filter { background: #002D62; color: white; border: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px; }
        .btn-filter:hover { background: #001a3a; }
        .btn-export { background: #10b981; color: white; text-decoration: none; padding: 12px 20px; border-radius: 8px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .btn-export:hover { background: #059669; color: white; }

        .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 25px; }
        .stat-box { padding: 18px; border-radius: 10px; color: white; position: relative; overflow: hidden; }
        .stat-box h4 { margin: 0; font-size: 0.85rem; opacity: 0.9; }
        .stat-box h2 { margin: 8px 0 0; font-size: 1.8rem; font-weight: 800; }
        
        .badge-ip { background: #e2e8f0; color: #334155; padding: 4px 8px; border-radius: 4px; font-family: monospace; font-size: 0.85rem; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h2>Laporan & Audit Aktivitas Pengunjung</h2>
        </div>

        <div class="filter-card">
            <form method="GET" class="filter-form">
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Dari Tanggal</label>
                    <input type="date" name="mulai" value="<?= htmlspecialchars($tgl_mulai) ?>" required>
                </div>
                <div class="filter-group">
                    <label><i class="fas fa-calendar-alt"></i> Sampai Tanggal</label>
                    <input type="date" name="selesai" value="<?= htmlspecialchars($tgl_selesai) ?>" required>
                </div>
                
                <button type="submit" class="btn-filter"><i class="fas fa-filter"></i> Tampilkan</button>
                
                <a href="?action=export&mulai=<?= $mulai_safe ?>&selesai=<?= $selesai_safe ?>" class="btn-export" style="margin-left: auto;">
                    <i class="fas fa-file-excel"></i> Unduh Rekap Excel (.csv)
                </a>
            </form>
        </div>

        <div class="stat-grid">
            <div class="stat-box" style="background: linear-gradient(135deg, #002D62, #001f44);">
                <h4>Total Pengecekan Paspor</h4>
                <h2><?= number_format($tot_hits, 0, ',', '.') ?> <small style="font-size:0.8rem; font-weight:normal;">kali</small></h2>
            </div>
            
            <div class="stat-box" style="background: linear-gradient(135deg, #0284c7, #0369a1);">
                <h4>Pengunjung Unik (IP Berbeda)</h4>
                <h2><?= number_format($tot_ip, 0, ',', '.') ?> <small style="font-size:0.8rem; font-weight:normal;">user</small></h2>
            </div>

            <div class="stat-box" style="background: linear-gradient(135deg, #d97706, #b45309);">
                <h4>Paspor Paling Sering Dicari</h4>
                <h2 style="font-size: 1.3rem; margin-top:12px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                    <?= htmlspecialchars($top_paspor) ?>
                </h2>
                <small style="opacity:0.8;">Dicek sebanyak <?= $top_hit ?> kali</small>
            </div>
        </div>

        <div class="admin-card">
            <h3 class="card-title">Log Aktivitas Terperinci <span style="font-size:0.8rem; font-weight:normal; color:#64748b;">(Menampilkan maksimal 100 aktivitas terbaru pada rentang ini)</span></h3>
            
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Tanggal</th>
                            <th>Jam</th>
                            <th>Nomor Permohonan Dicari</th>
                            <th>Alamat IP Warga</th>
                            <th>Investigasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q_log = mysqli_query($conn, "SELECT * FROM log_pencarian $where_clause ORDER BY tanggal DESC, jam DESC LIMIT 100");
                        $no_tabel = 1;
                        
                        if($q_log && mysqli_num_rows($q_log) > 0){
                            while($row = mysqli_fetch_assoc($q_log)){
                                $tgl_indo = date('d/m/Y', strtotime($row['tanggal']));
                                ?>
                                <tr>
                                    <td><?= $no_tabel++ ?></td>
                                    <td><?= $tgl_indo ?></td>
                                    <td><?= htmlspecialchars($row['jam']) ?> WIB</td>
                                    <td><strong style="color:#002D62; letter-spacing:1px;"><?= htmlspecialchars($row['no_permohonan']) ?></strong></td>
                                    <td><span class="badge-ip"><?= htmlspecialchars($row['ip_address']) ?></span></td>
                                    <td>
                                        <a href="input_data.php?cari=<?= urlencode($row['no_permohonan']) ?>" class="btn-edit" style="text-decoration:none; padding:6px 12px; font-size:0.8rem; display:inline-flex; align-items:center; gap:5px;" title="Cek status paspor ini di sistem">
                                            <i class="fas fa-search-plus"></i> Intip Paspor
                                        </a>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; padding:40px; color:#94a3b8;'>Belum ada aktivitas pengecekan paspor pada rentang tanggal tersebut.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>