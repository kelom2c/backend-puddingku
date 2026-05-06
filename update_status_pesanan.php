<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php'; // Pastikan file koneksi.php abang sudah benar

// Mengambil data dari POST (dikirim oleh Flutter)
$id_pesanan = isset($_POST['id_pesanan']) ? $_POST['id_pesanan'] : '';
$status = isset($_POST['status']) ? $_POST['status'] : '';

$response = array();

if (!empty($id_pesanan) && !empty($status)) {
    // 1. Pastikan nama kolom 'status_pesanan' sama dengan yang ada di tabel 'pesanan' abang
    // 2. Jika nama kolom di database abang cuma 'status', ganti 'status_pesanan' jadi 'status'
    $sql = "UPDATE pesanan SET status_pesanan = '$status' WHERE id_pesanan = '$id_pesanan'";

    if ($conn->query($sql) === TRUE) {
        $response['status'] = 'sukses';
        $response['pesan'] = 'Status pesanan berhasil diperbarui menjadi ' . $status;
    } else {
        $response['status'] = 'error';
        $response['pesan'] = 'Gagal memperbarui database: ' . $conn->error;
    }
} else {
    $response['status'] = 'error';
    $response['pesan'] = 'Data tidak lengkap. ID Pesanan atau Status kosong.';
}

echo json_encode($response);

$conn->close();
?>