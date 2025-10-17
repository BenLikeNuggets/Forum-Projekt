<?php
// === DB Connection ===
$servername = "mysql36.unoeuro.com";
$username = "etaesel_dk";
$password = "x52Bgmzc9kerG6FE3tfp";
$dbname = "etaesel_dk_db_Filmforum";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}