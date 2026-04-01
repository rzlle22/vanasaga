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

// --- LOGIKA BACKEND (CRUD) ---
if (isset($_POST['save_rank'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id_rank']);
    $nama = mysqli_real_escape_string($conn, $_POST['nama_rank']);
    $harga = mysqli_real_escape_string($conn, $_POST['harga']);
    $tipe = strtolower(trim(mysqli_real_escape_string($conn, $_POST['tipe']))); 
    $benefit = ""; 

    if (empty($id)) {
        $sql = "INSERT INTO ranks (nama_rank, tipe, harga, benefit) VALUES ('$nama', '$tipe', '$harga', '$benefit')";
    } else {
        $sql = "UPDATE ranks SET nama_rank='$nama', tipe='$tipe', harga='$harga' WHERE id_rank='$id'";
    }
    
    if (mysqli_query($conn, $sql)) {
        header("Location: ranks.php?msg=success");
        exit();
    } else {
        die("Gagal simpan! Error: " . mysqli_error($conn));
    }
}

if (isset($_GET['delete'])) {
    $id = mysqli_real_escape_string($conn, $_GET['delete']);
    if (mysqli_query($conn, "DELETE FROM ranks WHERE id_rank='$id'")) {
        header("Location: ranks.php?msg=deleted");
        exit();
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranks Management - Vanasaga Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">

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

        .rank-card {
            background: rgba(255, 255, 255, 0.05); border: 1px solid rgba(168, 85, 247, 0.3);
            border-radius: 28px; padding: 30px; text-align: center; transition: 0.3s ease;
            backdrop-filter: blur(10px); height: 100%;
        }
        .rank-card:hover { transform: translateY(-5px); background: rgba(168, 85, 247, 0.1); border-color: #a855f7; box-shadow: 0 10px 30px rgba(168, 85, 247, 0.2); }

        /* Responsive 1 Kolom Mobile */
        @media (max-width: 576px) {
            .rank-card { padding: 40px 20px; }
            .col-6 { width: 100% !important; }
            .action-btn { opacity: 1 !important; top: 15px !important; right: 15px !important; }
        }

        .rank-icon { font-size: 3rem; margin-bottom: 15px; color: #ffffff; text-shadow: 0 0 20px rgba(168, 85, 247, 0.6); }
        .user-count { background: rgba(168, 85, 247, 0.2); color: #a855f7; padding: 4px 12px; border-radius: 50px; font-size: 0.75rem; font-weight: 800; }

        .modal-content { background: rgba(15, 15, 15, 0.98); backdrop-filter: blur(25px); border: 1px solid rgba(168, 85, 247, 0.4); border-radius: 28px; color: white; }
        .form-control, .form-select { background: rgba(255, 255, 255, 0.05) !important; border: 1px solid rgba(168, 85, 247, 0.2) !important; color: white !important; border-radius: 14px; padding: 12px 15px; }

        .nav-link { color: #ffffff !important; border-radius: 12px; margin: 5px 15px; padding: 12px; text-decoration: none; font-weight: 600; transition: 0.3s; }
        .nav-link.active, .nav-link:hover { background: rgba(168, 85, 247, 0.3) !important; color: #a855f7 !important; }
        .nav-link.logout-link:hover { background: rgba(255, 68, 68, 0.1) !important; color: #ff4444 !important; }

        .action-btn { opacity: 0.4; transition: 0.3s; }
        .rank-card:hover .action-btn { opacity: 1; }

        .btn-save { background: #a855f7; border: none; font-weight: 700; padding: 12px 30px; border-radius: 50px; transition: 0.3s; }
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
        <a href="transaksi.php" class="nav-link"><i class="fa fa-exchange-alt me-2"></i> Transaksi</a>
        <a href="ranks.php" class="nav-link active"><i class="fa fa-crown me-2"></i> Ranks List</a>
        <a href="report.php" class="nav-link"><i class="fa fa-exclamation-triangle me-2"></i> Laporan Player</a>
    </div>
    <div class="pb-4">
        <hr style="border-color: rgba(168, 85, 247, 0.2); margin: 10px 20px;">
        <a href="logout.php" class="nav-link logout-link"><i class="fa fa-sign-out-alt me-2"></i> Logout</a>
    </div>
</div>

<div class="main-content">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-3">
            <div>
                <h2 class="fw-bold mb-1">Rank <span style="color: #a855f7;">Management</span></h2>
                <p style="opacity: 0.8; margin-bottom: 0;">Kelola harga, durasi, dan lisensi rank server.</p>
            </div>
            <button class="btn btn-primary px-4 py-2 rounded-pill shadow-lg fw-bold" style="background: #a855f7; border: none;" onclick="openAddModal()">
                <i class="fa fa-plus-circle me-2"></i> Tambah Rank
            </button>
        </div>

        <div class="row g-4">
            <?php
            $q_ranks = mysqli_query($conn, "SELECT r.*, COUNT(dt.id_detail) as total_users 
                                            FROM ranks r 
                                            LEFT JOIN detail_transaksi dt ON r.id_rank = dt.id_rank 
                                            LEFT JOIN transaksi t ON dt.id_transaksi = t.id_transaksi AND t.status = 'paid'
                                            GROUP BY r.id_rank");

            while($row = mysqli_fetch_assoc($q_ranks)) {
                $json_data = json_encode($row);
            ?>
            <div class="col-6 col-md-4 col-lg-3">
                <div class="rank-card position-relative">
                    <div class="position-absolute top-0 end-0 p-3 d-flex gap-2 action-btn">
                        <button class="btn btn-sm text-white" onclick="openEditModal(<?= htmlspecialchars($json_data) ?>)"><i class="fa fa-edit"></i></button>
                        <button class="btn btn-sm text-danger" onclick="confirmDelete(<?= $row['id_rank'] ?>)"><i class="fa fa-trash"></i></button>
                    </div>

                    <div onclick="showUsers(<?= $row['id_rank'] ?>, '<?= $row['nama_rank'] ?>')" style="cursor: pointer;">
                        <i class="fa fa-crown rank-icon"></i>
                        <h4 class="fw-bold mb-1"><?= $row['nama_rank'] ?></h4>
                        <div class="mb-3">
                            <span class="badge rounded-pill bg-primary bg-opacity-25 text-primary px-3 py-2 small" style="font-weight: 700;"><?= strtoupper($row['tipe'] ?? 'PERMANENT') ?></span>
                            <div class="text-success fw-bold mt-2" style="font-size: 1.1rem;">Rp <?= number_format($row['harga'], 0, ',', '.') ?></div>
                        </div>
                        <span class="user-count">👤 <?= $row['total_users'] ?> Users</span>
                    </div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>

<div class="modal fade" id="rankFormModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content shadow-lg">
            <form action="" method="POST">
                <div class="modal-header border-0 px-4 pt-4">
                    <h5 class="modal-title fw-bold" id="formTitle">Tambah Rank</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body px-4 pb-4">
                    <input type="hidden" name="id_rank" id="form_id_rank">
                    <div class="mb-4">
                        <label class="form-label small fw-bold text-uppercase opacity-50 mb-2">Nama Rank</label>
                        <input type="text" name="nama_rank" id="form_nama_rank" class="form-control" placeholder="Contoh: EMPEROR" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-4">
                            <label class="form-label small fw-bold text-uppercase opacity-50 mb-2">Harga (Rp)</label>
                            <input type="number" name="harga" id="form_harga" class="form-control" placeholder="0" required>
                        </div>
                        <div class="col-md-6 mb-4">
                            <label class="form-label small fw-bold text-uppercase opacity-50 mb-2">Tipe Durasi</label>
                            <select name="tipe" id="form_tipe" class="form-select">
                                <option value="permanent">Permanen</option>
                                <option value="trial">Trial (30 Hari)</option>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button" class="btn btn-link text-white text-decoration-none fw-bold me-auto" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" name="save_rank" class="btn btn-primary btn-save">SIMPAN RANK</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0 px-4 pt-4">
                <h5 class="modal-title" id="modalTitle"></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pb-4" id="userList"></div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const rankFormModal = new bootstrap.Modal(document.getElementById('rankFormModal'));
    function toggleSidebar() { document.getElementById('adminSidebar').classList.toggle('show'); }

    function openAddModal() {
        document.getElementById('formTitle').innerText = "BUAT RANK BARU";
        document.getElementById('form_id_rank').value = "";
        document.getElementById('form_nama_rank').value = "";
        document.getElementById('form_harga').value = "";
        document.getElementById('form_tipe').value = "permanent";
        rankFormModal.show();
    }

    function openEditModal(data) {
        document.getElementById('formTitle').innerText = "SETTING RANK: " + data.nama_rank.toUpperCase();
        document.getElementById('form_id_rank').value = data.id_rank;
        document.getElementById('form_nama_rank').value = data.nama_rank;
        document.getElementById('form_harga').value = data.harga;
        document.getElementById('form_tipe').value = data.tipe.toLowerCase();
        rankFormModal.show();
    }

    function confirmDelete(id) {
        if(confirm('Hapus rank ini secara permanen?')) {
            window.location.href = `ranks.php?delete=${id}`;
        }
    }

    function showUsers(idRank, namaRank) {
        const modal = new bootstrap.Modal(document.getElementById('userModal'));
        document.getElementById('modalTitle').innerHTML = `<i class="fa fa-crown text-warning me-2"></i>Rank ${namaRank}`;
        const container = document.getElementById('userList');
        container.innerHTML = "<div class='text-center py-4'><div class='spinner-border text-primary'></div></div>";
        
        fetch(`api/get_ranks_user.php?id=${idRank}`)
            .then(res => res.json())
            .then(data => {
                container.innerHTML = "";
                if(data.length === 0) {
                    container.innerHTML = "<div class='text-center py-4 opacity-50'><i class='fa fa-ghost fa-2x mb-2 d-block'></i>Belum ada pemegang rank.</div>";
                } else {
                    data.forEach(user => {
                        container.innerHTML += `
                            <div class="p-3 mb-2 rounded-4" style="background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-bold">${user.nama}</span>
                                    <span class="badge bg-white bg-opacity-10">🎮 ${user.nick_minecraft}</span>
                                </div>
                            </div>`;
                    });
                }
            });
        modal.show();
    }
</script>

</body>
</html>