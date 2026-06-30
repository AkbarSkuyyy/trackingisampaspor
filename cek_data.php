<?php
// cek_data.php - Penyedia Data Paspor (+ Radar Kalkulator 30 Hari)

header('Content-Type: application/json');
error_reporting(0); 

require_once 'koneksi.php';

if (!isset($_GET['no']) || empty(trim($_GET['no']))) {
    echo json_encode(['status' => 'error', 'message' => 'Nomor permohonan kosong']); exit;
}

$raw_input = trim($_GET['no']);
$no_permohonan = preg_replace('/[^a-zA-Z0-9]/', '', $raw_input); 

if (strlen($no_permohonan) < 5) {
    echo json_encode(['status' => 'error', 'message' => 'Format nomor tidak valid']); exit;
}

if (file_exists('pelacak_akses.php')) {
    include_once 'pelacak_akses.php';
    catat_log_pencarian($conn, $no_permohonan);
}

// TAMBAHAN: Memanggil kolom 'tgl_permohonan' dari database
$query = "SELECT no_permohonan, nama_pemohon, tgl_lahir, status_saat_ini, tgl_permohonan FROM permohonan WHERE no_permohonan = ? LIMIT 1";
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "s", $no_permohonan);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if ($row = mysqli_fetch_assoc($result)) {
        
        // --- MESIN KALKULATOR BATAS 30 HARI ---
        $data_waktu = null;
        if (!empty($row['tgl_permohonan'])) {
            $tgl_wawancara = strtotime($row['tgl_permohonan']);
            $tgl_kadaluarsa = strtotime('+30 days', $tgl_wawancara);
            $hari_ini       = strtotime(date('Y-m-d'));
            
            // Hitung sisa hari
            $selisih_detik = $tgl_kadaluarsa - $hari_ini;
            $sisa_hari     = floor($selisih_detik / (60 * 60 * 24));
            
            $status_waktu = 'AMAN';
            if ($sisa_hari < 0) {
                $status_waktu = 'HANGUS'; // Sudah lewat 30 hari
            } elseif ($sisa_hari <= 5) {
                $status_waktu = 'KRITIS'; // Tinggal $\le$ 5 hari lagi
            }

            $data_waktu = [
                'tgl_batas_str' => date('d-m-Y', $tgl_kadaluarsa),
                'sisa_hari'     => $sisa_hari,
                'kondisi'       => $status_waktu
            ];
        }
        // ----------------------------------------

        $data_aman = [
            'no_permohonan'   => htmlspecialchars($row['no_permohonan'], ENT_QUOTES, 'UTF-8'),
            'nama_pemohon'    => htmlspecialchars($row['nama_pemohon'], ENT_QUOTES, 'UTF-8'),
            'tgl_lahir'       => htmlspecialchars($row['tgl_lahir'] ?? '', ENT_QUOTES, 'UTF-8'),
            'status_saat_ini' => htmlspecialchars($row['status_saat_ini'], ENT_QUOTES, 'UTF-8'),
            'info_tempo'      => $data_waktu // Dikirim ke frontend
        ];
        
        echo json_encode(['status' => 'success', 'data' => $data_aman]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Data tidak ditemukan']);
    }
    mysqli_stmt_close($stmt);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Gangguan pada server data']);
}
mysqli_close($conn);
?>