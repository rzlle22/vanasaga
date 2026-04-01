<?php 
// 1. Proteksi Halaman Admin
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit;
}

include '../api/db_config.php'; 

if (!$conn) {
    die("Koneksi gagal! Periksa api/db_config.php");
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Transaksi - Vanasaga Admin</title>
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

        /* Table & Card Alchemy Style */
        .card-custom { 
            background: rgba(15, 15, 15, 0.5) !important; 
            border: 1px solid rgba(168, 85, 247, 0.4) !important; 
            backdrop-filter: blur(15px); 
            border-radius: 24px; 
            padding: 20px; 
        }

        .table, .table tr, .table td, .table th { 
            background-color: transparent !important; 
            color: #ffffff !important; 
            border-color: rgba(168, 85, 247, 0.1) !important; 
        }

        .table thead th { 
            background: rgba(168, 85, 247, 0.2) !important; 
            color: #a855f7 !important; 
            border-bottom: 2px solid rgba(168, 85, 247, 0.5) !important; 
            font-weight: 800; 
            text-transform: uppercase;
            font-size: 0.8rem;
        }

        /* Nav Links */
        .nav-link { 
            color: #ffffff !important; 
            border-radius: 12px; margin: 5px 15px; 
            padding: 12px; text-decoration: none; 
            font-weight: 600; 
            transition: 0.3s;
        }
        .nav-link:hover { background: rgba(168, 85, 247, 0.1); }
        .nav-link.active { 
            background: rgba(168, 85, 247, 0.3) !important; 
            color: #a855f7 !important; 
        }

        /* Status Badges */
        .status-badge { 
            padding: 5px 14px; 
            border-radius: 8px; 
            font-size: 0.7rem; 
            font-weight: 800; 
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-paid { 
            background: rgba(34, 197, 94, 0.15); 
            color: #22c55e; 
            border: 1px solid #22c55e; 
        }
        .badge-pending { 
            background: rgba(255, 193, 7, 0.15); 
            color: #ffc107; 
            border: 1px solid #ffc107; 
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
        <a href="dashboard.php" class="nav-link"><i class="fa fa-home me-2"></i> Dashboard</a>
        <a href="users.php" class="nav-link"><i class="fa fa-users me-2"></i> Kelola Users</a>
        <a href="transaksi.php" class="nav-link active"><i class="fa fa-exchange-alt me-2"></i> Transaksi</a>
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
        <div class="mb-4">
            <h2 class="fw-bold mb-1">History <span style="color: #a855f7;">Trades</span></h2>
            <p style="color: #ffffff; opacity: 0.8;">Seluruh catatan transaksi dimensi Vanasaga ID.</p>
        </div>

        <div class="card-custom shadow-lg">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th>Waktu</th>
                            <th>Pembeli (IGN)</th>
                            <th>Rank</th>
                            <th>Total</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Query JOIN disesuaikan dengan struktur Vanasaga ID
                        $query = "SELECT t.tanggal, u.nama, t.nick_minecraft, r.nama_rank, t.total_harga, t.status 
                                  FROM transaksi t
                                  JOIN users u ON t.id_user = u.id_user
                                  JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi
                                  JOIN ranks r ON dt.id_rank = r.id_rank
                                  ORDER BY t.tanggal DESC";

                        $sql = mysqli_query($conn, $query);
                        
                        if($sql && mysqli_num_rows($sql) > 0) {
                            while($d = mysqli_fetch_array($sql)){
                                // Dinamis class untuk status
                                $status_class = ($d['status'] == 'paid') ? 'badge-paid' : 'badge-pending';
                        ?>
                        <tr>
                            <td style="font-size: 0.85rem; opacity: 0.7;"><?= date('d M Y, H:i', strtotime($d['tanggal'])) ?></td>
                            <td>
                                <div class="fw-bold"><?= htmlspecialchars($d['nama']) ?></div>
                                <small style="color: #00d4ff;"><?= htmlspecialchars($d['nick_minecraft']) ?></small>
                            </td>
                            <td>
                                <span class="badge" style="background: rgba(168, 85, 247, 0.1); border: 1px solid #a855f7; color: #a855f7;">
                                    <?= htmlspecialchars($d['nama_rank']) ?>
                                </span>
                            </td>
                            <td class="fw-bold">Rp <?= number_format($d['total_harga'], 0, ',', '.') ?></td>
                            <td class="text-center">
                                <span class="status-badge <?= $status_class ?>"><?= strtoupper($d['status']) ?></span>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 opacity-50'>Belum ada perdagangan yang terjadi.</td></tr>";
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    function toggleSidebar() { document.getElementById('adminSidebar').classList.toggle('show'); }
</script>

</body>
</html>