<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$conn = new mysqli('localhost', 'root', '', 'puddingku');
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'pesan' => 'Koneksi gagal: ' . $conn->connect_error]);
    exit;
}

$id_menu     = $_POST['id_menu']     ?? '';
$nama_produk = $_POST['nama_produk'] ?? '';
$harga       = $_POST['harga']       ?? '';
$deskripsi   = $_POST['deskripsi']   ?? '';

if (empty($id_menu) || empty($nama_produk) || empty($harga)) {
    echo json_encode(['status' => 'error', 'pesan' => 'Data tidak lengkap']);
    exit;
}

$stmt = $conn->prepare("UPDATE menu SET nama_produk = ?, harga = ?, deskripsi = ? WHERE id_produk = ?");
$stmt->bind_param('sisi', $nama_produk, $harga, $deskripsi, $id_menu);

if ($stmt->execute()) {
    echo json_encode(['status' => 'sukses', 'pesan' => 'Menu berhasil diperbarui']);
} else {
    echo json_encode(['status' => 'error', 'pesan' => 'Gagal update: ' . $stmt->error]);
}

$stmt->close();
$conn->close();