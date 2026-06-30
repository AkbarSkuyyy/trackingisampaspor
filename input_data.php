<?php
// MENGHIDUPKAN PELACAK ERROR
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Proteksi Halaman
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }

require_once 'koneksi.php';

// --- 1. PROSES SIMPAN / UPDATE ---
if (isset($_POST['simpan'])) {
    $no   = trim($_POST['no_permohonan']);
    $nama = trim($_POST['nama']);
    
    $tgl = trim($_POST['tgl_lahir']);
    if(empty($tgl)) $tgl = NULL; // Mengosongkan data dengan aman ke DB
    
    // Menangkap Tanggal Input
    $tgl_input = trim($_POST['tanggal_input']);
    if(empty($tgl_input)) $tgl_input = date('Y-m-d'); 
    
    $status = trim($_POST['status']);

    // Menambahkan tanggal_input ke dalam Query MySQL
    $query = "INSERT INTO permohonan (no_permohonan, nama_pemohon, tgl_lahir, tanggal_input, status_saat_ini) 
              VALUES (?, ?, ?, ?, ?) 
              ON DUPLICATE KEY UPDATE 
              nama_pemohon=VALUES(nama_pemohon), 
              tgl_lahir=COALESCE(VALUES(tgl_lahir), tgl_lahir), 
              tanggal_input=VALUES(tanggal_input),
              status_saat_ini=VALUES(status_saat_ini)";
              
    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        // Parameter diubah menjadi 5 string ("sssss")
        mysqli_stmt_bind_param($stmt, "sssss", $no, $nama, $tgl, $tgl_input, $status);
        if (mysqli_stmt_execute($stmt)) {
            $sukses = "Data permohonan dengan nomor $no berhasil disimpan/diperbarui.";
        } else {
            $error = "Gagal memproses data di database.";
        }
        mysqli_stmt_close($stmt);
    } else {
        $error = "Terjadi kesalahan sistem (Statement Error).";
    }
}

// --- 2. PROSES HAPUS DATA ---
if (isset($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $stmt = mysqli_prepare($conn, "DELETE FROM permohonan WHERE id = ?");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
    }
    header("Location: input_data.php?pesan=hapus_sukses");
    exit;
}

// --- 3. PENCARIAN & FILTER & PAGINATION ---
$search_query = "";
$search_value = "";
$status_value = "";
$url_params = "";
$conditions = [];

if (isset($_GET['cari']) && !empty(trim($_GET['cari']))) {
    $search_value = trim($_GET['cari']);
    $search_safe = mysqli_real_escape_string($conn, $search_value);
    $conditions[] = "(no_permohonan LIKE '%$search_safe%' OR nama_pemohon LIKE '%$search_safe%')";
    $url_params .= "&cari=" . urlencode($search_value);
}

if (isset($_GET['filter_status']) && !empty(trim($_GET['filter_status']))) {
    $status_value = trim($_GET['filter_status']);
    $status_safe = mysqli_real_escape_string($conn, $status_value);
    $conditions[] = "status_saat_ini = '$status_safe'";
    $url_params .= "&filter_status=" . urlencode($status_value);
}

if (!empty($conditions)) {
    $search_query = "WHERE " . implode(" AND ", $conditions);
}

$limit = 20;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$start_offset = ($page - 1) * $limit;

$q_count = mysqli_query($conn, "SELECT COUNT(*) AS total FROM permohonan $search_query");
$total_data = mysqli_fetch_assoc($q_count)['total'];
$total_pages = ceil($total_data / $limit);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input & Edit Data - Admin Imigrasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style> body { font-family: 'Outfit', sans-serif; } </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h2>Manajemen Data Permohonan</h2>
        </div>

        <div class="admin-card" id="form-card">
            <h3 class="card-title">Input Entri Baru / Edit Status Manual</h3>
            <p style="color: #64748b; font-size: 0.9rem; margin-bottom: 20px;">Gunakan form ini untuk memasukkan data baru secara manual, atau memperbaiki status/kesalahan data.</p>
            
            <form method="POST">
                <!-- Baris 1: No Permohonan & Nama -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Nomor Permohonan</label>
                        <input type="text" name="no_permohonan" id="inp_no" placeholder="Contoh: 106123456789" required autocomplete="off">
                    </div>
                    <div class="form-group">
                        <label>Nama Lengkap Pemohon</label>
                        <input type="text" name="nama" id="inp_nama" placeholder="Sesuai Dokumen Resmi" required autocomplete="off">
                    </div>
                </div>
                
                <!-- Baris 2: Tanggal Lahir & Tanggal Input -->
                <div class="form-row">
                    <div class="form-group">
                        <label>Tanggal Lahir</label>
                        <input type="date" name="tgl_lahir" id="inp_tgl" style="opacity:0.8;" required>
                    </div>
                    <div class="form-group">
                        <label>Tanggal Input Berkas</label>
                        <!-- Default valuenya hari ini, tapi bisa diedit mundur/maju oleh admin jika perlu -->
                        <input type="date" name="tanggal_input" id="inp_tgl_input" value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                
                <!-- Baris 3: Status -->
                <div class="form-row">
                    <div class="form-group" style="width: 100%;">
                        <label>Status Saat Ini</label>
                        <select name="status" id="inp_status" required>
                            <option value="Wawancara & Foto Selesai">Wawancara & Foto Selesai</option>
                            <option value="Menunggu Pembayaran">Menunggu Pembayaran</option>
                            <option value="Alokasi">Alokasi</option>
                            <option value="Paspor Sedang Dicetak">Paspor Sedang Dicetak</option>
                            <option value="Paspor Selesai / Bisa Diambil">Paspor Selesai / Bisa Diambil</option>
                        </select>
                    </div>
                </div>
                
                <button type="submit" name="simpan" class="btn-submit" style="font-family: 'Outfit'; font-size: 1.05rem; width: 100%; margin-top: 10px;">
                    <i class="fas fa-save"></i> Simpan / Perbarui Data
                </button>
            </form>
        </div>

        <div class="admin-card" id="tabel-data">
            <h3 class="card-title">Database Permohonan (Total: <?= number_format($total_data, 0, ',', '.') ?> Berkas)</h3>
            
            <form method="GET" action="" style="display: flex; gap: 10px; margin-bottom: 25px; flex-wrap: wrap;">
                <input type="text" name="cari" placeholder="Cari nama/nomor..." value="<?= htmlspecialchars($search_value) ?>" style="flex: 1; padding: 12px 18px; border: 2px solid #cbd5e1; border-radius: 8px; font-family: 'Outfit'; font-size: 1rem; min-width: 200px;">
                
                <select name="filter_status" style="padding: 12px 18px; border: 2px solid #cbd5e1; border-radius: 8px; font-family: 'Outfit'; font-size: 1rem; background: white;">
                    <option value="">Semua Status</option>
                    <option value="Wawancara & Foto Selesai" <?= ($status_value == 'Wawancara & Foto Selesai') ? 'selected' : '' ?>>Wawancara & Foto Selesai</option>
                    <option value="Menunggu Pembayaran" <?= ($status_value == 'Menunggu Pembayaran') ? 'selected' : '' ?>>Menunggu Pembayaran</option>
                    <option value="Alokasi" <?= ($status_value == 'Alokasi') ? 'selected' : '' ?>>Alokasi</option>
                    <option value="Paspor Sedang Dicetak" <?= ($status_value == 'Paspor Sedang Dicetak') ? 'selected' : '' ?>>Paspor Sedang Dicetak</option>
                    <option value="Paspor Selesai / Bisa Diambil" <?= ($status_value == 'Paspor Selesai / Bisa Diambil') ? 'selected' : '' ?>>Paspor Selesai / Bisa Diambil</option>
                </select>
                
                <button type="submit" class="btn-submit" style="padding: 12px 25px; margin: 0; font-family: 'Outfit';"><i class="fas fa-search"></i> Filter</button>
                <?php if(!empty($search_value) || !empty($status_value)): ?>
                    <a href="input_data.php" class="btn-hapus" style="display: flex; align-items: center; justify-content: center; padding: 12px 20px; margin: 0; text-decoration: none; border-radius: 8px; background: #ef4444; color: white;"><i class="fas fa-times" style="margin-right: 5px;"></i> Reset</a>
                <?php endif; ?>
            </form>

            <div class="table-responsive">
                <table>
                    <thead>
                        <tr>
                            <th>No Permohonan</th>
                            <th>Nama Pemohon</th>
                            <th>Tanggal Lahir</th>
                            <th>Tgl Input</th> <!-- Kolom Baru -->
                            <th>Status Paspor</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $q_data = mysqli_query($conn, "SELECT * FROM permohonan $search_query ORDER BY id DESC LIMIT $start_offset, $limit");
                        
                        if(mysqli_num_rows($q_data) > 0){
                            while($row = mysqli_fetch_assoc($q_data)){
                                $tgl_indo = !empty($row['tgl_lahir']) ? date('d-m-Y', strtotime($row['tgl_lahir'])) : '<span style="color:#cbd5e1; font-style:italic;">Kosong</span>';
                                
                                // Format Tanggal Input
                                $tgl_input_indo = !empty($row['tanggal_input']) ? date('d-m-Y', strtotime($row['tanggal_input'])) : '-';
                                ?>
                                <tr>
                                    <td><strong><?= htmlspecialchars($row['no_permohonan']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['nama_pemohon']) ?></td>
                                    <td><?= $tgl_indo ?></td>
                                    <td><?= $tgl_input_indo ?></td> <!-- Menampilkan Tanggal Input -->
                                    <td><span class='badge'><?= htmlspecialchars($row['status_saat_ini']) ?></span></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button type="button" class="btn-edit" onclick="editData('<?= htmlspecialchars($row['no_permohonan']) ?>', '<?= htmlspecialchars(addslashes($row['nama_pemohon'])) ?>', '<?= htmlspecialchars($row['tgl_lahir']) ?>', '<?= htmlspecialchars($row['tanggal_input']) ?>', '<?= htmlspecialchars($row['status_saat_ini']) ?>')" title="Edit Data">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <a href='#' class='btn-hapus' onclick="confirmDelete(event, '?hapus=<?= $row['id'] ?>')" title="Hapus Data">
                                                <i class='fas fa-trash-alt'></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo "<tr><td colspan='6' style='text-align:center; padding:30px; color:#94a3b8;'><i class='fas fa-folder-open' style='font-size:2.5rem; margin-bottom:15px; display:block;'></i>Data tidak ditemukan.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>

            <?php if($total_pages > 1): ?>
            <div class="pagination">
                <?php if($page > 1): ?>
                    <a href="?page=<?= $page - 1 ?><?= $url_params ?>#tabel-data" class="page-link"><i class="fas fa-chevron-left"></i> Prev</a>
                <?php endif; ?>
                
                <?php 
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $page + 2);
                
                if($start_page > 1) { echo '<span style="padding:8px 12px;color:#94a3b8;font-weight:bold;">...</span>'; }
                
                for($i = $start_page; $i <= $end_page; $i++): 
                ?>
                    <a href="?page=<?= $i ?><?= $url_params ?>#tabel-data" class="page-link <?= ($i == $page) ? 'active' : '' ?>"><?= $i ?></a>
                <?php endfor; ?>
                
                <?php if($end_page < $total_pages) { echo '<span style="padding:8px 12px;color:#94a3b8;font-weight:bold;">...</span>'; } ?>

                <?php if($page < $total_pages): ?>
                    <a href="?page=<?= $page + 1 ?><?= $url_params ?>#tabel-data" class="page-link">Next <i class="fas fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>

        </div>
    </main>

    <script>
        // Penambahan parameter tgl_input agar saat di-klik Edit, tanggal input ikut terisi ke form
        function editData(no, nama, tgl, tgl_input, status) {
            document.getElementById('inp_no').value = no;
            document.getElementById('inp_nama').value = nama;
            document.getElementById('inp_tgl').value = tgl;
            document.getElementById('inp_tgl_input').value = tgl_input;
            document.getElementById('inp_status').value = status;
            
            window.scrollTo({ top: 0, behavior: 'smooth' });
            
            const formCard = document.getElementById('form-card');
            formCard.style.transition = "box-shadow 0.3s ease";
            formCard.style.boxShadow = "0 0 0 4px rgba(249, 199, 79, 0.8)";
            
            document.getElementById('inp_status').focus();
            
            setTimeout(() => { formCard.style.boxShadow = "0 20px 40px rgba(0,0,0,0.05)"; }, 1500);
        }

        function confirmDelete(e, url) {
            e.preventDefault();
            Swal.fire({
                title: 'Hapus Data Ini?',
                text: "Tindakan ini permanen dan tidak bisa dibatalkan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#002D62',
                confirmButtonText: 'Ya, Hapus Data!',
                cancelButtonText: 'Batal',
                fontFamily: 'Outfit'
            }).then((result) => {
                if (result.isConfirmed) { window.location.href = url; }
            });
        }
    </script>

    <?php if(isset($sukses)): ?>
    <script>
        Swal.fire({ icon: 'success', title: 'Tersimpan!', text: '<?= $sukses ?>', confirmButtonColor: '#10b981' });
        if ( window.history.replaceState ) { window.history.replaceState( null, null, window.location.href ); }
    </script>
    <?php endif; ?>

    <?php if(isset($error)): ?>
    <script>
        Swal.fire({ icon: 'error', title: 'Terjadi Kesalahan', text: '<?= $error ?>', confirmButtonColor: '#ef4444' });
    </script>
    <?php endif; ?>

    <?php if(isset($_GET['pesan']) && $_GET['pesan'] == 'hapus_sukses'): ?>
    <script>
        Swal.fire({ icon: 'success', title: 'Dihapus!', text: 'Data permohonan berhasil dihapus dari sistem.', confirmButtonColor: '#10b981' });
        const url = new URL(window.location);
        url.searchParams.delete('pesan');
        window.history.replaceState(null, null, url);
    </script>
    <?php endif; ?>

</body>
</html>
<?php 
// 2. Pemutus Sesi Database (Aturan Anti-Crash)
if (isset($conn)) {
    mysqli_close($conn);
}
?>