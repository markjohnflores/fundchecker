<?php
session_start();

$host = 'localhost';
$dbname = 'school_ledger';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $stmt = $pdo->query("SELECT COUNT(*) FROM users");
    if ($stmt->fetchColumn() == 0) {
        $hash = password_hash('123456', PASSWORD_DEFAULT);
        $pdo->exec("INSERT INTO users (username, password, full_name, role) VALUES ('superadmin', '$hash', 'System Super Admin', 'SUPER_ADMIN')");
        $pdo->exec("INSERT INTO users (username, password, full_name, role) VALUES ('admin', '$hash', 'Mia Reyes', 'ADMIN')");
        $pdo->exec("INSERT INTO users (username, password, full_name, role) VALUES ('user', '$hash', 'Guest Student', 'USER')");
    }
} catch(PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function checkAccess($allowed_roles) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
    if (!in_array($_SESSION['role'], $allowed_roles)) {
        header("Location: overview.php");
        exit;
    }
}
?>