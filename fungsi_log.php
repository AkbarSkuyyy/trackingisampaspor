<?php
// fungsi_log.php - Modul pencatat aktivitas admin
function catat_aktivitas($conn, $aktivitas) {
    // 1. Pastikan tabel log ada
    $sql_create = "CREATE TABLE IF NOT EXISTS log_aktivitas (
        id INT AUTO_INCREMENT PRIMARY KEY,
        waktu DATETIME DEFAULT CURRENT_TIMESTAMP,
        admin_id VARCHAR(50),
        nama_admin VARCHAR(100),
        aktivitas TEXT
    )";
    mysqli_query($conn, $sql_create);

    // 2. Ambil data admin
    $admin = isset($_SESSION['admin']) ? $_SESSION['admin'] : 'Sistem';
    $nama = isset($_SESSION['nama_admin']) ? $_SESSION['nama_admin'] : 'Admin';

    // 3. Simpan log
    $stmt = mysqli_prepare($conn, "INSERT INTO log_aktivitas (admin_id, nama_admin, aktivitas) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $admin, $nama, $aktivitas);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
}
?>