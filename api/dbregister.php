<?php
include 'db_config.php';

$nama = $_POST['nama'];
$email = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$nick = trim($_POST['nick_minecraft']);
$platform = $_POST['platform'];

// Otomatisasi prefix titik untuk Bedrock agar sinkron dengan sistem LuckPerms
if ($platform === 'bedrock' && !str_starts_with($nick, '.')) {
    $nick = "." . $nick;
}

$query = "INSERT INTO users (nama, email, password, nick_minecraft, platform) 
          VALUES ('$nama', '$email', '$password', '$nick', '$platform')";

if (mysqli_query($conn, $query)) {
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => mysqli_error($conn)]);
}
?>