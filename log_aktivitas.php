<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
require_once 'koneksi.php';
// Pastikan fungsi log terinclude agar tabel otomatis terbuat jika belum ada
require_once 'fungsi_log.php'; 

// Panggil fungsi log untuk memastikan tabel ada (jika belum pernah dijalankan)
catat_aktivitas($conn, "Membuka halaman Log Aktivitas");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Log Aktivitas Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <?php include 'sidebar.php'; ?>
    <main class="admin-main">
        <div class="admin-header"><h2><i class="fas fa-history"></i> Riwayat Aktivitas Sistem</h2></div>
        
        <div class="admin-card">
            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Admin</th>
                            <th>Aktivitas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Tambahkan pengecekan error pada query
                        $query = "SELECT * FROM log_aktivitas ORDER BY waktu DESC LIMIT 50";
                        $q_log = mysqli_query($conn, $query);

                        if (!$q_log) {
                            // Jika query gagal, tampilkan pesan error yang jelas
                            echo "<tr><td colspan='3' style='text-align:center; color:red;'>
                                    <strong>Error Database:</strong> " . mysqli_error($conn) . 
                                    "<br><small>Pastikan tabel 'log_aktivitas' sudah ada di database.</small>
                                  </td></tr>";
                        } elseif (mysqli_num_rows($q_log) == 0) {
                            echo "<tr><td colspan='3' style='text-align:center;'>Belum ada aktivitas tercatat.</td></tr>";
                        } else {
                            while($row = mysqli_fetch_assoc($q_log)){
                                echo "<tr>
                                        <td>".date('d/m/Y H:i', strtotime($row['waktu']))."</td>
                                        <td><strong>".htmlspecialchars($row['nama_admin'])."</strong></td>
                                        <td>".htmlspecialchars($row['aktivitas'])."</td>
                                      </tr>";
                            }
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>
</body>
</html>