<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "taskpulse_db";

// Create connection
$conn = mysqli_connect($host, $username, $password, $dbname);

// Check connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>