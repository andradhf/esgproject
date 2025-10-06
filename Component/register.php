<?php
include "../config/config.php"; // koneksi database
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName  = trim($_POST['first_name']);
    $lastName   = trim($_POST['last_name']);
    $email      = trim($_POST['email']);
    $password   = $_POST['password'];
    $repeatPass = $_POST['repeat_password'];

    // Validasi sederhana
    if ($password !== $repeatPass) {
        echo "<script>alert('Password dan Repeat Password tidak sama!'); window.history.back();</script>";
        exit;
    }

    // Cek apakah email sudah ada
    $check = $conn->prepare("SELECT id FROM user_accounts WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    $check->store_result();
    if ($check->num_rows > 0) {
        echo "<script>alert('Email sudah digunakan! Silakan pakai email lain.'); window.history.back();</script>";
        exit;
    }

    if (!empty($firstName) && !empty($email) && !empty($password)) {
        $conn->begin_transaction();

        try {
            // 1. Simpan ke umkm_accounts
            $nama_umkm    = $firstName . " " . $lastName;
            $nama_pemilik = $firstName . " " . $lastName;
            $no_telepon   = "";
            $alamat       = "";

            $stmt1 = $conn->prepare("INSERT INTO umkm_accounts (nama_umkm, nama_pemilik, email, no_telepon, alamat) VALUES (?, ?, ?, ?, ?)");
            $stmt1->bind_param("sssss", $nama_umkm, $nama_pemilik, $email, $no_telepon, $alamat);
            $stmt1->execute();
            $umkm_id = $stmt1->insert_id;

            // 2. Generate username unik
            do {
                $username = strtolower($firstName) . rand(100, 999);
                $checkUser = $conn->prepare("SELECT id FROM user_accounts WHERE username = ?");
                $checkUser->bind_param("s", $username);
                $checkUser->execute();
                $checkUser->store_result();
            } while ($checkUser->num_rows > 0);

            // 3. Simpan ke user_accounts
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $role = "user";

            $stmt2 = $conn->prepare("INSERT INTO user_accounts (username, password, email, role) VALUES (?, ?, ?, ?)");
            $stmt2->bind_param("ssss", $username, $hashedPassword, $email, $role);
            $stmt2->execute();

            $conn->commit();

            echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location.href='login.php';</script>";
            exit;
        } catch (Exception $e) {
            $conn->rollback();
            echo "<script>alert('Terjadi kesalahan saat registrasi.'); window.history.back();</script>";
            exit;
        }
    } else {
        echo "<script>alert('Data tidak boleh kosong!'); window.history.back();</script>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Monitoring ESG - Register</title>

    <!-- Font Awesome -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">

    <!-- Google Fonts Nunito -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,300,400,600,700,800,900" rel="stylesheet">

    <!-- Custom styles (SB Admin 2) -->
    <link href="../css/sb-admin-2.min.css" rel="stylesheet">

    <style>
        body {
            background: linear-gradient(135deg, #3fab0e, #0096c7);
            font-family: 'Nunito', sans-serif;
        }

        .card {
            border-radius: 20px;
        }

        .card-body {
            background: #ffffff;
            border-radius: 20px;
        }

        .btn-user {
            border-radius: 30px;
            font-weight: 600;
            background: linear-gradient(90deg, #0096c7, #38b000);
            border: none;
            color: #000;
        }

        .btn-user:hover {
            background: linear-gradient(90deg, #38b000, #0096c7);
            transform: scale(1.02);
            transition: all 0.3s ease-in-out;
            color: #000;
        }

        .footer-text {
            text-align: center;
            margin-top: -10px;
            color: white;
            font-size: 22px;
            font-weight: 600;
        }

        /* Kolom gambar di register */
        .bg-register-image {
            background: url('../img/esg_bg.jpg') center center;
            background-size: cover;
            border-radius: 10px 0 0 10px;
        }

          @media (max-width: 768px) {
            .card {
                margin: 5px; 
            }
            .footer-text {
                font-size: 16px; 
                margin-top: 10px;
                padding: 0 10px;
            }
            
            img[alt="Logo UHAMKA"] { height: 60px !important; }
            img[alt="Logo ESG"] { height: 50px !important; }
        }
    </style>
</head>

<body>
     <!-- Logo -->
    <div style="position:absolute; top:20px; left:20px; z-index:1000;">
        <img src="../img/logo_uhamka.png" alt="Logo UHAMKA" style="height:100px;">
    </div>
    <div style="position:absolute; top:20px; right:20px; z-index:1000;">
        <img src="../img/logo_kemendikbud.png" alt="Logo ESG" style="height:70px;">
    </div>

<div class="container" style="margin-top: 120px;">
    <div class="card o-hidden border-0 shadow-lg my-5">
        <div class="card-body p-0">
            <div class="row">
                <!-- AREA GAMBAR -->
                <div class="col-lg-5 d-none d-lg-block bg-register-image"></div>

                <!-- FORM REGISTER -->
                <div class="col-lg-7" style="padding-top: 50px; border-radius: 50px">
                    <div class="p-5">
                        <div class="text-center">
                            <h1 class="h4 text-gray-900 mb-4">Create an Account!</h1>
                        </div>
                        <form class="user" action="register.php" method="post">
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="text" name="first_name" class="form-control form-control-user"
                                           placeholder="First Name" required>
                                </div>
                                <div class="col-sm-6">
                                    <input type="text" name="last_name" class="form-control form-control-user"
                                           placeholder="Last Name">
                                </div>
                            </div>
                            <div class="form-group">
                                <input type="email" name="email" class="form-control form-control-user"
                                       placeholder="Email Address" required>
                            </div>
                            <div class="form-group row">
                                <div class="col-sm-6 mb-3 mb-sm-0">
                                    <input type="password" name="password" class="form-control form-control-user"
                                           placeholder="Password" required>
                                </div>
                                <div class="col-sm-6">
                                    <input type="password" name="repeat_password" class="form-control form-control-user"
                                           placeholder="Repeat Password" required>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-user btn-block">Register Account</button>
                        </form>
                        <hr>
                        <div class="text-center">
                            <a class="small" href="login.php">Already have an account? Login!</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
       <div class="footer-text">
        Membangun UMKM Berkelanjutan dengan Prinsip ESG Syariah
    </div>
</div>

<script src="../vendor/jquery/jquery.min.js"></script>
<script src="../vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="../vendor/jquery-easing/jquery.easing.min.js"></script>
<script src="../js/sb-admin-2.min.js"></script>

</body>
</html>
