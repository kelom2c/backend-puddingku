<?php
error_reporting(0);
ini_set('display_errors', 0);
ob_start();

include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {

    $sql    = "SELECT * FROM menu";
    $result = $conn->query($sql);
    $data   = [];

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }

    ob_end_clean();
    echo json_encode($data);

} elseif ($method == 'POST') {

    $nama      = $_POST['nama_produk'] ?? '';
    $kategori  = $_POST['kategori']    ?? '';
    $harga     = intval($_POST['harga']  ?? 0);
    $stok      = intval($_POST['stok']   ?? 0);
    $deskripsi = $_POST['deskripsi']   ?? '';

    $nama_file_gambar = "tiramisu.png";

    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == 0) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $nama_file_gambar = time() . "_" . basename($_FILES["gambar"]["name"]);
        move_uploaded_file($_FILES["gambar"]["tmp_name"], $target_dir . $nama_file_gambar);
    }

    if (!empty($nama) && !empty($kategori)) {
        $nama_esc      = $conn->real_escape_string($nama);
        $kategori_esc  = $conn->real_escape_string($kategori);
        $deskripsi_esc = $conn->real_escape_string($deskripsi);
        $gambar_esc    = $conn->real_escape_string($nama_file_gambar);

        $sql = "INSERT INTO menu (nama_produk, kategori, harga, stok, deskripsi, gambar)
                VALUES ('$nama_esc', '$kategori_esc', $harga, $stok, '$deskripsi_esc', '$gambar_esc')";

        ob_end_clean();
        if ($conn->query($sql) === TRUE) {
            echo json_encode(["status" => "sukses", "pesan" => "Menu berhasil ditambahkan!"]);
        } else {
            echo json_encode(["status" => "error", "pesan" => $conn->error]);
        }
    } else {
        ob_end_clean();
        echo json_encode(["status" => "error", "pesan" => "Nama dan Kategori wajib diisi!"]);
    }

} else {
    ob_end_clean();
    echo json_encode(["status" => "error", "pesan" => "Method tidak diizinkan."]);
}
?>