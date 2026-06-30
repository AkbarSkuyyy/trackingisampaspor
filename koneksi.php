<?php
// ============================================================================
// KONEKSI.PHP - Jembatan Penghubung Sistem dengan Database (Anti-Crash Version)
// ============================================================================

$host = "localhost";
$user = "etrackin_paspor";      
$pass = "Paspor1!"; // *Sesuaikan jika Anda sudah mengubah sandinya di cPanel
$db   = "etrackin_paspor";      

try {
    // Membuka koneksi
    $conn = mysqli_connect($host, $user, $pass, $db);
    mysqli_set_charset($conn, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    // Jika terjadi antrean penuh (Too Many Connections)
    if (strpos($e->getMessage(), 'Too many connections') !== false) {
        die("
            <div style='text-align:center; padding:50px; font-family:sans-serif; background:#f8fafc; color:#0f172a; height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center;'>
                <h2 style='color:#ef4444; margin-bottom:10px;'>⚠️ Server Sedang Padat</h2>
                <p style='color:#64748b; margin:0;'>Lalu lintas data ke database sedang penuh saat ini.</p>
                <p style='color:#64748b; margin:5px 0 20px 0;'>Sistem akan normal kembali dalam beberapa saat.</p>
                <button onclick='window.location.reload()' style='background:#002D62; color:#fff; border:none; padding:12px 25px; border-radius:8px; font-weight:bold; cursor:pointer;'>Muat Ulang Halaman</button>
            </div>
        ");
    } else {
        // Jika error disebabkan hal lain (misal user/password salah)
        die("Koneksi Database Gagal: " . $e->getMessage());
    }
}
?>