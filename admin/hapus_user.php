<?php 
include '../api/db.php';

$id = $_GET['id'];
$query = mysqli_query($koneksi, "DELETE FROM users WHERE id='$id'");

if($query){
    echo "<script>alert('Data berhasil dihapus'); window.location='users.php';</script>";
} else {
    echo "Gagal menghapus: " . mysqli_error($koneksi);
}
?>