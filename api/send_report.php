<?php
include 'db_config.php'; // Pastikan path ke db_config benar

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dan amankan dari SQL Injection
    $user = mysqli_real_escape_string($conn, $_POST['username']);
    $tipe = mysqli_real_escape_string($conn, $_POST['tipe']);
    $laporan = mysqli_real_escape_string($conn, $_POST['laporan']);

    // Query simpan ke tabel baru
    $sql = "INSERT INTO reports (username, tipe_laporan, isi_laporan) VALUES ('$user', '$tipe', '$laporan')";

    if (mysqli_query($conn, $sql)) {
        // Jika sukses, lempar balik ke halaman report dengan notifikasi
        echo "<script>alert('Laporan berhasil dikirim! Staff akan segera meninjau.'); window.location.href='../report.html';</script>";
    } else {
        echo "Gagal mengirim laporan: " . mysqli_error($conn);
    }
}
?>