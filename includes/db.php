<?php
define('DB_HOST', 'mysql.railway.internal');
define('DB_USER', 'root');
define('DB_PASS','rPrIoxmKYwOsKyMrjhNqBHpVVqZQxlLj');
define('DB_NAME', 'railway');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306);
if ($conn->connect_error) {
    die('<div class="alert alert-danger">
            <strong>Database Connection Failed:</strong> ' .
            htmlspecialchars($conn->connect_error) .
         '</div>');
}
$conn->set_charset('utf8mb4');
