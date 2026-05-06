<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST");
header("Content-Type: application/json; charset=UTF-8");

include 'koneksi.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // ============================================================
    // MENAMPILKAN RIWAYAT PESANAN (Untuk Admin atau User)
    // ============================================================
    $kondisi = "";
    if (isset($_GET['id_user'])) {
        $id_user = $_GET['id_user'];
        $kondisi = "WHERE p.id_user = '$id_user'";
    }

    // tanggal_pesan di-alias jadi created_at agar Flutter bisa baca
    $sql = "SELECT p.*, p.tanggal_pesan AS created_at 
            FROM pesanan p $kondisi 
            ORDER BY p.id_pesanan DESC";

    $result = $conn->query($sql);
    $data = array();

    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $id_pesanan = $row['id_pesanan'];

            // Ambil rincian produk
            $sql_detail = "SELECT d.jumlah, m.nama_produk 
                           FROM detail_pesanan d 
                           JOIN menu m ON d.id_menu = m.id_produk 
                           WHERE d.id_pesanan = '$id_pesanan'";
            $res_detail = $conn->query($sql_detail);

            $ringkasan = [];
            if ($res_detail) {
                while ($d = $res_detail->fetch_assoc()) {
                    $ringkasan[] = $d['nama_produk'] . " " . $d['jumlah'] . "x";
                }
            }

            $row['ringkasan_pesanan'] = implode(", ", $ringkasan);
            $data[] = $row;
        }
    }

    echo json_encode($data);

} elseif ($method == 'POST') {
    // ============================================================
    // PROSES CHECKOUT
    // ============================================================
    $input = json_decode(file_get_contents("php://input"), true);

    if (isset($input['id_user']) && isset($input['total_harga']) && isset($input['items'])) {
        $id_user     = $input['id_user'];
        $total_harga = $input['total_harga'];
        $metode      = isset($input['metode_pembayaran']) ? $input['metode_pembayaran'] : 'Bayar di Outlet';
        $items       = $input['items'];

        // tanggal_pesan otomatis terisi NOW()
        $sql_pesanan = "INSERT INTO pesanan (id_user, total_harga, metode_pembayaran, tanggal_pesan) 
                        VALUES ('$id_user', '$total_harga', '$metode', NOW())";

        if ($conn->query($sql_pesanan) === TRUE) {
            $id_pesanan_baru = $conn->insert_id;

            foreach ($items as $item) {
                $id_menu      = $item['id_menu'];
                $jumlah       = $item['jumlah'];
                $harga_satuan = $item['harga'];
                $subtotal     = $jumlah * $harga_satuan;

                $sql_detail = "INSERT INTO detail_pesanan (id_pesanan, id_menu, jumlah, harga_satuan, subtotal) 
                               VALUES ('$id_pesanan_baru', '$id_menu', '$jumlah', '$harga_satuan', '$subtotal')";
                $conn->query($sql_detail);
            }

            $conn->query("DELETE FROM keranjang WHERE id_user = '$id_user'");

            echo json_encode([
                "status"     => "sukses",
                "pesan"      => "Checkout Berhasil!",
                "id_pesanan" => $id_pesanan_baru
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "pesan"  => "Gagal membuat pesanan: " . $conn->error
            ]);
        }
    } else {
        echo json_encode([
            "status" => "error",
            "pesan"  => "Data checkout tidak lengkap"
        ]);
    }
}
?>
