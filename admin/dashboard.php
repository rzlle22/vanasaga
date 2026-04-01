<?php 
// 1. Proteksi Halaman: Hanya admin yang sudah login bisa masuk
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

// 2. Koneksi Database
include '../api/db_config.php'; 
if (!$conn) { die("Koneksi gagal!"); }

// Hitung Statistik Utama
$res_u = mysqli_query($conn, "SELECT COUNT(*) as total FROM users");
$total_users = mysqli_fetch_assoc($res_u)['total'] ?? 0;

$res_t = mysqli_query($conn, "SELECT COUNT(*) as total FROM transaksi");
$total_transaksi = mysqli_fetch_assoc($res_t)['total'] ?? 0;

// Hitung Total Pendapatan (Hanya yang statusnya 'paid')
$res_p = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi WHERE status = 'paid'");
$total_pendapatan = mysqli_fetch_assoc($res_p)['total'] ?? 0;

// Hitung Pendapatan Hari Ini
$tgl_sekarang = date('Y-m-d');
$res_h = mysqli_query($conn, "SELECT SUM(total_harga) as total FROM transaksi WHERE status = 'paid' AND DATE(tanggal) = '$tgl_sekarang'");
$income_today = mysqli_fetch_assoc($res_h)['total'] ?? 0;

// Hitung Total Laporan (Reports)
$res_r = mysqli_query($conn, "SELECT COUNT(*) as total FROM reports");
$total_reports = mysqli_fetch_assoc($res_r)['total'] ?? 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Vanasaga Admin</title>
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

        /* Sidebar Alchemy Style */
        .sidebar { 
            width: 250px; 
            background: rgba(10, 10, 10, 0.95) !important; 
            backdrop-filter: blur(20px); 
            border-right: 1px solid rgba(168, 85, 247, 0.3); 
            min-height: 100vh; 
            position: fixed; 
            transition: all 0.3s; 
            z-index: 1050; 
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
                z-index: 1040; padding: 15px 20px; 
                background: rgba(10, 10, 10, 0.9); 
                backdrop-filter: blur(10px); 
                border-bottom: 1px solid rgba(168, 85, 247, 0.3); 
            }
        }

        /* Card Alchemy Style */
        .card-custom {
            background: rgba(15, 15, 15, 0.5) !important; 
            border: 1px solid rgba(168, 85, 247, 0.4) !important; 
            backdrop-filter: blur(15px); 
            border-radius: 24px; 
            padding: 25px;
            transition: 0.3s ease;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        .card-custom:hover {
            transform: translateY(-5px); 
            border-color: #a855f7 !important;
            background: rgba(168, 85, 247, 0.05) !important;
            box-shadow: 0 0 20px rgba(168, 85, 247, 0.2);
        }

        /* Navigation Alchemy Style */
        .nav-link { 
            color: #ffffff !important; border-radius: 12px; margin: 5px 15px; 
            padding: 12px; text-decoration: none; font-weight: 600;
            transition: 0.3s;
        }

        .nav-link.active, .nav-link:hover { 
            background: rgba(168, 85, 247, 0.3) !important; 
            color: #a855f7 !important; 
        }

        .nav-link.logout-link:hover {
            background: rgba(255, 68, 68, 0.1) !important;
            color: #ff4444 !important;
        }

        .stat-icon {
            width: 50px; height: 50px; background: rgba(168, 85, 247, 0.15);
            border-radius: 15px; display: flex; align-items: center;
            justify-content: center; color: #a855f7; font-size: 1.5rem;
        }
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
        <a href="dashboard.php" class="nav-link active"><i class="fa fa-home me-2"></i> Dashboard</a>
        <a href="users.php" class="nav-link"><i class="fa fa-users me-2"></i> Kelola Users</a>
        <a href="transaksi.php" class="nav-link"><i class="fa fa-exchange-alt me-2"></i> Transaksi</a>
        <a href="ranks.php" class="nav-link"><i class="fa fa-crown me-2"></i> Ranks List</a>
        <a href="report.php" class="nav-link"><i class="fa fa-exclamation-triangle me-2"></i> Laporan Player</a>
    </div>
    <div class="pb-4">
        <hr style="border-color: rgba(168, 85, 247, 0.2); margin: 10px 20px;">
        <a href="logout.php" class="nav-link logout-link"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="mb-5">
            <h2 class="fw-bold mb-1">Dashboard <span style="color: #a855f7;">Overview</span></h2>
            <p style="opacity: 0.8;">Statistik pendapatan dan populasi Vanasaga ID.</p>
        </div>
        
        <div class="row g-4">
            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom d-flex align-items-center justify-content-between h-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1" style="color: #22c55e;">Total Revenue</p>
                        <h3 class="fw-bold mb-0">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></h3>
                        <small class="text-white-50">Saldo sukses masuk</small>
                    </div>
                    <div class="stat-icon" style="color: #22c55e; background: rgba(34, 197, 94, 0.1);">
                        <i class="fa fa-wallet"></i>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom d-flex align-items-center justify-content-between h-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1" style="color: #00d4ff;">Income Today</p>
                        <h3 class="fw-bold mb-0">Rp <?= number_format($income_today, 0, ',', '.') ?></h3>
                        <small class="text-white-50"><?= date('d M Y') ?></small>
                    </div>
                    <div class="stat-icon" style="color: #00d4ff; background: rgba(0, 212, 255, 0.1);">
                        <i class="fa fa-chart-line"></i>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-4">
                <div class="card-custom d-flex align-items-center justify-content-between h-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1" style="color: #a855f7;">Total Users</p>
                        <h3 class="fw-bold mb-0"><?= number_format($total_users, 0, ',', '.') ?></h3>
                        <small class="text-white-50">Seluruh pemain</small>
                    </div>
                    <div class="stat-icon">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-6">
                <div class="card-custom d-flex align-items-center justify-content-between h-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1" style="color: #ffcc00;">Total Orders</p>
                        <h3 class="fw-bold mb-0"><?= number_format($total_transaksi, 0, ',', '.') ?></h3>
                        <small class="text-white-50">Seluruh pesanan</small>
                    </div>
                    <div class="stat-icon" style="color: #ffcc00; background: rgba(255, 204, 0, 0.1);">
                        <i class="fa fa-shopping-cart"></i>
                    </div>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-6" style="cursor: pointer;" onclick="location.href='report.php'">
                <div class="card-custom d-flex align-items-center justify-content-between h-100">
                    <div>
                        <p class="small text-uppercase fw-bold mb-1" style="color: #ff4444;">Laporan Player</p>
                        <h3 class="fw-bold mb-0"><?= number_format($total_reports, 0, ',', '.') ?> Aduan</h3>
                        <small class="text-white-50">Klik untuk melihat detail</small>
                    </div>
                    <div class="stat-icon" style="color: #ff4444; background: rgba(255, 68, 68, 0.1);">
                        <i class="fa fa-exclamation-triangle"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleSidebar() { document.getElementById('adminSidebar').classList.toggle('show'); }
</script>

</body>
</html>