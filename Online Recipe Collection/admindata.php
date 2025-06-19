<?php
// Database connection parameters
$servername = "localhost:3307"; // Database server
$username = "root"; // Database username
$password = ""; // Database password
$dbname = "users"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin user data
$adminUsername = "ADMIN";
$adminEmail = "admin@example.com";
$adminPassword = "ABC"; // Plaintext password

// Hash the password
$hashedPassword = password_hash($adminPassword, PASSWORD_DEFAULT);

// Check if the admin already exists (based on email or username)
$checkUserSql = "SELECT * FROM admin WHERE email = '$adminEmail' OR username = '$adminUsername'";
$result = $conn->query($checkUserSql);

if ($result->num_rows > 0) {
    // If user already exists
    echo "Admin user already exists.";
} else {
    // Insert the new admin user into the database
    $insertSql = "INSERT INTO admin (username, email, password) VALUES ('$adminUsername', '$adminEmail', '$hashedPassword')";
    
    if ($conn->query($insertSql) === TRUE) {
        echo "Admin user created successfully!";
    } else {
        echo "Error: " . $insertSql . "<br>" . $conn->error;
    }
}

// Close the connection
$conn->close();
?>
