<?php
// Konfigurasi Database
$host = 'localhost';
$dbname = 'smart_c';
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
    // Auto-Initialization logic for Login System
    $pdo->exec("CREATE TABLE IF NOT EXISTS web_users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) UNIQUE NOT NULL,
        password VARCHAR(255) NOT NULL,
        nama_lengkap VARCHAR(100),
        role ENUM('admin', 'warga', 'bendahara') DEFAULT 'warga',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");

    // Seed default admin hanya sekali saat tabel user masih kosong.
    // Jangan reset otomatis agar perubahan Master User (username/password) tetap tersimpan.
    $totalUsers = (int) $pdo->query("SELECT COUNT(*) FROM web_users")->fetchColumn();
    if ($totalUsers === 0) {
        $defaultPassword = password_hash('admin', PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO web_users (username, password, nama_lengkap, role) VALUES ('admin', ?, 'Administrator', 'admin')");
        $stmt->execute([$defaultPassword]);
    }

} catch(Exception $e) {
    die("Koneksi Database Gagal: " . $e->getMessage());
}