<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

$host = getenv("MYSQLHOST") ?: "localhost";
$user = getenv("MYSQLUSER") ?: "root";       
$pass = getenv("MYSQLPASSWORD") ?: "";           
$db   = getenv("MYSQLDATABASE") ?: "puddingku";  
$port = getenv("MYSQLPORT") ?: 3306;

$conn = new mysqli($host, $user, $pass, $db, $port);
$koneksi = $conn; 

if ($conn->connect_error) {
    die(json_encode(["status" => "error", "pesan" => "Koneksi Database Gagal: " . $conn->connect_error]));
}
// SENGAJA TIDAK ADA TAG PENUTUP DI SINI AGAR AMAN DARI SPASI GAIB