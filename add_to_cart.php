<?php
session_start(); // Start the session

// -------------------------------
// Database configuration
// -------------------------------
$servername = "192.168.56.101"; // Your MySQL server IP address
$username   = "root";           // Your MySQL username
$password   = "a";              // Your MySQL password
$dbname     = "ecommerce";      // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// -------------------------------
// Get the current session ID
// -------------------------------
$userSessionId = session_id();

// -------------------------------
// Retrieve product data from JSON input
// -------------------------------
$input = file_get_contents('php://input');
$data  = json_decode($input, true);

// Ensure required product data is provided
if (
    !isset($data['prodID']) ||
    !isset($data['prodName']) ||
    !isset($data['price'])   ||
    !isset($data['quantity'])
) {
    echo json_encode(["success" => false, "error" => "Missing product data"]);
    exit();
}
$userId = $data['userId'];
$prodID   = $data['prodID'];
$prodName = $data['prodName'];
$price    = $data['price'];
$quantity = $data['quantity'];

// -------------------------------
// Fetch the user ID from the users table using the session ID
// -------------------------------
// -------------------------------
// Insert the item into the cart table
// -------------------------------
$stmtCart = $conn->prepare("INSERT INTO cart (user_id, product_id, product_name, quantity, price, session_id) VALUES (?, ?, ?, ?, ?, ?)");
if (!$stmtCart) {
    echo json_encode(["success" => false, "error" => "Cart query prepare failed: " . $conn->error]);
    exit();
}

// Bind parameters: 
// "i" for user_id (integer),
// "s" for product_id (string),
// "s" for product_name (string),
// "i" for quantity (integer),
// "d" for price (decimal/double),
// "s" for session_id (string)
$stmtCart->bind_param("issids", $userId, $prodID, $prodName, $quantity, $price, $userSessionId);

if ($stmtCart->execute()) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "error" => $stmtCart->error]);
}

$stmtCart->close();
$conn->close();

// -------------------------------
// Destroy the session so that a new session ID is generated for the next user
// -------------------------------
$_SESSION = []; // Clear session variables

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    // Delete the session cookie by setting its expiration time to the past.
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

?>
