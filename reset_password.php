<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

$phone = $_POST['phone'] ?? '';
$new_password = $_POST['new_password'] ?? '';

if(empty($phone)){
    echo json_encode(["status" => "error", "pesan" => "Nomor telepon wajib diisi!"]);
    exit();
}

// JIKA HANYA CEK NOMOR (Password masih kosong)
if(empty($new_password)){
    $cek = mysqli_query($koneksi, "SELECT * FROM users WHERE phone='$phone'");
    if(mysqli_num_rows($cek) > 0){
        echo json_encode(["status" => "sukses", "pesan" => "Nomor terdaftar!"]);
    } else {
        echo json_encode(["status" => "error", "pesan" => "Nomor telepon tidak ditemukan!"]);
    }
} 
// JIKA UPDATE PASSWORD
else {
    $update = mysqli_query($koneksi, "UPDATE users SET password='$new_password' WHERE phone='$phone'");
    if($update){
        echo json_encode(["status" => "sukses", "pesan" => "Password berhasil diperbarui!"]);
    } else {
        echo json_encode(["status" => "error", "pesan" => "Gagal memperbarui password!"]);
    }
}
?>