<?php
// includes/db.php  –  Database connection
// Change these values to match your XAMPP / MySQL setup

define('DB_HOST', 'localhost');
define('DB_USER', 'root');       // default XAMPP user
define('DB_PASS', '');           // default XAMPP password (empty)
define('DB_NAME', 'vsms');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    die('<div class="alert alert-danger">
            <strong>Database Connection Failed:</strong> ' .
            htmlspecialchars($conn->connect_error) .
         '</div>');
}

$conn->set_charset('utf8mb4');
