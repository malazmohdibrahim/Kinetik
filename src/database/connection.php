<?php
// Secure Database Connection Module using PDO
$host = 'sql202.infinityfree.com'; // Matches the MySQL service name inside your docker-compose.yml
$db   = 'if0_42187554_init';
$user = 'if0_42187554';
$pass = 'malaz321';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
     $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
     // Elegant fallback error layout matching our luxury dark-mode aesthetics
     echo "<div style='background:#070708; color:#e11d48; padding:40px; font-family:sans-serif; text-align:center; min-height:100vh; display:flex; flex-direction:column; justify-content:center; align-items:center;'>";
     echo "<h2 style='letter-spacing:3px; font-weight:900; margin-bottom:10px;'>KINETIK CORE OFFLINE</h2>";
     echo "<p style='color:#9ca3af; max-width:500px; line-height:1.6;'>The high-performance database cluster is initializing or calibrating dependencies. Systems down safely.</p>";
     echo "<span style='color:#374151; font-size:11px; margin-top:20px; font-family:monospace;'>Matrix Link Exception: " . htmlspecialchars($e->getMessage()) . "</span>";
     echo "</div>";
     exit;
}
?>