<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

$username = $_POST['username'] ?? '';
$password = $_POST['password'] ?? '';

if(empty($username) || empty($password)){
    echo json_encode(["status" => "error", "pesan" => "Username dan Password wajib diisi!"]);
    exit();
}

// Cari user berdasarkan username DAN password
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE (username='$username' OR phone='$username') AND password='$password'");

if(mysqli_num_rows($query) > 0){
    $user = mysqli_fetch_assoc($query);
    echo json_encode([
        "status" => "sukses",
        "pesan" => "Selamat datang, " . $user['nama'],
        "role" => $user['role'],
        "user" => $user // Mengirim data user kalau butuh nama/id di halaman menu
    ]);
} else {
    echo json_encode(["status" => "error", "pesan" => "Username atau Password salah!"]);
}
?>