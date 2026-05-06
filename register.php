<?php
// Izinkan aplikasi luar (Flutter) buat ngakses file ini
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

// Nangkep data yang dikirim dari Flutter
$nama = $_POST['nama'] ?? '';
$username = $_POST['username'] ?? '';
$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? ''; 

// Cek kalau ada data yang kosong
if(empty($nama) || empty($username) || empty($phone) || empty($password)){
    echo json_encode(["status" => "error", "pesan" => "Data tidak boleh kosong!"]);
    exit();
}

// --- LOGIKA BARU: Cek Username ATAU Nomor Telepon ---
$cek_user = mysqli_query($koneksi, "SELECT * FROM users WHERE username='$username' OR phone='$phone'");

if(mysqli_num_rows($cek_user) > 0){
    // Kalau ketahuan ada yang sama (entah username atau nomornya), tolak!
    echo json_encode(["status" => "error", "pesan" => "Username atau Nomor Telepon sudah terdaftar! Harap gunakan yang lain."]);
} else {
    // Kalau aman, simpan ke database
    $query = "INSERT INTO users (nama, username, phone, password) VALUES ('$nama', '$username', '$phone', '$password')";
    $insert = mysqli_query($koneksi, $query);

    if($insert){
        echo json_encode(["status" => "sukses", "pesan" => "Register berhasil!"]);
    } else {
        echo json_encode(["status" => "error", "pesan" => "Gagal mendaftar, coba lagi."]);
    }
}
?>