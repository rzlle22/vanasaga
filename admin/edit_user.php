<?php 
include '../api/db_config.php'; 

if (!$conn) {
    die("Koneksi gagal! Periksa api/db_config.php");
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header("Location: users.php");
    exit();
}

$query = mysqli_query($conn, "SELECT * FROM users WHERE id_user = '$id'");
$data = mysqli_fetch_assoc($query);

if (!$data) {
    echo "<script>alert('Data tidak ditemukan!'); window.location='users.php';</script>";
    exit();
}

$u_name     = $data['nama']; 
$u_mail     = $data['email'];
$u_gamer    = $data['nick_minecraft'] ?? ''; 
$u_platform = $data['platform'] ?? ''; 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User - Vanasaga Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0; background: #030303; 
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(168, 85, 247, 0.2) 0%, transparent 40%),
                radial-gradient(circle at 90% 80%, rgba(126, 34, 206, 0.15) 0%, transparent 40%),
                radial-gradient(circle at 50% 50%, rgba(76, 29, 149, 0.2) 0%, transparent 50%);
            background-size: cover; color: #ffffff !important;
            font-family: 'Plus Jakarta Sans', sans-serif; min-height: 100vh;
        }

        .sidebar {
            width: 250px; background: rgba(10, 10, 10, 0.95) !important;
            backdrop-filter: blur(20px); border-right: 1px solid rgba(168, 85, 247, 0.3);
            min-height: 100vh; position: fixed; z-index: 1050;
        }

        .main-content { padding: 20px; }

        @media (min-width: 992px) {
            .main-content { margin-left: 250px; padding: 40px; }
            .mobile-header { display: none !important; }
        }

        @media (max-width: 991.98px) {
            .sidebar { left: -250px; transition: 0.3s; }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0; padding-top: 80px; }
            .mobile-header { display: flex !important; position: fixed; top: 0; width: 100%; z-index: 1040; padding: 15px 20px; background: rgba(10, 10, 10, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(168, 85, 247, 0.3); }
        }

        .card-custom {
            background: rgba(15, 15, 15, 0.5) !important;
            border: 1px solid rgba(168, 85, 247, 0.4) !important;
            backdrop-filter: blur(15px); border-radius: 24px; padding: 30px;
            max-width: 700px; margin: auto;
        }

        .form-label { color: #a855f7 !important; font-weight: 700; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 1px; }

        /* FIX DROPDOWN STYLE */
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05) !important;
            border: 1px solid rgba(168, 85, 247, 0.2) !important;
            color: #ffffff !important; border-radius: 12px; padding: 10px 15px;
        }

        /* Memaksa elemen option untuk berwarna gelap */
        .form-select option {
            background-color: #1a1a1a !important; /* Warna background gelap */
            color: #ffffff !important; /* Teks putih */
        }

        .form-control:focus, .form-select:focus { 
            border-color: #a855f7 !important; 
            box-shadow: 0 0 15px rgba(168, 85, 247, 0.2);
            outline: none;
        }

        .btn-update { 
            background: linear-gradient(135deg, #a855f7 0%, #6d28d9 100%) !important; 
            border: none; color: white !important; font-weight: 800; padding: 14px; border-radius: 15px; width: 100%; margin-top: 20px;
        }

        .nav-link { color: #ffffff !important; border-radius: 12px; margin: 5px 15px; padding: 12px; text-decoration: none; font-weight: 600; }
        .nav-link.active { background: rgba(168, 85, 247, 0.3) !important; color: #a855f7 !important; }
        .section-title { border-left: 4px solid #a855f7; padding-left: 10px; margin-bottom: 20px; font-weight: 800; font-size: 1.1rem; }
    </style>
</head>
<body>

<div class="mobile-header d-flex d-lg-none justify-content-between align-items-center">
    <h4 class="mb-0 fw-bold" style="color: #a855f7;">VANASAGA</h4>
    <button class="btn text-white fs-3" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
</div>

<div class="sidebar d-flex flex-column shadow" id="adminSidebar">
    <div class="p-4 text-center d-none d-lg-block">
        <h4 class="mb-0 fw-bold" style="color: #a855f7;">VANASAGA</h4>
        <small style="color: #ffffff; letter-spacing: 2px;">ADMIN PANEL</small>
    </div>
    <div class="mt-3 mt-lg-0 flex-grow-1">
        <a href="index.php" class="nav-link"><i class="fa fa-home me-2"></i> Dashboard</a>
        <a href="users.php" class="nav-link active"><i class="fa fa-users me-2"></i> Kelola Users</a>
        <a href="transaksi.php" class="nav-link"><i class="fa fa-exchange-alt me-2"></i> Transaksi</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="card-custom shadow-lg">
            <form action="proses_edit_user.php" method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">

                <div class="section-title">INFORMASI DASAR</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control" value="<?= $u_name ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control" value="<?= $u_mail ?>" required>
                    </div>
                </div>

                <div class="section-title mt-3">GAMING PROFILE</div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nick Minecraft (IGN)</label>
                        <input type="text" name="nick_minecraft" class="form-control" value="<?= $u_gamer ?>" placeholder="Contoh: Player_Vanasaga">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Platform</label>
                        <select name="platform" class="form-select">
                            <option value="java" <?= $u_platform == 'java' ? 'selected' : '' ?>>Java Edition</option>
                            <option value="bedrock" <?= $u_platform == 'bedrock' ? 'selected' : '' ?>>Bedrock Edition</option>
                        </select>
                    </div>
                </div>

                <div class="section-title mt-3">KEAMANAN (RESET SANDI)</div>
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin ganti sandi">
                    <small class="text-white-50">*Reset sandi akan langsung menimpa password lama user.</small>
                </div>

                <button type="submit" class="btn btn-update">
                    <i class="fa fa-sync-alt me-2"></i> UPDATE DATA & PROFILE
                </button>

                <div class="text-center mt-3">
                    <a href="users.php" style="color: #a855f7; text-decoration: none; font-size: 0.8rem;">
                        <i class="fa fa-arrow-left me-1"></i> Kembali ke List
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() { document.getElementById('adminSidebar').classList.toggle('show'); }
</script>

</body>
</html>