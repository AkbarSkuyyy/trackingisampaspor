<?php
session_start();
require_once 'koneksi.php';

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $cek = mysqli_query($conn, "SELECT * FROM users WHERE username='$username' AND password='$password'");
    if (mysqli_num_rows($cek) > 0) {
        $_SESSION['admin'] = true;
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - E-Tracking Imigrasi</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap');
        
        * { box-sizing: border-box; }
        body { 
            font-family: 'Outfit', sans-serif; 
            background-color: #f4f6f9; 
            display: flex; 
            justify-content: center; 
            align-items: center; 
            height: 100vh; 
            margin: 0; 
        }
        .login-wrapper { 
            background: #ffffff; 
            padding: 40px; 
            border-radius: 12px; 
            box-shadow: 0 10px 30px rgba(0,0,0,0.1); 
            width: 100%; 
            max-width: 420px; 
            border-top: 5px solid #003366; 
        }
        
        /* Menggunakan tata letak logo tunggal di sisi kiri yang bersih */
        .login-header { 
            display: flex; 
            align-items: center; 
            justify-content: flex-start; 
            gap: 18px; 
            margin-bottom: 35px; 
        }
        .login-header img { 
            height: 55px; 
            width: auto;
        }
        .login-header h2 { 
            margin: 0; 
            font-size: 1.35rem; 
            color: #003366; 
            font-weight: 800; 
            line-height: 1.25; 
        }
        .login-header p {
            margin: 2px 0 0 0;
            font-size: 0.8rem;
            color: #475569; /* Warna abu kebiruan profesional */
            font-weight: 500;
        }

        .form-group { margin-bottom: 22px; }
        .form-group label { 
            display: block; 
            margin-bottom: 8px; 
            color: #475569; 
            font-weight: 600; 
            font-size: 0.95rem; 
        }
        .form-group input { 
            width: 100%; 
            padding: 14px 16px; 
            border: 2px solid #e2e8f0; 
            border-radius: 8px; 
            font-family: 'Outfit', sans-serif; 
            font-size: 1rem; 
            color: #334155; 
            transition: all 0.3s ease; 
            background-color: #f8fafc;
        }
        .form-group input:focus { 
            border-color: #003366; 
            background-color: #ffffff;
            outline: none; 
            box-shadow: 0 0 0 3px rgba(0, 51, 102, 0.1);
        }
        
        .btn-login { 
            width: 100%; 
            padding: 16px; 
            background: #003366; 
            color: #ffffff; 
            border: none; 
            border-radius: 8px; 
            font-size: 1.05rem; 
            font-weight: 700; 
            font-family: 'Outfit', sans-serif; 
            cursor: pointer; 
            transition: all 0.3s ease; 
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
        }
        .btn-login:hover { 
            background: #f9c74f; 
            color: #003366; 
            transform: translateY(-2px);
        }
        
        .alert-error { 
            background: #fee2e2; 
            color: #ef4444; 
            padding: 12px; 
            border-radius: 8px; 
            font-size: 0.95rem; 
            font-weight: 500;
            margin-bottom: 25px; 
            text-align: center; 
            border-left: 4px solid #ef4444;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-header">
            <img src="assets/images/Logo Ditjen Imigrasi.png" alt="Logo Imigrasi" onerror="this.onerror=null; this.src='assets/images/logo-imigrasi.png';">
            <div>
                <h2>Admin Panel</h2>
                <p>E-Tracking Paspor</p>
            </div>
        </div>
        
        <?php if(isset($error)) { echo "<div class='alert-error'><i class='fas fa-exclamation-circle'></i> $error</div>"; } ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username Admin</label>
                <input type="text" name="username" placeholder="Masukkan username" required autocomplete="off">
            </div>
            <div class="form-group">
                <label>Kata Sandi</label>
                <input type="password" name="password" placeholder="Masukkan password" required>
            </div>
            <button type="submit" name="login" class="btn-login">
                Masuk Sistem <i class="fas fa-arrow-right"></i>
            </button>
        </form>
    </div>
</body>
</html>