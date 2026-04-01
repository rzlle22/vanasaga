<?php
include '../api/db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // 1. Ambil data dari form
    $id       = $_POST['id'];
    $nama     = $_POST['nama'];
    $email    = $_POST['email'];
    $nick     = $_POST['nick_minecraft'];
    $platform = $_POST['platform'];
    $password = $_POST['password'];

    // 2. Mulai susun Query Update Dasar
    $query = "UPDATE users SET 
                nama='$nama', 
                email='$email', 
                nick_minecraft='$nick', 
                platform='$platform'";

    // 3. Logika Reset Sandi yang AMAN
    if (!empty($password)) {
        // PERBAIKAN: Gunakan password_hash agar sinkron dengan sistem Login/Register
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $query .= ", password='$hashed_password'";
    }

    $query .= " WHERE id_user='$id'";

    // 4. Eksekusi ke Database
    if (mysqli_query($conn, $query)) {
        echo "<script>
                alert('Data & Password Berhasil Diperbarui!'); 
                window.location='users.php';
              </script>";
    } else {
        echo "<script>
                alert('Error: " . mysqli_error($conn) . "'); 
                window.history.back();
              </script>";
    }
}
?>