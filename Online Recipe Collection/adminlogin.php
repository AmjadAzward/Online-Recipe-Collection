<?php
session_start();

// Database connection parameters
$servername = "localhost:3307";
$username = "root";
$password = ""; // Database password
$dbname = "users"; // Database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize response array
$response = ['success' => false, 'message' => ''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user = $_POST['username'];
    $pass = $_POST['password'];

    // SQL query to fetch user data from the database
    $sql = "SELECT * FROM admin WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // User found
        $row = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($pass, $row['password'])) {
            // Password is correct
            $_SESSION['username'] = $user;
            $response['success'] = true; // Successful login
        } else {
            $response['message'] = "Invalid password.";
        }
    } else {
        $response['message'] = "No user found with this username.";
    }

    echo json_encode($response); // Return JSON response
    exit;
}

// Close connection
$conn->close();
?>
