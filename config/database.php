<?php
// Konfigurasi Database
$host = 'localhost';
$dbname = 'smart_b';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password (kosong)

try {
    // PDO Connection (For Modern CMS API)
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // MySQLi Connection (For Legacy Portal Index)
    $conn = mysqli_connect($host, $username, $password, $dbname);
    if (!$conn) {
        throw new Exception("MySQLi Connection Failed: " . mysqli_connect_error());
    }
} catch(Exception $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}