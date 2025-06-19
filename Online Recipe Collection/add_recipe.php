<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection
$host = 'localhost:3307';
$user = 'root';
$password = '';
$database = 'users';

// Create connection
$conn = new mysqli($host, $user, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize it
    $recipe_name = filter_input(INPUT_POST, 'recipe-name', FILTER_SANITIZE_STRING);
    $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_STRING);
    $ingredients = filter_input(INPUT_POST, 'ingredients', FILTER_SANITIZE_STRING);
    $recipe_instructions = filter_input(INPUT_POST, 'recipe', FILTER_SANITIZE_STRING);

    // Validate form fields
    if (empty($recipe_name) || empty($category) || empty($ingredients) || empty($recipe_instructions)) {
        die("All fields are required. Please fill in all details.<br>");
    }

    // Check if the recipe already exists with the same name and category
    $check_sql = "SELECT COUNT(*) FROM recipes WHERE recipe_name = ? AND category = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("ss", $recipe_name, $category);
    $check_stmt->execute();
    $check_stmt->bind_result($count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($count > 0) {
        die("A recipe with the same name and category already exists. Please choose a different name or category.");
    }

    // File upload handling
    if (isset($_FILES['food-image']) && $_FILES['food-image']['error'] == UPLOAD_ERR_OK) {
        $image_tmp_name = $_FILES['food-image']['tmp_name'];
        $image_name = $_FILES['food-image']['name'];
        $image_extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        // Check if file type is valid and image is actually an image
        if (in_array(strtolower($image_extension), $allowed_extensions) && getimagesize($image_tmp_name) !== false) {
            $upload_directory = "uploads/";

            // Ensure the 'uploads' directory exists
            if (!is_dir($upload_directory)) {
                mkdir($upload_directory, 0755, true);
            }

            // Generate unique file name
            $image_path = $upload_directory . uniqid() . "." . $image_extension;

            // Try moving the uploaded file to the 'uploads' directory
            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Prepare SQL query to insert recipe
                $sql = "INSERT INTO recipes (recipe_name, category, ingredients, recipe_instructions, food_image) 
                        VALUES (?, ?, ?, ?, ?)";

                $stmt = $conn->prepare($sql);
                if ($stmt) {
                    $stmt->bind_param("sssss", $recipe_name, $category, $ingredients, $recipe_instructions, $image_path);

                    // Execute the query
                    if ($stmt->execute()) {
                        echo "Recipe uploaded successfully!";
                    } else {
                        echo "Error: " . $stmt->error . "<br>";
                    }
                    $stmt->close();
                } else {
                    echo "Error preparing statement: " . $conn->error . "<br>";
                }
            } else {
                echo "Error moving the uploaded file. Please check directory permissions";
            }
        } else {
            echo "Invalid file type or file is not an image. Only JPG, PNG, and GIF are allowed.";
        }
    } else {
        // Display error if there is a problem with the file upload
        echo "Error code: " . $_FILES['food-image']['error'] . "<br>";
        if ($_FILES['food-image']['error'] == UPLOAD_ERR_INI_SIZE) {
            echo "File exceeds the upload_max_filesize in php.ini.<br>";
        } elseif ($_FILES['food-image']['error'] == UPLOAD_ERR_FORM_SIZE) {
            echo "File exceeds the MAX_FILE_SIZE directive in the HTML form.<br>";
        } elseif ($_FILES['food-image']['error'] == UPLOAD_ERR_PARTIAL) {
            echo "File was only partially uploaded.<br>";
        } elseif ($_FILES['food-image']['error'] == UPLOAD_ERR_NO_FILE) {
            echo "No file was uploaded.<br>";
        } elseif ($_FILES['food-image']['error'] == UPLOAD_ERR_NO_TMP_DIR) {
            echo "Missing temporary folder.<br>";
        } elseif ($_FILES['food-image']['error'] == UPLOAD_ERR_CANT_WRITE) {
            echo "Failed to write file to disk.<br>";
        } elseif ($_FILES['food-image']['error'] == UPLOAD_ERR_EXTENSION) {
            echo "A PHP extension stopped the file upload.<br>";
        }
    }
} else {
    echo "Invalid request method.<br>";
}

// Close database connection
$conn->close();
?>
