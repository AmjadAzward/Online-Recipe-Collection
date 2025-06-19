<?php
// adminlogin.php

// Start session to manage login status
session_start();

// Database connection
$servername = "localhost:3307"; // Replace with your database server
$username = "root"; // Replace with your database username
$password = ""; // Replace with your database password
$dbname = "users"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted via AJAX
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize the input data to prevent SQL injection
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // SQL query to check if the username and password match
    $sql = "SELECT * FROM users WHERE username='$username'"; // Assume you have a table 'users' for storing user data
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User exists, fetch the data
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // If password matches, start the session and login
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            echo "success";
        } else {
            echo "Invalid password. Please try again.";
        }
    } else {
        echo "No user found with that username.";
    }

    $conn->close();
}
?>
