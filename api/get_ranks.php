<?php
header('Content-Type: application/json');
include "db_config.php";

// Mengambil data dengan memastikan benefit tidak NULL
$q = mysqli_query($conn, "SELECT id_rank, nama_rank, tipe, harga, COALESCE(benefit, '-') as benefit FROM ranks");

if (!$q) {
    echo json_encode(["error" => mysqli_error($conn)]);
    exit;
}

$data = [];
while ($row = mysqli_fetch_assoc($q)) {
    // PENTING: Mengubah baris baru menjadi koma agar tidak merusak JavaScript
    $row['benefit'] = str_replace(array("\r", "\n"), ', ', $row['benefit']);
    $data[] = $row;
}

echo json_encode($data);
?>