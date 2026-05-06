<?php
// 1. Sembunyikan error HTML bawaan XAMPP
error_reporting(0);
ini_set('display_errors', 0);

// 2. Jinakkan mode galak MySQLi di PHP versi terbaru
mysqli_report(MYSQLI_REPORT_OFF);

header("Content-Type: application/json; charset=UTF-8");

// 3. Bungkus seluruh kodingan dengan Try-Catch agar anti-crash
try {
    include 'koneksi.php'; 

    if(isset($koneksi)){ 
        $db = $koneksi; 
    } elseif(isset($conn)){ 
        $db = $conn; 
    } else {
        echo json_encode(["status" => "gagal", "pesan" => "Variabel database tidak ditemukan!"]); 
        exit;
    }

    $id_user = $_POST['id_user'] ?? '';
    $nama = $_POST['nama_pemesan'] ?? '';
    $no_telp = $_POST['no_telp'] ?? '';

    if(empty($id_user) || empty($nama) || empty($no_telp)) {
        echo json_encode(["status" => "gagal", "pesan" => "Data tidak lengkap!"]);
        exit;
    }

    // ==============================================================================
    // PERBAIKAN: Ubah m.id_menu menjadi m.id_produk di JOIN
    // ==============================================================================
    $sql_total = "SELECT SUM(k.jumlah * m.harga) as total FROM keranjang k JOIN menu m ON k.id_menu = m.id_produk WHERE k.id_user = '$id_user'";
    $result_total = $db->query($sql_total);

    if (!$result_total) {
        echo json_encode(["status" => "gagal", "pesan" => "Error Tabel Keranjang: " . $db->error]);
        exit;
    }

    $row_total = $result_total->fetch_assoc();
    $total_harga = $row_total['total'] ?? 0;

    if($total_harga == 0){
        echo json_encode(["status" => "gagal", "pesan" => "Keranjang masih kosong!"]);
        exit;
    }

    $kode_resi = "#PUD" . rand(1000, 9999);

    // Insert Pesanan
    $sql_pesanan = "INSERT INTO pesanan (id_user, nama_pemesan, no_telp, kode_resi, total_harga, status_pesanan) 
                    VALUES ('$id_user', '$nama', '$no_telp', '$kode_resi', '$total_harga', 'PROSES')";

    if ($db->query($sql_pesanan) === TRUE) {
        $id_pesanan = $db->insert_id;

        // ==============================================================================
        // PERBAIKAN JUGA DISINI: Ubah m.id_menu menjadi m.id_produk di JOIN
        // ==============================================================================
        $sql_detail = "INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, harga_satuan, subtotal)
                       SELECT '$id_pesanan', k.id_menu, k.jumlah, m.harga, (k.jumlah * m.harga)
                       FROM keranjang k JOIN menu m ON k.id_menu = m.id_produk
                       WHERE k.id_user = '$id_user'";
        
        if (!$db->query($sql_detail)) {
            echo json_encode(["status" => "gagal", "pesan" => "Error Tabel Detail: " . $db->error]);
            exit;
        }

        // ==============================================================================
        // FITUR BARU: PENGURANGAN STOK OTOMATIS (Tanpa while loop, sangat cepat)
        // ==============================================================================
        $sql_kurangi_stok = "UPDATE menu m 
                             JOIN keranjang k ON m.id_produk = k.id_menu 
                             SET m.stok = m.stok - k.jumlah 
                             WHERE k.id_user = '$id_user'";
        
        if (!$db->query($sql_kurangi_stok)) {
            echo json_encode(["status" => "gagal", "pesan" => "Error Kurangi Stok: " . $db->error]);
            exit;
        }
        // ==============================================================================

        // Hapus Keranjang (Dilakukan SETELAH stok dikurangi agar datanya tidak hilang duluan)
        $db->query("DELETE FROM keranjang WHERE id_user = '$id_user'");

        echo json_encode([
            "status" => "sukses",
            "pesan" => "Pesanan berhasil dibuat",
            "kode_resi" => $kode_resi
        ]);
    } else {
        echo json_encode(["status" => "gagal", "pesan" => "Error Tabel Pesanan: " . $db->error]);
    }

} catch (Exception $e) {
    echo json_encode(["status" => "gagal", "pesan" => "Fatal Error XAMPP: " . $e->getMessage()]);
}
?>