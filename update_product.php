<?php
include('db.php');
session_start(); // Start the session

header('Content-Type: application/json'); // Ensure JSON response

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!$data || !isset($data['prodID']) || !isset($data['current_quant'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$prodID = $data['prodID'];
$current_quant = $data['current_quant'];

// Prepare SQL query
$sql = "UPDATE product SET current_quant = ? WHERE product_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database query failed"]);
    exit;
}

$stmt->bind_param("is", $current_quant, $prodID);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => true, "message" => "Product updated successfully"]);
} else {
    echo json_encode(["success" => false, "message" => "Product update failed or no changes made"]);
}

$stmt->close();
$conn->close();
?>
