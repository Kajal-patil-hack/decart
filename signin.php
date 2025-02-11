<?php
include('db.php'); // Include your database connection file

session_start(); // Start a session to store user information after login

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get values from the login form
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare the SQL statement to check if the email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email); // "s" means a single string parameter

    $stmt->execute();
    $result = $stmt->get_result();

    // If the user exists, check the password
    if ($result->num_rows > 0) { 
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['username'] = $user['username'];
            $_SESSION['email'] = $user['email'];

            // Redirect to home page
            header("Location: home.html");
            exit(); // <---- IMPORTANT: Prevent further script execution
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with this email.";
    }
    
    // Close the statement
    $stmt->close();

    // Close the database connection
    $conn->close();
}
?>
