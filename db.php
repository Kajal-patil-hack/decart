<?php
$servername = "192.168.56.101"; // IP address of the MySQL server
$username = "root";            // MySQL username
$password = "a";   // MySQL password
$dbname = "ecommerce";         // Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
