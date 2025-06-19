<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection settings
$host = 'localhost:3307';  // Database host (usually localhost)
$username = 'root';  // Database username
$password = '';  // Database password
$dbname = 'users';  // Database name

// Create a connection using mysqli
$conn = new mysqli($host, $username, $password, $dbname);

// Check for a connection error
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed: ' . $conn->connect_error]);
    exit();
}

// Check if the feedback is set in the POST request
if (isset($_POST['feedback'])) {
    $feedback = $_POST['feedback'];

    // Prepare the SQL query to insert the feedback
    $sql = "INSERT INTO feedback (feedback) VALUES (?)";

    // Prepare the statement
    if ($stmt = $conn->prepare($sql)) {
        // Bind the parameter
        $stmt->bind_param("s", $feedback);

        // Execute the query
        if ($stmt->execute()) {
            echo json_encode(['success' => 'Feedback submitted successfully!']);
        } else {
            echo json_encode(['error' => 'Error submitting feedback: ' . $stmt->error]);
        }

        // Close the statement
        $stmt->close();
    } else {
        echo json_encode(['error' => 'Error preparing the statement: ' . $conn->error]);
    }
} else {
    echo json_encode(['error' => 'Feedback is not set.']);
}

// Close the connection
$conn->close();
?>
