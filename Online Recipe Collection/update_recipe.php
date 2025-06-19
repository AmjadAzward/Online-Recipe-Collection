<?php
// Database connection
$host = "localhost:3307";
$dbname = "users";
$username = "root";
$password = "";
$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]));
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Fetch input data
    $originalRecipeName = $_POST['original-recipe-name'];
    $recipeName = $_POST['recipe_name'];
    $ingredients = $_POST['ingredients'];
    $recipeInstructions = $_POST['recipe_instructions'];
    $category = $_POST['category'];

    // Validate recipe name
    if (empty($recipeName)) {
        echo json_encode(['success' => false, 'message' => 'Recipe name is required.']);
        exit();
    }

    // Check if the original recipe exists
    $stmt = $conn->prepare("SELECT * FROM recipes WHERE recipe_name = ?");
    $stmt->bind_param("s", $originalRecipeName);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Original recipe not found.']);
        exit();
    }

    // File upload logic
    $image = null;
    if (isset($_FILES['food_image']) && $_FILES['food_image']['error'] == UPLOAD_ERR_OK) {
        $image_tmp_name = $_FILES['food_image']['tmp_name'];
        $image_name = $_FILES['food_image']['name'];
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array(strtolower($image_extension), $allowed_extensions) && getimagesize($image_tmp_name) !== false) {
            $upload_directory = "uploads/";
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0755, true);
            }
            $image_path = $upload_directory . uniqid() . "." . $image_extension;
            if (move_uploaded_file($image_tmp_name, $image_path)) {
                $image = $image_path;
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to upload the file.']);
                exit();
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid image type.']);
            exit();
        }
    } else {
        $row = $result->fetch_assoc();
        $image = $row['food_image']; // Use existing image if no new file is uploaded
    }

    // Update recipe in database
    $sql = "UPDATE recipes SET 
                recipe_name = ?, 
                ingredients = ?, 
                recipe_instructions = ?, 
                category = ?, 
                food_image = ? 
            WHERE recipe_name = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt === false) {
        echo json_encode(['success' => false, 'message' => 'Query preparation failed.']);
        exit();
    }

    $stmt->bind_param("ssssss", $recipeName, $ingredients, $recipeInstructions, $category, $image, $originalRecipeName);

    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Recipe updated successfully!']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Failed to update recipe.']);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
