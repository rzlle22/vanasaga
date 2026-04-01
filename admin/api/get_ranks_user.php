<?php
include '../../api/db_config.php';
header('Content-Type: application/json');

// Pastikan ID Rank adalah angka untuk keamanan
$id_rank = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_rank <= 0) {
    echo json_encode([]);
    exit;
}

// Query JOIN disesuaikan dengan screenshot databasemu
$query = "SELECT u.nama, u.nick_minecraft 
          FROM users u 
          JOIN transaksi t ON u.id_user = t.id_user 
          JOIN detail_transaksi dt ON t.id_transaksi = dt.id_transaksi 
          WHERE dt.id_rank = $id_rank AND t.status = 'paid'";

$res = mysqli_query($conn, $query);
$users = [];

if ($res) {
    while($row = mysqli_fetch_assoc($res)) {
        $users[] = $row;
    }
}

echo json_encode($users);
?>