<?php
include "../config/config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $role     = $_POST['role'];
    $email    = $_POST['email'];
    $password = $_POST['password'];

    // Cek akun user di database
    $stmt = $conn->prepare("SELECT * FROM user_accounts WHERE email = ? AND role = ?");
    $stmt->bind_param("ss", $email, $role);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();

        // Verifikasi password (cek hash atau plaintext)
        $validPassword = false;
        if (password_verify($password, $user['password'])) {
            $validPassword = true; // jika password di-hash
        } elseif ($password === $user['password']) {
            $validPassword = true; // jika masih plaintext
        }

        if ($validPassword) {
            // Simpan ke session
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['email']    = $user['email'];
            $_SESSION['role']     = $user['role'];

            // Jika role = user → masuk index.php
            if ($user['role'] === "user") {
                // ambil umkm_id kalau ada
                $stmt_umkm = $conn->prepare("SELECT id FROM umkm_accounts WHERE email = ?");
                $stmt_umkm->bind_param("s", $user['email']);
                $stmt_umkm->execute();
                $umkm = $stmt_umkm->get_result()->fetch_assoc();
                $_SESSION['umkm_id'] = $umkm['id'] ?? null;

                header("Location: index.php");
                exit;
            }

            // Jika role = admin → admin_dashboard.php
            if ($user['role'] === "admin") {
                header("Location: admin_dashboard.php");
                exit;
            }

        } else {
            echo "<script>alert('Password salah!'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Akun tidak ditemukan!'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Monitoring ESG - Login</title>
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #3fab0e, #0096c7);
            font-family: 'Nunito', sans-serif;
        }
        .card { border-radius: 20px; }
        .card-body { background: #ffffff; border-radius: 20px; }
        .btn-user { border-radius: 30px; font-weight: 600;
            background: linear-gradient(90deg, #0096c7, #38b000);
            border: none; color: #000; }
        .btn-user:hover { background: linear-gradient(90deg, #38b000, #0096c7);
            transform: scale(1.02); transition: all 0.3s ease-in-out; color: #000; }
        .footer-text { text-align: center; margin-top: -10px; color: white; font-size: 22px; font-weight: 600; }
    </style>
</head>

<body>
    <!-- Logo -->
    <div style="position:absolute; top:20px; left:20px; z-index:1000;">
        <img src="img/logo_uhamka.png" alt="Logo UHAMKA" style="height:100px;">
    </div>
    <div style="position:absolute; top:20px; right:20px; z-index:1000;">
        <img src="img/logo_kemendikbud.png" alt="Logo ESG" style="height:70px;">
    </div>

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-10 col-lg-12 col-md-9">
                <div class="card o-hidden border-0 shadow-lg my-5">
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-lg-6 d-none d-lg-block bg-login-image"
                                style="background:url('img/esg_bg.jpg'); background-size:cover; border-radius:10px 0 0 10px;">
                            </div>
                            <div class="col-lg-6">
                                <div class="p-5">
                                    <div class="text-center mb-4">
                                        <h1 class="h4" style="font-weight:700;">
                                            Selamat Datang di Website ESG Syariah UMKM
                                        </h1>
                                    </div>

                                    <!-- FORM LOGIN -->
                                    <form class="user" method="POST" action="login.php">
                                        <div class="form-group">
                                            <select class="form-control" name="role" required>
                                                <option value="">-- Masuk Sebagai --</option>
                                                <option value="user">User UMKM</option>
                                                <option value="admin">Admin</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <input type="email" name="email" class="form-control form-control-user"
                                                placeholder="Enter Email Address..." required>
                                        </div>
                                        <div class="form-group">
                                            <input type="password" name="password" class="form-control form-control-user"
                                                placeholder="Password" required>
                                        </div>
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox small">
                                                <input type="checkbox" class="custom-control-input" id="customCheck">
                                                <label class="custom-control-label" for="customCheck">Remember Me</label>
                                            </div>
                                        </div>
                                        <button type="submit" class="btn btn-user btn-block">Login</button>
                                    </form>

                                    <!-- Link ke register -->
                                    <div class="text-center mt-3">
                                        <a class="small" href="register.php">Belum punya akun? Daftar di sini</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="footer-text">
        Membangun UMKM Berkelanjutan dengan Prinsip ESG Syariah
    </div>
</body>
</html>
