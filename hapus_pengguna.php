<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

include 'koneksi.php';

$id = $_POST['id'] ?? '';

if (!empty($id)) {
    // Kita hapus berdasarkan kolom 'id' sesuai di database lo
    $query = "DELETE FROM users WHERE id = '$id'";
    if (mysqli_query($koneksi, $query)) {
        echo json_encode(["status" => "sukses", "pesan" => "Pengguna berhasil dihapus"]);
    } else {
        echo json_encode(["status" => "gagal", "pesan" => "Gagal menghapus pengguna"]);
    }
} else {
    echo json_encode(["status" => "gagal", "pesan" => "ID tidak valid"]);
}
?>