<?php
// Database connection
$host = "localhost:3307"; // Database host
$dbname = "users"; // Database name
$username = "root"; // Database username
$password = ""; // Database password

$conn = new mysqli($host, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query to fetch feedbacks (only ID and Feedback)
$sql = "SELECT feedback_id, feedback FROM feedback"; // Corrected table name from 'feedback' to 'feedbacks'
$result = $conn->query($sql);

$feedback = [];
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $feedback[] = $row;
    }
}

// Return feedbacks as JSON
echo json_encode(["success" => true, "feedback" => $feedback]); // Corrected key name to 'feedbacks'

$conn->close();
?>
