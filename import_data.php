<?php
session_start();
if (!isset($_SESSION['admin'])) { header("Location: login.php"); exit; }
require_once 'koneksi.php';
require_once 'fungsi_log.php';

// AUTO-PATCH DATABASE (Tanpa perlu phpMyAdmin manual)
$check1 = mysqli_query($conn, "SHOW COLUMNS FROM permohonan LIKE 'jenis_permohonan'");
if(mysqli_num_rows($check1) == 0) { mysqli_query($conn, "ALTER TABLE permohonan ADD jenis_permohonan VARCHAR(150) NULL AFTER tgl_lahir"); }

$check2 = mysqli_query($conn, "SHOW COLUMNS FROM permohonan LIKE 'tgl_permohonan'");
if(mysqli_num_rows($check2) == 0) { mysqli_query($conn, "ALTER TABLE permohonan ADD tgl_permohonan DATE NULL AFTER jenis_permohonan"); }

if (isset($_POST['import'])) {
    $berhasil_insert = 0;
    $berhasil_update = 0;
    $dilewati = 0;
    
    // Hirarki Status untuk Mencegah "Downgrade" (Permohonan Masuk Dihapus, Wawancara jadi Base Level)
    $status_hierarchy = [
        "Wawancara & Foto Selesai" => 2,
        "Menunggu Pembayaran" => 3,
        "Alokasi" => 4,
        "Paspor Sedang Dicetak" => 5,
        "Paspor Selesai / Bisa Diambil" => 6
    ];
    
    if (isset($_POST['csv_data']) && !empty($_POST['csv_data'])) {
        $lines = explode("\n", trim($_POST['csv_data']));
        
        // AUTO-PATCH: Pastikan kolom tanggal boleh bernilai NULL agar tidak terjadi error MySQL Strict Mode
        mysqli_query($conn, "ALTER TABLE permohonan MODIFY tgl_lahir DATE NULL");
        mysqli_query($conn, "ALTER TABLE permohonan MODIFY tgl_permohonan DATE NULL");

        $stmt_check = mysqli_prepare($conn, "SELECT status_saat_ini FROM permohonan WHERE no_permohonan = ?");
        $stmt_insert = mysqli_prepare($conn, "INSERT INTO permohonan (no_permohonan, nama_pemohon, tgl_lahir, jenis_permohonan, tgl_permohonan, status_saat_ini) VALUES (?, ?, ?, ?, ?, ?)");
        
        // Update membiarkan tanggal lama tetap utuh menggunakan COALESCE jika data baru bernilai NULL
        $stmt_update = mysqli_prepare($conn, "UPDATE permohonan SET nama_pemohon=?, jenis_permohonan=?, tgl_permohonan=COALESCE(?, tgl_permohonan), tgl_lahir=COALESCE(?, tgl_lahir), status_saat_ini=? WHERE no_permohonan=?");
        
        foreach ($lines as $line) {
            if (empty(trim($line))) continue;
            
            $row = explode("~|~", $line);
            
            if(count($row) >= 6) {
                $no_permohonan    = trim($row[0]);
                $nama_pemohon     = trim($row[1]);
                $tgl_lahir        = trim($row[2]);
                $status           = trim($row[3]);
                $jenis_permohonan = trim($row[4]);
                $tgl_permohonan   = trim($row[5]);
                
                // Konversi tanggal DD-MM-YYYY ke YYYY-MM-DD
                // Format Tanggal Lahir
                $tgl_l_val = NULL;
                if(!empty($tgl_lahir)) {
                    if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $tgl_lahir)) {
                        $tgl_l_val = date('Y-m-d', strtotime($tgl_lahir));
                    } else {
                        $tgl_l_val = $tgl_lahir; // Asumsi sudah Y-m-d
                    }
                }
                
                // Format Tanggal Permohonan
                $tgl_p_val = NULL;
                if(!empty($tgl_permohonan)) {
                    if (preg_match('/^\d{2}-\d{2}-\d{4}$/', $tgl_permohonan)) {
                        $tgl_p_val = date('Y-m-d', strtotime($tgl_permohonan));
                    } else {
                        $tgl_p_val = $tgl_permohonan; // Asumsi sudah Y-m-d
                    }
                }
                
                // Amankan Nama dari potensi Spasi di awal/akhir
                $nama_pemohon = trim($nama_pemohon);
                
                if(!empty($no_permohonan)) {
                    mysqli_stmt_bind_param($stmt_check, "s", $no_permohonan);
                    mysqli_stmt_execute($stmt_check);
                    $result = mysqli_stmt_get_result($stmt_check);
                    
                    if(mysqli_num_rows($result) > 0) {
                        $row_db = mysqli_fetch_assoc($result);
                        $status_lama = $row_db['status_saat_ini'];
                        
                        $bobot_lama = isset($status_hierarchy[$status_lama]) ? $status_hierarchy[$status_lama] : 0;
                        $bobot_baru = isset($status_hierarchy[$status]) ? $status_hierarchy[$status] : 0;
                        
                        if($bobot_baru >= $bobot_lama) {
                            mysqli_stmt_bind_param($stmt_update, "ssssss", $nama_pemohon, $jenis_permohonan, $tgl_p_val, $tgl_l_val, $status, $no_permohonan);
                            mysqli_stmt_execute($stmt_update);
                            $berhasil_update++;
                        } else {
                            $dilewati++;
                        }
                    } else {
                        mysqli_stmt_bind_param($stmt_insert, "ssssss", $no_permohonan, $nama_pemohon, $tgl_l_val, $jenis_permohonan, $tgl_p_val, $status);
                        mysqli_stmt_execute($stmt_insert);
                        $berhasil_insert++;
                    }
                }
            }
        }
        
        $total_proses = $berhasil_insert + $berhasil_update;
        catat_aktivitas($conn, "Melakukan Import Data Massal sebanyak $total_proses berkas.");
        $sukses = "Selesai! $berhasil_insert Data Baru, $berhasil_update Data Diupdate. ($dilewati dilewati karena status di sistem lebih tinggi).";
    } else {
        $error = "Data kosong atau gagal terbaca. Pastikan Anda telah memilih minimal 1 file.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Import Laporan Imigrasi - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/admin.css">
    <script src="https://cdn.jsdelivr.net/npm/xlsx/dist/xlsx.full.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: 'Outfit', sans-serif; }
        
        .upload-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 25px;
        }
        
        .upload-zone {
            background: #f8fafc;
            border: 2px dashed #cbd5e1;
            border-radius: 12px;
            padding: 20px 15px;
            text-align: center;
            position: relative;
            transition: all 0.3s;
        }
        
        .upload-zone:hover { background: #e2e8f0; border-color: #002D62; }
        .upload-zone.filled { background: #f0fdf4; border-color: #10b981; border-style: solid; }
        
        .upload-zone input[type="file"] {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            opacity: 0; cursor: pointer;
        }
        
        .upload-zone i { font-size: 2rem; color: #94a3b8; margin-bottom: 10px; transition: 0.3s; }
        .upload-zone.filled i { color: #10b981; }
        
        .zone-title { font-weight: 700; color: #002D62; font-size: 0.95rem; margin-bottom: 5px; }
        .file-name { font-size: 0.8rem; color: #64748b; margin-top: 5px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        
        .zone-badge {
            display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 0.75rem; 
            font-weight: 600; color: white; margin-bottom: 10px;
        }
        .bg-wawancara { background: #3b82f6; }
        .bg-alokasi { background: #8b5cf6; }
        .bg-cetak { background: #f59e0b; }
        .bg-ambil { background: #10b981; }
    </style>
</head>
<body>

    <?php include 'sidebar.php'; ?>

    <main class="admin-main">
        <div class="admin-header">
            <h2>Sinkronisasi SPRI (Multi-Zona)</h2>
        </div>

        <div class="admin-card">
            <h3 class="card-title">Unggah Laporan Sesuai Tahapan</h3>
            <p style="color: #64748b; font-size: 0.95rem; margin-bottom: 25px;">
                Isi kotak-kotak di bawah ini dengan file Excel yang sesuai. Data <b>Tanggal Lahir</b> hanya akan dibaca otomatis dari file <b>Laporan Penerbitan</b> (Paspor Selesai).
            </p>

            <form method="POST" id="formImport">
                
                <div class="upload-grid">
                    <!-- ZONA 1: WAWANCARA -->
                    <div class="upload-zone" id="zone_wawancara">
                        <span class="zone-badge bg-wawancara">1. Wawancara & Foto</span><br>
                        <i class="fas fa-camera" id="icon_wawancara"></i>
                        <div class="zone-title">Laporan Wawancara</div>
                        <div class="file-name" id="name_wawancara">Belum ada file</div>
                        <input type="file" id="f_wawancara" accept=".xls,.xlsx,.csv" onchange="updateZone('wawancara', this)">
                    </div>

                    <!-- ZONA 2: ALOKASI -->
                    <div class="upload-zone" id="zone_alokasi">
                        <span class="zone-badge bg-alokasi">2. Alokasi Paspor</span><br>
                        <i class="fas fa-clipboard-check" id="icon_alokasi"></i>
                        <div class="zone-title">Laporan Alokasi</div>
                        <div class="file-name" id="name_alokasi">Belum ada file</div>
                        <input type="file" id="f_alokasi" accept=".xls,.xlsx,.csv" onchange="updateZone('alokasi', this)">
                    </div>

                    <!-- ZONA 3: PENCETAKAN -->
                    <div class="upload-zone" id="zone_pencetakan">
                        <span class="zone-badge bg-cetak">3. Proses Cetak</span><br>
                        <i class="fas fa-print" id="icon_pencetakan"></i>
                        <div class="zone-title">Laporan Pencetakan</div>
                        <div class="file-name" id="name_pencetakan">Belum ada file</div>
                        <input type="file" id="f_pencetakan" accept=".xls,.xlsx,.csv" onchange="updateZone('pencetakan', this)">
                    </div>

                    <!-- ZONA 4: PENYERAHAN -->
                    <div class="upload-zone" id="zone_penyerahan">
                        <span class="zone-badge bg-ambil">4. Paspor Selesai</span><br>
                        <i class="fas fa-check-circle" id="icon_penyerahan"></i>
                        <div class="zone-title">Laporan Penerbitan</div>
                        <div class="file-name" id="name_penyerahan">Belum ada file</div>
                        <input type="file" id="f_penyerahan" accept=".xls,.xlsx,.csv" onchange="updateZone('penyerahan', this)">
                    </div>
                </div>
                
                <input type="hidden" name="csv_data" id="csv_data">
                <input type="hidden" name="import" value="1">
                
                <button type="submit" class="btn-submit" id="btnProses" style="width: 100%; padding: 18px; border-radius: 8px; font-size: 1.15rem; font-family: 'Outfit';">
                    <i class="fas fa-sync-alt"></i> Proses & Integrasikan Semua Data
                </button>
            </form>
        </div>
    </main>

    <script>
        // Mengubah warna kotak jika file diisi
        function updateZone(zoneId, input) {
            const zone = document.getElementById('zone_' + zoneId);
            const nameLabel = document.getElementById('name_' + zoneId);
            
            if(input.files.length > 0) {
                zone.classList.add('filled');
                nameLabel.innerHTML = '<strong>' + input.files[0].name + '</strong>';
                nameLabel.style.color = '#10b981';
            } else {
                zone.classList.remove('filled');
                nameLabel.innerText = 'Belum ada file';
                nameLabel.style.color = '#64748b';
            }
        }

        // Daftar input (Hapus Permohonan Masuk/ALL)
        const inputsConfig = [
            { id: 'f_wawancara', status: 'Wawancara & Foto Selesai', weight: 2 },
            { id: 'f_alokasi', status: 'Alokasi', weight: 4 },
            { id: 'f_pencetakan', status: 'Paspor Sedang Dicetak', weight: 5 },
            { id: 'f_penyerahan', status: 'Paspor Selesai / Bisa Diambil', weight: 6 }
        ];

        document.getElementById('formImport').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            let filesToProcess = inputsConfig.filter(conf => document.getElementById(conf.id).files.length > 0);
            
            if (filesToProcess.length === 0) {
                Swal.fire('Form Kosong', 'Silakan pilih minimal 1 file Excel terlebih dahulu.', 'warning');
                return;
            }
            
            const btnProses = document.getElementById('btnProses');
            btnProses.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sedang Membaca Excel...';
            btnProses.disabled = true;

            let pemohonMap = new Map();

            try {
                // Proses setiap file yang diisi
                for (let conf of filesToProcess) {
                    let file = document.getElementById(conf.id).files[0];
                    let data = await file.arrayBuffer();
                    let workbook = XLSX.read(data, {type: 'array'});
                    let json = XLSX.utils.sheet_to_json(workbook.Sheets[workbook.SheetNames[0]], {header: 1, defval: ""});

                    // Pencarian Dinamis Kolom (Mendukung Laporan Tahapan & Penerbitan)
                    let headerRowIdx = -1;
                    let colNo = -1, colNama = -1, colJenis = -1, colTglMohon = -1, colTglLahir = -1;
                    
                    for(let r=0; r<15; r++){
                        if(!json[r]) continue;
                        for(let c=0; c<json[r].length; c++) {
                            let val = json[r][c] ? json[r][c].toString().trim().toUpperCase() : "";
                            if(val === "NOMOR PEMOHON" || val === "NO PERMOHONAN") { headerRowIdx = r; colNo = c; }
                            if(val === "NAMA PEMOHON" || val === "NAMA") colNama = c;
                            if(val === "JENIS PERMOHONAN") colJenis = c;
                            if(val === "TANGGAL PERMOHONAN") colTglMohon = c;
                            if(val === "TANGGAL LAHIR") colTglLahir = c;
                        }
                        if(headerRowIdx !== -1) break;
                    }

                    if(headerRowIdx !== -1) {
                        for(let j = headerRowIdx + 1; j < json.length; j++) {
                            let row = json[j];
                            if(row && colNo !== -1 && row[colNo] && row[colNo].toString().trim().length >= 15 && colNama !== -1 && row[colNama]) {
                                let no = row[colNo].toString().trim();
                                let nama = row[colNama].toString().trim();
                                let jenis = (colJenis !== -1 && row[colJenis]) ? row[colJenis].toString().trim() : "Tidak Diketahui";
                                let tgl_mohon = (colTglMohon !== -1 && row[colTglMohon]) ? row[colTglMohon].toString().trim() : "";
                                let tgl_lahir = (colTglLahir !== -1 && row[colTglLahir]) ? row[colTglLahir].toString().trim() : "";
                                
                                if (pemohonMap.has(no)) {
                                    let existing = pemohonMap.get(no);
                                    if (conf.weight > existing.weight) {
                                        pemohonMap.set(no, { nama: nama, tgl: tgl_lahir, status: conf.status, jenis: jenis, tgl_mohon: tgl_mohon, weight: conf.weight });
                                    }
                                } else {
                                    pemohonMap.set(no, { nama: nama, tgl: tgl_lahir, status: conf.status, jenis: jenis, tgl_mohon: tgl_mohon, weight: conf.weight });
                                }
                            }
                        }
                    }
                }
                
                // Siapkan data CSV final
                if (pemohonMap.size > 0) {
                    let allCsvRows = [];
                    pemohonMap.forEach((data, no) => {
                        allCsvRows.push([no, data.nama, data.tgl, data.status, data.jenis, data.tgl_mohon].join('~|~'));
                    });
                    
                    document.getElementById('csv_data').value = allCsvRows.join('\n');
                    document.getElementById('formImport').submit();
                } else {
                    Swal.fire('Data Kosong', 'File berhasil dibaca tetapi tidak ditemukan header/data yang sesuai.', 'warning');
                    btnProses.innerHTML = '<i class="fas fa-sync-alt"></i> Proses & Integrasikan Semua Data';
                    btnProses.disabled = false;
                }

            } catch (err) {
                console.error(err);
                Swal.fire('Gagal', 'Terjadi kesalahan saat membaca file. Pastikan file bukan hasil edit manual yang rusak.', 'error');
                btnProses.innerHTML = '<i class="fas fa-sync-alt"></i> Proses & Integrasikan Semua Data';
                btnProses.disabled = false;
            }
        });
    </script>

    <?php if(isset($sukses)): ?>
    <script>
        Swal.fire({ icon: 'success', title: 'Integrasi Berhasil!', text: '<?= $sukses ?>', confirmButtonColor: '#10b981' });
        if ( window.history.replaceState ) { window.history.replaceState( null, null, window.location.href ); }
    </script>
    <?php endif; ?>
    
    <?php if(isset($error)): ?>
    <script>
        Swal.fire({ icon: 'error', title: 'Proses Ditolak', text: '<?= $error ?>', confirmButtonColor: '#ef4444' });
    </script>
    <?php endif; ?>

</body>
</html>