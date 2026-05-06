<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200); exit();
}

$method = $_SERVER['REQUEST_METHOD'];

// Bagian GET di api_keranjang.php
if ($method == 'GET') {
    $id_user = $_GET['id_user'] ?? '';
    // Join k.id_menu (tabel keranjang) dengan m.id_produk (tabel menu)
    $sql = "SELECT k.id_keranjang, k.jumlah, m.id_produk, m.nama_produk, m.harga, m.gambar 
            FROM keranjang k 
            JOIN menu m ON k.id_menu = m.id_produk 
            WHERE k.id_user = '$id_user'";
    // ...
    $result = $conn->query($sql);
    $data = [];
    while($row = $result->fetch_assoc()) { $data[] = $row; }
    echo json_encode($data);

} elseif ($method == 'POST') {
    $id_user = $_POST['id_user'] ?? '';
    $id_menu = $_POST['id_menu'] ?? '';
    $jumlah  = $_POST['jumlah'] ?? 1;

    if (empty($id_user)) {
        $json = json_decode(file_get_contents("php://input"), true);
        $id_user = $json['id_user'] ?? '';
        $id_menu = $json['id_menu'] ?? '';
        $jumlah  = $json['jumlah'] ?? 1;
    }

    if (!empty($id_user) && !empty($id_menu)) {
        // Cek data di tabel keranjang
        $cek = $conn->query("SELECT * FROM keranjang WHERE id_user = '$id_user' AND id_menu = '$id_menu'");
        if ($cek->num_rows > 0) {
            $sql = "UPDATE keranjang SET jumlah = jumlah + $jumlah WHERE id_user = '$id_user' AND id_menu = '$id_menu'";
        } else {
            $sql = "INSERT INTO keranjang (id_user, id_menu, jumlah) VALUES ('$id_user', '$id_menu', '$jumlah')";
        }

        if ($conn->query($sql)) {
            echo json_encode(["status" => "sukses", "pesan" => "Masuk keranjang"]);
        } else {
            echo json_encode(["status" => "error", "pesan" => $conn->error]);
        }
    } else {
        echo json_encode(["status" => "error", "pesan" => "Gagal: id_user atau id_menu (id_produk) kosong"]);
    }
}
?>