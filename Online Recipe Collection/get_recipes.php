<?php
// Database connection
$servername = "localhost:3307";
$username = "root";
$password = "";
$dbname = "users";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Modify the query to select only appetizers
$sql = "SELECT recipe_id, recipe_name, category, ingredients, recipe_instructions, food_image FROM recipes";
$result = $conn->query($sql);

$recipes = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $recipes[] = $row;
    }
}

$conn->close();

// Return the recipes as a JSON response
header('Content-Type: application/json');
echo json_encode($recipes);
?>
