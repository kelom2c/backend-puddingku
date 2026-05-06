<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

include 'koneksi.php'; 

// PERBAIKAN: Kita pakai kolom 'id' sesuai dengan screenshot database lo!
$query = "SELECT * FROM users ORDER BY id DESC"; 
$result = mysqli_query($koneksi, $query);

$users = [];
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    echo json_encode(["status" => "sukses", "data" => $users]);
} else {
    // Biar kalau error ketahuan pesan aslinya dari MySQL
    echo json_encode(["status" => "gagal", "pesan" => mysqli_error($koneksi)]); 
}
?>