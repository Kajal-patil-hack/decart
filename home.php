
<?php
// Initialize message variable
$message = "";

// Database connection parameters
$servername = "192.168.56.101";  // Your database server, change if necessary
$username = "root";         // Your database username
$password = "a";             // Your database password
$dbname = "ecommerce";      // Your database name

// Create database connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and assign form inputs
    $name = htmlspecialchars($_POST['name']);
    $email = htmlspecialchars($_POST['email']);
    $message_content = htmlspecialchars($_POST['message']);

    // Get the current date and time for 'submitted_at'
    $submitted_at = date("Y-m-d H:i:s");

    // SQL query to insert form data into 'contact_form' table
    $sql = "INSERT INTO contact_form (name, email, message, submitted_at)
            VALUES ('$name', '$email', '$message_content', '$submitted_at')";

    // Execute the query
    if ($conn->query($sql) === TRUE) {
        // If the query is successful, set the message
        $message = "Thank you, $name! Your message has been received.";
    } else {
        // If the query fails, set the error message
        $message = "Error: " . $sql . "<br>" . $conn->error;
    }

    // Close the connection
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4; /* New background color */
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin: 0 auto;
            padding: 30px;
            background-color: #fff;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
            margin-top: 100px;
            animation: fadeIn 1.5s ease-out;
        }

        h2 {
            text-align: center;
            color: #333;
            font-size: 2em;
            margin-bottom: 20px;
            text-transform: uppercase;
        }

        label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
            color: #333;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 2px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        input[type="text"]:focus,
        input[type="email"]:focus,
        textarea:focus {
            border-color: #6e7fdb;
            outline: none;
        }

        input[type="submit"] {
            width: 100%;
            padding: 15px;
            background-color: #f8c501; /* Yellow button */
            color: #fff;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #f7b500; /* Slightly darker yellow on hover */
        }

        .message {
            text-align: center;
            font-size: 18px;
            color: #28a745;
            margin-top: 20px;
        }

        .form-row {
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .form-row input,
        .form-row textarea {
            flex-grow: 1;
        }

        textarea {
            min-height: 150px;
        }

        /* Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

    </style>
</head>
<body>

<div class="container">

<h2>Contact Us</h2>

<?php
// Display the thank you message if the form is submitted
if ($message != "") {
    echo "<p class='message'>$message</p>";
} else {
    // Display the form if not submitted
    ?>
    <form action="home.php" method="POST">
        <div class="form-row">
            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>
        </div>

        <div class="form-row">
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
        </div>

        <div class="form-row">
            <label for="message">Message:</label>
            <textarea id="message" name="message" required></textarea>
        </div>

        <input type="submit" value="Submit">
    </form>
    <?php
}
?>

</div>

</body>
</html>
