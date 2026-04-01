<?php 
// 1. Proteksi Halaman Admin
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// 2. Koneksi Database
include '../api/db_config.php'; 
if (!$conn) { die("Koneksi gagal! Periksa api/db_config.php"); }
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Users - Vanasaga Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0; padding: 0; background: #030303; 
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
            min-height: 100vh; position: fixed; transition: all 0.3s; z-index: 1050;
        }

        .main-content { padding: 20px; transition: all 0.3s; }

        @media (min-width: 992px) {
            .main-content { margin-left: 250px; padding: 40px; }
            .mobile-header { display: none !important; }
        }

        @media (max-width: 991.98px) {
            .sidebar { left: -250px; }
            .sidebar.show { left: 0; }
            .main-content { margin-left: 0; padding-top: 80px; }
            .mobile-header { 
                display: flex !important; position: fixed; top: 0; width: 100%; 
                z-index: 1040; padding: 15px 20px; background: rgba(10, 10, 10, 0.9); 
                backdrop-filter: blur(10px); border-bottom: 1px solid rgba(168, 85, 247, 0.3); 
            }
        }

        .card-custom {
            background: rgba(15, 15, 15, 0.5) !important;
            border: 1px solid rgba(168, 85, 247, 0.4) !important;
            backdrop-filter: blur(15px); border-radius: 24px; padding: 20px;
        }

        .table, .table tr, .table td, .table th {
            background-color: transparent !important; color: #ffffff !important;
            border-color: rgba(168, 85, 247, 0.1) !important;
        }

        .table thead th {
            background: rgba(168, 85, 247, 0.2) !important; color: #a855f7 !important;
            border-bottom: 2px solid rgba(168, 85, 247, 0.5) !important; font-weight: 800;
        }

        .nav-link { color: #ffffff !important; border-radius: 12px; margin: 5px 15px; padding: 12px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .nav-link:hover, .nav-link.active { background: rgba(168, 85, 247, 0.3) !important; color: #a855f7 !important; }

        .info-icon {
            color: #ffffff;
            width: 20px;
            text-align: center;
            margin-right: 8px;
            opacity: 0.9;
        }

        .btn-edit { background: linear-gradient(135deg, #a855f7 0%, #6d28d9 100%) !important; border: none; color: white !important; font-weight: 700; padding: 6px 12px; border-radius: 8px; transition: 0.3s; }
        .btn-edit:hover { transform: scale(1.05); box-shadow: 0 0 15px rgba(168, 85, 247, 0.4); }
        .btn-delete { background: rgba(239, 68, 68, 0.2) !important; border: 1px solid #ef4444; color: #ff4d4d !important; font-weight: 700; padding: 6px 12px; border-radius: 8px; transition: 0.3s; }
        .btn-delete:hover { background: rgba(239, 68, 68, 0.4) !important; }
    </style>
</head>
<body>

<div class="mobile-header d-flex d-lg-none justify-content-between align-items-center">
    <h4 class="mb-0 fw-bold" style="color: #a855f7;">VANASAGA</h4>
    <button class="btn text-white fs-3" onclick="toggleSidebar()"><i class="fa fa-bars"></i></button>
</div>

<div class="sidebar d-flex flex-column shadow" id="adminSidebar">
    <div class="p-4 text-center d-none d-lg-block">
        <h4 class="mb-0 fw-bold" style="color: #a855f7; text-shadow: 0 0 10px rgba(168, 85, 247, 0.3);">VANASAGA</h4>
        <small style="color: #ffffff; letter-spacing: 2px; font-weight: 600;">ADMIN PANEL</small>
    </div>
    <div class="mt-3 mt-lg-0 flex-grow-1">
        <a href="dashboard.php" class="nav-link"><i class="fa fa-home me-2"></i> Dashboard</a>
        <a href="users.php" class="nav-link active"><i class="fa fa-users me-2"></i> Kelola Users</a>
        <a href="transaksi.php" class="nav-link"><i class="fa fa-exchange-alt me-2"></i> Transaksi</a>
        <a href="ranks.php" class="nav-link"><i class="fa fa-crown me-2"></i> Ranks List</a>
        <a href="report.php" class="nav-link"><i class="fa fa-exclamation-triangle me-2"></i> Laporan Player</a>
    </div>
    <div class="pb-4">
        <hr style="border-color: rgba(168, 85, 247, 0.2); margin: 10px 20px;">
        <a href="logout.php" class="nav-link text-danger"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="fw-bold mb-1" style="color: #ffffff;">Kelola <span style="color: #a855f7;">Users</span></h2>
                <p style="color: #ffffff; font-weight: 400;">Manajemen portal dimensi Vanasaga ID.</p>
            </div>
            <a href="dashboard.php" class="btn btn-sm" style="border: 2px solid #a855f7; color: #ffffff; background: rgba(168, 85, 247, 0.1); border-radius: 10px; padding: 8px 15px; font-weight: 600;">
                <i class="fa fa-arrow-left me-2"></i> Dashboard
            </a>
        </div>

        <div class="card-custom shadow-lg">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th class="d-none d-md-table-cell">Email</th>
                            <th>Gamertag</th>
                            <th class="d-none d-md-table-cell">Platform</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $sql = mysqli_query($conn, "SELECT * FROM users");
                        if($sql && mysqli_num_rows($sql) > 0) {
                            while($d = mysqli_fetch_array($sql)){
                                $u_id       = $d['id_user'];
                                $u_name     = $d['nama'];
                                $u_mail     = $d['email'];
                                $u_nick     = !empty($d['nick_minecraft']) ? $d['nick_minecraft'] : '---';
                                $u_platform = !empty($d['platform']) ? strtoupper($d['platform']) : '---';
                        ?>
                        <tr>
                            <td class="fw-bold" style="color: #ffffff;">
                                <i class="fa fa-user info-icon"></i> <?= htmlspecialchars($u_name) ?>
                            </td>
                            <td class="d-none d-md-table-cell" style="color: #ffffff; font-size: 0.9rem;">
                                <i class="fa fa-envelope info-icon"></i> <?= htmlspecialchars($u_mail) ?>
                            </td>
                            <td style="color: #ffffff;">
                                <span class="badge bg-white bg-opacity-10 text-white border border-white border-opacity-25 px-2 py-1">
                                   <i class="fa fa-gamepad me-1"></i> <?= htmlspecialchars($u_nick) ?>
                                </span>
                            </td>
                            <td class="d-none d-md-table-cell" style="color: #ffffff;">
                                <span class="badge" style="background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(255, 255, 255, 0.2); color: #ffffff;">
                                    <i class="fa fa-desktop me-1"></i> <?= htmlspecialchars($u_platform) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="edit_user.php?id=<?= $u_id ?>" class="btn-edit btn-sm" title="Edit Data"><i class="fa fa-edit"></i></a>
                                    <a href="hapus_user.php?id=<?= $u_id ?>" class="btn-delete btn-sm" onclick="return confirm('Hapus penduduk ini?')" title="Hapus"><i class="fa fa-trash"></i></a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5' style='color: #ffffff;'>Tidak ada data penduduk.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() {
        document.getElementById('adminSidebar').classList.toggle('show');
    }
</script>

</body>
</html>