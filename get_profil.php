<?php
// Izinkan akses dari aplikasi Flutter
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// Panggil koneksi database lo
include 'koneksi.php';

// Cek apakah aplikasi Flutter mengirimkan 'id_user'
if(isset($_POST['id_user'])) {
    $id_user = $_POST['id_user'];

    // Cari data user di database berdasarkan ID
    // PENTING: Pastikan nama tabelnya 'users' dan kolomnya 'nama', 'username', 'phone'
    $query = "SELECT nama, username, phone FROM users WHERE id = '$id_user'";
    $result = mysqli_query($koneksi, $query);

    // Kalau datanya ketemu
    if(mysqli_num_rows($result) > 0) {
        $data = mysqli_fetch_assoc($result);
        
        // Kirim balikan sukses beserta datanya ke Flutter
        echo json_encode([
            "status" => "sukses", 
            "data" => $data
        ]);
    } else {
        // Kalau user nggak ada di database
        echo json_encode([
            "status" => "gagal", 
            "pesan" => "User tidak ditemukan"
        ]);
    }
} else {
    // Kalau ID User tidak terkirim dari Flutter
    echo json_encode([
        "status" => "error", 
        "pesan" => "ID User tidak dikirim"
    ]);
}
?>