<?php
include('db.php');
session_start(); // Start the session

header('Content-Type: application/json'); // Ensure JSON response

$input = file_get_contents('php://input');
$data = json_decode($input, true);

// Validate input
if (!$data || !isset($data['prodID'])) {
    echo json_encode(["success" => false, "message" => "Invalid input"]);
    exit;
}

$prodID = $data['prodID'];

// Prepare SQL query
$sql = "SELECT current_quant from product WHERE product_id = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["success" => false, "message" => "Database query failed"]);
    exit;
}

$stmt->bind_param("s",$prodID);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo json_encode(["success" => true, "items" => [ $row ]]); // Wrap in an array
} else {
    echo json_encode(["success" => false, "message" => "Product not found"]);
}

$stmt->close();
$conn->close();
?>
