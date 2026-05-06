<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli('localhost', 'root', '', 'puddingku');
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'pesan' => 'Koneksi gagal: ' . $conn->connect_error]);
    exit;
}

$id_menu = $_POST['id_menu'] ?? '';

if (empty($id_menu)) {
    echo json_encode(['status' => 'error', 'pesan' => 'ID menu tidak ditemukan']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM menu WHERE id_produk = ?");
$stmt->bind_param('i', $id_menu);

if ($stmt->execute()) {
    echo json_encode(['status' => 'sukses', 'pesan' => 'Menu berhasil dihapus']);
} else {
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal hapus: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
