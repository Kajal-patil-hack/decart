<?php
session_start(); // Start the session

$servername = "192.168.56.101"; // Your MySQL server IP address
$username = "root"; // Your MySQL username
$password = "a"; // Your MySQL password
$dbname = "ecommerce"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the session ID (used to uniquely identify the user)
$userSessionId = session_id();

// Query the database to get items for the current session
$sql = "SELECT guitar_name, price FROM cart WHERE session_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $userSessionId); // Bind the session ID parameter
$stmt->execute();
$result = $stmt->get_result();

$cartItems = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $cartItems[] = $row;
    }
    echo json_encode(["success" => true, "items" => $cartItems]);
} else {
    echo json_encode(["success" => false, "message" => "No items in cart"]);
}

$stmt->close();
$conn->close();
?>

