<?php
// Database connection
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

$response = array('success' => false, 'message' => ''); // Initialize response array

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $confirmPassword = mysqli_real_escape_string($conn, $_POST['confirm-password']);
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $response['message'] = "Invalid email format!";
    }
    
    // Validate password match
    elseif ($password !== $confirmPassword) {
        $response['message'] = "Passwords do not match!";
    }
    
    // Check if fields are empty
    elseif (empty($username) || empty($email) || empty($password)) {
        $response['message'] = "All fields are required!";
    } else {
        // Check if the email already exists in the database
        $checkEmail = "SELECT * FROM users WHERE email = '$email'";
        $checkUsername = "SELECT * FROM users WHERE username = '$username'"; // Check for username uniqueness
        $resultEmail = $conn->query($checkEmail);
        $resultUsername = $conn->query($checkUsername);

        if ($resultEmail->num_rows > 0) {
            $response['message'] = "This email is already registered. Please use a different email.";
        } elseif ($resultUsername->num_rows > 0) {
            $response['message'] = "This username is already taken. Please choose a different username.";
        } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Prepare SQL query to insert data
            $sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$hashedPassword')";
            
            // Execute query and check if successful
            if ($conn->query($sql) === TRUE) {
                $response['success'] = true;
                $response['message'] = "Account created successfully!";
            } else {
                $response['message'] = "Error: " . $conn->error;
            }
        }
    }
}

// Close connection
$conn->close();

// Return response as JSON
echo json_encode($response);
?>
