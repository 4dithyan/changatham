<?php
$servername = "localhost";
$username   = "root";   // change if different
$password   = "";       // change if different
$database   = "changatham";

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset
$conn->set_charset("utf8");
?>