<?php
session_start(); // Start or resume the session

// -------------------------------
// Database configuration
// -------------------------------
$servername = "192.168.56.101"; // Your MySQL server IP address
$username   = "root";           // Your MySQL username
$password   = "a";              // Your MySQL password
$dbname     = "ecommerce";      // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// -------------------------------
// Check if the email is set in the session
// -------------------------------
if (!isset($_SESSION['email'])) {
    echo json_encode([
        "success" => false, 
        "error" => "No email found in session. User is not logged in."
    ]);
    exit();
}

// -------------------------------
// Fetch the user data from the users table using the session email
// -------------------------------
$stmt = $conn->prepare("SELECT Id, session_id FROM users WHERE email = ?");
if (!$stmt) {
    echo json_encode([
        "success" => false, 
        "error" => "User query prepare failed: " . $conn->error
    ]);
    exit();
}

$stmt->bind_param("s", $_SESSION['email']);
$stmt->execute();
$result = $stmt->get_result();

$userData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $userData[] = $row;
    }
    echo json_encode([
        "success" => true, 
        "users" => $userData
    ]);
} else {
    echo json_encode([
        "success" => false, 
        "error" => "No user found for session"
    ]);
}

$stmt->close();
$conn->close();

// -------------------------------
// (Optional) Destroy the session so that a new session ID is generated for the next user
// -------------------------------
// Uncomment the following lines if you wish to clear the session after retrieving data

/*
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();
*/
?>
