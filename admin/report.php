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

// --- LOGIKA BACKEND ---
// Update Status Laporan
if (isset($_GET['status']) && isset($_GET['id'])) {
    $id = mysqli_real_escape_string($conn, $_GET['id']);
    $st = mysqli_real_escape_string($conn, $_GET['status']);
    mysqli_query($conn, "UPDATE reports SET status='$st' WHERE id_report='$id'");
    header("Location: reports.php?msg=updated");
    exit;
}

// Hapus Laporan
if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    mysqli_query($conn, "DELETE FROM reports WHERE id_report='$id'");
    header("Location: reports.php?msg=deleted");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Reports - Vanasaga Admin</title>
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

        /* Sidebar & Layout */
        .sidebar { width: 250px; background: rgba(10, 10, 10, 0.95) !important; backdrop-filter: blur(20px); border-right: 1px solid rgba(168, 85, 247, 0.3); min-height: 100vh; position: fixed; transition: all 0.3s; z-index: 1050; }
        .main-content { padding: 20px; transition: all 0.3s; }

        @media (min-width: 992px) { .main-content { margin-left: 250px; padding: 40px; } .mobile-header { display: none !important; } }
        @media (max-width: 991.98px) { 
            .sidebar { left: -250px; } 
            .sidebar.show { left: 0; } 
            .main-content { margin-left: 0; padding-top: 80px; } 
            .mobile-header { display: flex !important; position: fixed; top: 0; width: 100%; z-index: 1040; padding: 15px 20px; background: rgba(10, 10, 10, 0.9); backdrop-filter: blur(10px); border-bottom: 1px solid rgba(168, 85, 247, 0.3); } 
        }

        /* Card & Table */
        .card-custom { background: rgba(15, 15, 15, 0.5) !important; border: 1px solid rgba(168, 85, 247, 0.4) !important; backdrop-filter: blur(15px); border-radius: 24px; padding: 20px; }
        .table { color: #ffffff !important; margin-bottom: 0; }
        .table thead th { background: rgba(168, 85, 247, 0.2) !important; color: #a855f7 !important; border-bottom: 2px solid rgba(168, 85, 247, 0.5) !important; font-weight: 800; text-transform: uppercase; font-size: 0.75rem; padding: 15px; }
        .table tbody td { border-bottom: 1px solid rgba(168, 85, 247, 0.1) !important; padding: 15px; background: transparent !important; color: #fff !important; }

        /* Nav Links */
        .nav-link { color: #ffffff !important; border-radius: 12px; margin: 5px 15px; padding: 12px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .nav-link.active, .nav-link:hover { background: rgba(168, 85, 247, 0.3) !important; color: #a855f7 !important; }

        /* --- MODERN STATUS BUTTONS --- */
        .status-btn-group { display: flex; gap: 8px; justify-content: center; }
        
        .btn-action {
            padding: 7px 14px;
            border-radius: 10px;
            font-size: 0.75rem;
            font-weight: 700;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 6px;
            border: 1px solid rgba(255, 255, 255, 0.05);
            background: rgba(255, 255, 255, 0.03);
            color: rgba(255, 255, 255, 0.4);
        }

        .btn-action:hover { transform: translateY(-2px); color: #fff; }

        /* Status: Proses (Orange/Yellow) */
        .btn-proses.active {
            background: rgba(245, 158, 11, 0.15);
            border-color: #f59e0b;
            color: #fbbf24;
            box-shadow: 0 0 15px rgba(245, 158, 11, 0.2);
        }
        .btn-proses.active i { animation: spin 2s linear infinite; }

        /* Status: Selesai (Green) */
        .btn-selesai.active {
            background: rgba(34, 197, 94, 0.15);
            border-color: #22c55e;
            color: #4ade80;
            box-shadow: 0 0 15px rgba(34, 197, 94, 0.2);
        }

        /* Action: Delete (Red) */
        .btn-delete:hover {
            background: rgba(239, 68, 68, 0.2);
            border-color: #ef4444;
            color: #f87171;
        }

        @keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }
        
        /* Mobile Specific Table */
        @media (max-width: 768px) {
            .table thead { display: none; }
            .table tr { display: block; margin-bottom: 20px; background: rgba(255,255,255,0.03); border-radius: 15px; padding: 10px; border: 1px solid rgba(168, 85, 247, 0.2); }
            .table td { display: flex; justify-content: space-between; align-items: center; text-align: right; border: none !important; padding: 8px 10px !important; font-size: 0.85rem; }
            .table td::before { content: attr(data-label); font-weight: 800; color: #a855f7; text-transform: uppercase; font-size: 0.7rem; float: left; }
            .status-btn-group { justify-content: flex-end; margin-top: 5px; }
        }
    </style>
</head>
<body>

<div class="mobile-header d-flex d-lg-none justify-content-between align-items-center shadow-sm">
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
        <a href="transaksi.php" class="nav-link"><i class="fa fa-exchange-alt me-2"></i> Transaksi</a>
        <a href="ranks.php" class="nav-link"><i class="fa fa-crown me-2"></i> Ranks List</a>
        <a href="report.php" class="nav-link active"><i class="fa fa-exclamation-triangle me-2"></i> Laporan Player</a>
    </div>
    <div class="pb-4">
        <hr style="border-color: rgba(168, 85, 247, 0.2); margin: 10px 20px;">
        <a href="logout.php" class="nav-link text-danger"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="mb-4">
            <h2 class="fw-bold mb-1">Player <span style="color: #a855f7;">Reports</span></h2>
            <p style="color: #ffffff; opacity: 0.8;">Manajemen aduan bug dan laporan pemain Vanasaga ID.</p>
        </div>

        <div class="card-custom shadow-lg">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th style="width: 15%;">Waktu</th>
                            <th style="width: 15%;">Username</th>
                            <th style="width: 10%;">Tipe</th>
                            <th style="width: 30%;">Keterangan</th>
                            <th class="text-center" style="width: 30%;">Aksi & Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $q = mysqli_query($conn, "SELECT * FROM reports ORDER BY tanggal_kirim DESC");
                        if($q && mysqli_num_rows($q) > 0) {
                            while($row = mysqli_fetch_assoc($q)) {
                                $currentStatus = $row['status'];
                        ?>
                        <tr>
                            <td data-label="Waktu" style="font-size: 0.8rem; opacity: 0.7;"><?= date('d M, H:i', strtotime($row['tanggal_kirim'])) ?></td>
                            <td data-label="Username" class="fw-bold"><?= htmlspecialchars($row['username']) ?></td>
                            <td data-label="Tipe"><span style="color: #00d4ff; font-weight: 600;">#<?= strtoupper($row['tipe_laporan']) ?></span></td>
                            <td data-label="Keterangan">
                                <div style="max-height: 80px; overflow-y: auto; font-size: 0.85rem; line-height: 1.4;">
                                    <?= nl2br(htmlspecialchars($row['isi_laporan'])) ?>
                                </div>
                            </td>
                            <td data-label="Aksi" class="text-center">
                                <div class="status-btn-group">
                                    <a href="reports.php?id=<?= $row['id_report'] ?>&status=proses" 
                                       class="btn-action btn-proses <?= ($currentStatus == 'proses') ? 'active' : '' ?>" title="Tandai Sedang Diproses">
                                        <i class="fa fa-spinner"></i> <?= ($currentStatus == 'proses') ? 'Diproses' : 'Proses' ?>
                                    </a>

                                    <a href="reports.php?id=<?= $row['id_report'] ?>&status=selesai" 
                                       class="btn-action btn-selesai <?= ($currentStatus == 'selesai') ? 'active' : '' ?>" title="Tandai Selesai">
                                        <i class="fa fa-check-circle"></i> Selesai
                                    </a>

                                    <a href="reports.php?delete=<?= $row['id_report'] ?>" 
                                       class="btn-action btn-delete" 
                                       onclick="return confirm('Hapus laporan ini permanen?')" title="Hapus Laporan">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php 
                            }
                        } else {
                            echo "<tr><td colspan='5' class='text-center py-5 opacity-50'>Belum ada laporan masuk.</td></tr>";
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
    function toggleSidebar() { 
        document.getElementById('adminSidebar').classList.toggle('show'); 
    }
</script>

</body>
</html>