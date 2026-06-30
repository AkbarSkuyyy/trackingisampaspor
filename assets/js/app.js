// ============================================================================
// APP.JS - MESIN LOGIKA E-TRACKING KANTOR IMIGRASI SAMPIT
// ============================================================================

// 1. Captcha
function buatCaptchaBaru() {
    const chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    let res = ""; for(let i=0; i<6; i++) res += chars[Math.floor(Math.random() * chars.length)]; return res;
}

const teksCaptcha = document.getElementById("layar_captcha"), inputUser = document.getElementById("input_kode_captcha");
const btnRefresh = document.getElementById("tombol_refresh"), btnSubmit = document.getElementById("tombol_cek");
let kodeSaatIni = "";

function siapkanForm() { kodeSaatIni = buatCaptchaBaru(); teksCaptcha.textContent = kodeSaatIni; inputUser.value = ""; validasiInput(); }
function validasiInput() { if(inputUser.value.toUpperCase() === kodeSaatIni && inputUser.value !== "") btnSubmit.removeAttribute("disabled"); else btnSubmit.setAttribute("disabled", "true"); }

window.addEventListener('DOMContentLoaded', siapkanForm);
btnRefresh.addEventListener("click", siapkanForm);
inputUser.addEventListener("keyup", validasiInput);

// 2. Fungsi Menutup Semua Modal
function tutupSemuaModal() {
    document.getElementById('modal_proses').classList.remove('aktif');
    document.getElementById('modal_selesai').classList.remove('aktif');
    document.getElementById('modal_gagal').classList.remove('aktif');
    siapkanForm();
}

// 3. Eksekusi Tampilan "PROSES" (Tanpa Tanggal Lahir)
function eksekusiModalProses(d, statusAman) {
    document.getElementById("p_res_nomor").innerText = d.nomor;
    document.getElementById("p_res_nama").innerText = d.nama;
    document.getElementById("p_res_status").innerText = statusAman;

    // Menampilkan Tanggal Input (Perbaikan Sinkronisasi)
    if (d.tgl_input && d.tgl_input.trim() !== "") {
        document.getElementById("p_res_tgl_input").innerText = d.tgl_input.split('-').reverse().join('-');
        document.getElementById("p_baris_tgl_input").style.display = "flex";
    } else {
        document.getElementById("p_baris_tgl_input").style.display = "none";
    }

    for(let i=1; i<=6; i++) document.getElementById('p_step_'+i).className = 'timeline-step';
    
    if (statusAman === "Permohonan Masuk") { document.getElementById('p_step_1').classList.add('active'); }
    else if (statusAman === "Wawancara & Foto Selesai") { document.getElementById('p_step_1').classList.add('completed'); document.getElementById('p_step_2').classList.add('active'); }
    else if (statusAman === "Menunggu Pembayaran") { document.getElementById('p_step_1').classList.add('completed'); document.getElementById('p_step_2').classList.add('completed'); document.getElementById('p_step_3').classList.add('active'); }
    else if (statusAman === "Alokasi") { document.getElementById('p_step_1').classList.add('completed'); document.getElementById('p_step_2').classList.add('completed'); document.getElementById('p_step_3').classList.add('completed'); document.getElementById('p_step_4').classList.add('active'); }
    else if (statusAman === "Paspor Sedang Dicetak") { document.getElementById('p_step_1').classList.add('completed'); document.getElementById('p_step_2').classList.add('completed'); document.getElementById('p_step_3').classList.add('completed'); document.getElementById('p_step_4').classList.add('completed'); document.getElementById('p_step_5').classList.add('active'); }

    document.getElementById('modal_proses').classList.add('aktif');
}

// 4. Eksekusi Tampilan "SELESAI" (+ Tanggal Lahir & Hitung Mundur 30 Hari)
function eksekusiModalSelesai(d, statusAman, tempo) {
    document.getElementById("s_res_nomor").innerText = d.nomor;
    document.getElementById("s_res_nama").innerText = d.nama;
    document.getElementById("s_res_status").innerText = statusAman;
    
    if (d.tgl && d.tgl.trim() !== "" && d.tgl !== "0000-00-00") {
        document.getElementById("s_res_tgl").innerText = d.tgl.split('-').reverse().join('-');
    } else {
        document.getElementById("s_res_tgl").innerText = "Selesai";
    }

    // Menampilkan Tanggal Input (Perbaikan Sinkronisasi)
    if (d.tgl_input && d.tgl_input.trim() !== "") {
        document.getElementById("s_res_tgl_input").innerText = d.tgl_input.split('-').reverse().join('-');
        document.getElementById("s_baris_tgl_input").style.display = "flex";
    } else {
        document.getElementById("s_baris_tgl_input").style.display = "none";
    }

    const kotakBatas = document.getElementById("baris_batas_waktu");
    const teksBatas  = document.getElementById("s_res_batas");

    if (tempo) {
        kotakBatas.style.display = "flex";
        if (tempo.kondisi === 'HANGUS') {
            teksBatas.innerHTML = `<span style="color:#ef4444; font-weight:800;">${tempo.tgl_batas_str} <br><small style="font-size:0.7rem; letter-spacing:0.5px;">(LEWAT BATAS / PASPOR BATAL)</small></span>`;
        } else if (tempo.kondisi === 'KRITIS') {
            teksBatas.innerHTML = `<span style="color:#d97706; font-weight:700;">${tempo.tgl_batas_str} <small style="color:#ef4444;">(Sisa ${tempo.sisa_hari} hari!)</small></span>`;
        } else {
            teksBatas.innerHTML = `<span style="color:#0f172a; font-weight:600;">${tempo.tgl_batas_str}</span>`;
        }
    } else {
        kotakBatas.style.display = "none";
    }

    document.getElementById('modal_selesai').classList.add('aktif');
}

// 5. Eksekusi Tampilan "GAGAL"
function eksekusiModalGagal(judul, pesan) {
    document.getElementById("err_title").innerText = judul;
    document.getElementById("err_message").innerHTML = pesan;
    document.getElementById('modal_gagal').classList.add('aktif');
}

// 6. Request Fetch API ke Server
document.getElementById("formCekImigrasi").addEventListener("submit", function(e) {
    e.preventDefault(); 
    const noInput = document.getElementById("no_permohonan").value.replace(/\s+/g, '');
    btnSubmit.innerText = "Mencari..."; btnSubmit.setAttribute("disabled", "true");
    
    fetch('cek_data.php?no=' + noInput)
        .then(r => r.json())
        .then(res => {
            btnSubmit.innerText = "Cek Status Sekarang"; btnSubmit.removeAttribute("disabled");
            
            if (res.status === 'success') {
                const statusAman = res.data.status_saat_ini.replace(/&amp;/g, '&');
                
                if (statusAman === "Paspor Selesai / Bisa Diambil") {
                    // PERBAIKAN: Melempar variabel tgl_input ke eksekusiModalSelesai
                    eksekusiModalSelesai({ nomor: res.data.no_permohonan, nama: res.data.nama_pemohon, tgl: res.data.tgl_lahir, tgl_input: res.data.tanggal_input }, statusAman, res.data.info_tempo);
                } else {
                    // PERBAIKAN: Melempar variabel tgl_input ke eksekusiModalProses
                    eksekusiModalProses({ nomor: res.data.no_permohonan, nama: res.data.nama_pemohon, tgl_input: res.data.tanggal_input }, statusAman);
                }
            } else {
                eksekusiModalGagal("Tidak Ditemukan", "Nomor permohonan <strong>" + noInput + "</strong> tidak terdaftar di dalam sistem.");
            }
        })
        .catch(() => {
            btnSubmit.innerText = "Cek Status Sekarang"; btnSubmit.removeAttribute("disabled");
            eksekusiModalGagal("Gangguan Server", "Terjadi gangguan koneksi saat mengambil data ke server pusat.");
        });
});

// 7. Toggle Menu Sidebar Kiri & Submenu Hover
const navToggle = document.getElementById('navToggle'), sidebarMenu = document.getElementById('sidebarMenuFront'), sidebarOverlay = document.getElementById('sidebarOverlay');
function tSlL() { sidebarMenu.classList.toggle('aktif'); sidebarOverlay.classList.toggle('aktif'); }
if(navToggle) navToggle.addEventListener('click', tSlL); 
const closeSidebarBtn = document.getElementById('closeSidebarBtn');
if(closeSidebarBtn) closeSidebarBtn.addEventListener('click', tSlL); 
if(sidebarOverlay) sidebarOverlay.addEventListener('click', tSlL);

const accordionTrigger = document.getElementById('accordion-trigger');
if(accordionTrigger) accordionTrigger.addEventListener('click', () => document.getElementById('panel-info').classList.toggle('buka'));

const submenuItems = document.querySelectorAll('.has-submenu');
submenuItems.forEach(item => {
    const linkElement = item.querySelector('a');
    if (linkElement) {
        linkElement.addEventListener('click', function(e) {
            if (window.innerWidth <= 992) {
                e.preventDefault(); e.stopPropagation();
                submenuItems.forEach(other => { if (other !== item) other.classList.remove('open'); });
                item.classList.toggle('open');
            }
        });
    }
});

// 8. Anti-Inspect Dasar (Opsional)
document.addEventListener('contextmenu', event => event.preventDefault()); 
document.onkeydown = function(e) {
    if(e.keyCode == 123) return false;
    if(e.ctrlKey && e.shiftKey && (e.keyCode == 'I'.charCodeAt(0) || e.keyCode == 'C'.charCodeAt(0) || e.keyCode == 'J'.charCodeAt(0))) return false;
    if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)) return false;
};