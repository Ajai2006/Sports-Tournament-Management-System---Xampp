<?php
// Database Configuration - Update these values as per your XAMPP setup
define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // Default XAMPP MySQL username
define('DB_PASS', '');           // Default XAMPP MySQL password (empty)
define('DB_NAME', 'sports_tournament');

// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check connection
if ($conn->connect_error) {
    die('<div style="font-family:sans-serif;padding:40px;background:#fff0f0;border:2px solid #e55;border-radius:12px;margin:40px auto;max-width:600px;">
        <h2 style="color:#c00;">⚠️ Database Connection Failed</h2>
        <p><strong>Error:</strong> ' . $conn->connect_error . '</p>
        <ol>
            <li>Make sure XAMPP is running (Apache + MySQL)</li>
            <li>Open phpMyAdmin → Import → Select <code>database.sql</code></li>
            <li>Check your credentials in <code>includes/config.php</code></li>
        </ol>
    </div>');
}

$conn->set_charset("utf8mb4");

// Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Utility functions
function sanitize($conn, $input) {
    return $conn->real_escape_string(htmlspecialchars(strip_tags(trim($input))));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isAdmin() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

function requireAdmin() {
    if (!isAdmin()) {
        redirect('../admin/login.php');
    }
}

function timeAgo($datetime) {
    $diff = time() - strtotime($datetime);
    if ($diff < 60) return 'Just now';
    if ($diff < 3600) return floor($diff/60) . 'm ago';
    if ($diff < 86400) return floor($diff/3600) . 'h ago';
    return date('M d, Y', strtotime($datetime));
}
?>
