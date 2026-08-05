<?php
// Shared database connection — every script below does require 'db_config.php';
// XAMPP defaults: user "root", empty password.
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "axiommath_db";

$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
