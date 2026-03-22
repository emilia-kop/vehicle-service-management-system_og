<?php
define('DB_HOST', 'MYSQLHOST value here');
define('DB_USER', 'MYSQLUSER value here');
define('DB_PASS', 'MYSQLPASSWORD value here');
define('DB_NAME', 'MYSQLDATABASE value here');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, 3306);
if ($conn->connect_error) {
    die('<div class="alert alert-danger">
            <strong>Database Connection Failed:</strong> ' .
            htmlspecialchars($conn->connect_error) .
         '</div>');
}
$conn->set_charset('utf8mb4');
