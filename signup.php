<?php
session_start(); // Start the session
include('db.php'); // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $userSessionId = session_id();

    // Check if the passwords match
    if ($password === $confirm_password) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO users (username, email, password,session_id) VALUES (?, ?, ?,?)");
        $stmt->bind_param("ssss", $username, $email, $hashed_password,$userSessionId); // "sss" -> 3 strings

        // Execute the query and check if it succeeds
        if ($stmt->execute()) {
            $_SESSION['email'] = $email; // Set session after successful signup
            header("Location: signin.html"); // Redirect to home.html
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }

        // Close the statement
        $stmt->close();
    } else {
        echo "Passwords do not match!";
    }

    // Close the database connection
    $conn->close();
}
?>
