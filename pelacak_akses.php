<?php
// pelacak_akses.php - Modul Khusus Radar Pelacak Pengunjung (Terpisah)

// Cegah akses langsung ke file ini melalui URL browser
if (basename($_SERVER['PHP_SELF']) == basename(__FILE__)) {
    header("HTTP/1.1 403 Forbidden");
    exit("Akses langsung ditolak.");
}

function catat_log_pencarian($conn, $no_permohonan) {
    // 1. AUTO-CREATE: Buat tabel log_pencarian secara otomatis jika belum ada
    $sql_create = "CREATE TABLE IF NOT EXISTS log_pencarian (
        id INT AUTO_INCREMENT PRIMARY KEY,
        tanggal DATE,
        jam TIME,
        no_permohonan VARCHAR(100),
        ip_address VARCHAR(50)
    )";
    mysqli_query($conn, $sql_create);

    // 2. Tangkap data pengunjung (Waktu & IP Address)
    $tgl_sekarang = date('Y-m-d');
    $jam_sekarang = date('H:i:s');
    
    // Menangkap IP asli warga (Kompatibel dengan Cloudflare / Proxy)
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        $ip_pemohon = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ip_pemohon = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        $ip_pemohon = $_SERVER['REMOTE_ADDR'];
    }

    // 3. Simpan ke database menggunakan Prepared Statement agar aman dari serangan
    $stmt_log = mysqli_prepare($conn, "INSERT INTO log_pencarian (tanggal, jam, no_permohonan, ip_address) VALUES (?, ?, ?, ?)");
    if ($stmt_log) {
        mysqli_stmt_bind_param($stmt_log, "ssss", $tgl_sekarang, $jam_sekarang, $no_permohonan, $ip_pemohon);
        mysqli_stmt_execute($stmt_log);
        mysqli_stmt_close($stmt_log);
    }
}
?>