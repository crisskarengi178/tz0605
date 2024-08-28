<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname_admin = "admin_db";
$dbname_users = "users_db";

// Create connection to admin database
$conn_admin = new mysqli($servername, $username, $password, $dbname_admin);
if ($conn_admin->connect_error) {
    die("Connection failed: " . $conn_admin->connect_error);
}

// Create connection to users database
$conn_users = new mysqli($servername, $username, $password, $dbname_users);
if ($conn_users->connect_error) {
    die("Connection failed: " . $conn_users->connect_error);
}
?>
