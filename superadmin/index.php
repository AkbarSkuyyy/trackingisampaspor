<?php
session_start();
include '../koneksi.php'; 

// Proteksi Otoritas
if (!isset($_SESSION['admin'])) {
    header("Location: ../login");
    exit;
}

$nama_sesi = $_SESSION['nama_petugas'] ?? '';
$q_cek = mysqli_query($conn, "SELECT id, level FROM admin WHERE nama_lengkap = '$nama_sesi' LIMIT 1");
$d_cek = mysqli_fetch_assoc($q_cek);

if (!$d_cek || $d_cek['level'] !== 'superadmin') {
    die("
        <div style='font-family:sans-serif; text-align:center; padding:50px; background:#fef2f2; color:#991b1b; height:100vh;'>
            <h1 style='font-size:3rem;'>⛔ AKSES DITOLAK</h1>
            <p style='font-size:1.2rem;'>Area ini adalah Ruang Kendali Master. Hak akses Anda terdeteksi sebagai Petugas Biasa.</p>
            <br><a href='../dashboard' style='background:#002D62; color:#fff; padding:12px 25px; text-decoration:none; border-radius:8px;'>&larr; Kembali ke Ruang Kerja Petugas</a>
        </div>
    ");
}

$id_super_admin_aktif = $d_cek['id'];
$pesan = "";

// Tambah Petugas
if (isset($_POST['tambah_petugas'])) {
    $user_baru  = mysqli_real_escape_string($conn, $_POST['username']);
    $nama_baru  = mysqli_real_escape_string($conn, $_POST['nama_lengkap']);
    $pass_raw   = $_POST['password'];
    $level_baru = $_POST['level'];
    
    $pass_hash = password_hash($pass_raw, PASSWORD_BCRYPT);
    $cek_user = mysqli_query($conn, "SELECT id FROM admin WHERE username='$user_baru'");
    
    if (mysqli_num_rows($cek_user) > 0) {
        $pesan = "<div class='alert-err'><i class='fas fa-exclamation-triangle'></i> Username sudah terdaftar!</div>";
    } else {
        $insert = mysqli_query($conn, "INSERT INTO admin (username, password, nama_lengkap, level) VALUES ('$user_baru', '$pass_hash', '$nama_baru', '$level_baru')");
        if ($insert) $pesan = "<div class='alert-ok'><i class='fas fa-check-circle'></i> Akun petugas berhasil diterbitkan!</div>";
    }
}

// Hapus Petugas
if (isset($_GET['hapus'])) {
    $id_hapus = (int)$_GET['hapus'];
    if ($id_hapus === (int)$id_super_admin_aktif) {
        $pesan = "<div class='alert-err'>Sistem Keamanan: Anda dilarang menghapus akun Anda sendiri!</div>";
    } else {
        mysqli_query($conn, "DELETE FROM admin WHERE id='$id_hapus'");
        header("Location: index");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Master Control Room - Super Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <div class="super-main-content">
        
        <div class="page-header">
            <h1>Manajemen Otoritas Akses</h1>
            <p>Kelola akun pendaftaran petugas dan tingkat wewenang di dalam sistem.</p>
        </div>

        <div class="grid-container">
            <div class="panel-card">
                <h3><i class="fas fa-user-plus"></i> Terbitkan Akun Baru</h3>
                <?= $pesan; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Nama Lengkap Petugas</label>
                        <input type="text" name="nama_lengkap" placeholder="Contoh: Budi Santoso" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Username Akses</label>
                        <input type="text" name="username" placeholder="budi_loket1" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Kata Sandi Awal</label>
                        <input type="text" name="password" placeholder="Ketik sandi sementara..." required>
                    </div>
                    <div class="form-group">
                        <label>Tingkat Otoritas (Level)</label>
                        <select name="level">
                            <option value="petugas">Petugas Loket (Biasanya)</option>
                            <option value="superadmin">Super Admin (Kepala / IT)</option>
                        </select>
                    </div>
                    <button type="submit" name="tambah_petugas" class="btn-gold">TERBITKAN AKUN</button>
                </form>
            </div>

            <div class="panel-card">
                <h3><i class="fas fa-users-viewfinder"></i> Daftar Petugas Terdaftar</h3>
                <table>
                    <thead>
                        <tr>
                            <th>NAMA PETUGAS</th>
                            <th>USERNAME</th>
                            <th>KASTA</th>
                            <th>KONTROL</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q_users = mysqli_query($conn, "SELECT * FROM admin ORDER BY id DESC");
                        while($row = mysqli_fetch_assoc($q_users)):
                        ?>
                        <tr>
                            <td><b style="color:#f8fafc;"><?= htmlspecialchars($row['nama_lengkap']); ?></b></td>
                            <td style="color:#94a3b8;">@<?= htmlspecialchars($row['username']); ?></td>
                            <td>
                                <?php if($row['level'] == 'superadmin'): ?>
                                    <span class="badge-super"><i class="fas fa-crown"></i> SUPER ADMIN</span>
                                <?php else: ?>
                                    <span class="badge-staff">PETUGAS LOKET</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($row['id'] != $id_super_admin_aktif): ?>
                                    <a href="index?hapus=<?= $row['id']; ?>" class="btn-del" onclick="return confirm('Cabut hak akses petugas ini?')"><i class="fas fa-trash-alt"></i> Hapus</a>
                                <?php else: ?>
                                    <span style="color:#64748b; font-size:0.8rem; font-style:italic;">(Akun Anda)</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
    </div>

</body>
</html>