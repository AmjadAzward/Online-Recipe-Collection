<?php
// Database configuration
$host = 'localhost:3307';
$username = 'root';
$password = '';
$dbname = 'users';

try {
    // Create a MySQLi connection
    $mysqli = new mysqli($host, $username, $password, $dbname);

    // Check connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    // Fetch categories
    if (isset($_GET['action']) && $_GET['action'] == 'getCategories') {
        $stmt = $mysqli->query("SELECT DISTINCT category FROM recipes");
        $categories = [];
        while ($row = $stmt->fetch_assoc()) {
            $categories[] = $row;
        }

        if (!empty($categories)) {
            echo json_encode(["categories" => $categories]);
        } else {
            echo json_encode(["error" => "No categories found."]);
        }
    }

    // Fetch recipes based on category
    if (isset($_GET['action']) && $_GET['action'] == 'getRecipes' && isset($_GET['category'])) {
        $category = $mysqli->real_escape_string($_GET['category']);
        $stmt = $mysqli->query("SELECT recipe_name FROM recipes WHERE category = '$category'");
        $recipes = [];
        while ($row = $stmt->fetch_assoc()) {
            $recipes[] = $row;
        }

        if (!empty($recipes)) {
            echo json_encode(["recipes" => $recipes]);
        } else {
            echo json_encode(["error" => "No recipes found for this category."]);
        }
    }

// Fetch recipe details by recipe name
if (isset($_GET['action']) && $_GET['action'] === 'getRecipeDetails' && isset($_GET['recipe_name'])) {
    $recipe_name = $mysqli->real_escape_string($_GET['recipe_name']);
    
    // Query to fetch recipe details
    $stmt = $mysqli->query("SELECT recipe_id, recipe_name, category, ingredients, recipe_instructions, food_image FROM recipes WHERE recipe_name = '$recipe_name' LIMIT 1");
    $recipe_details = $stmt->fetch_assoc();

    if ($recipe_details) {
        // Base URL for images
        $imageBaseUrl = "http://localhost/online%20recipe%20collection/uploads/"; // URL to the 'uploads' directory

        // Check if the recipe has a food image
        if ($recipe_details['food_image']) {
            // Concatenate base URL and the food image directly
            $recipe_details['image_url'] = $imageBaseUrl . $recipe_details['food_image'];
        } else {
            // Fallback image if no food image is found
            $recipe_details['image_url'] = $imageBaseUrl . "placeholder.png";
        }

        // Return the JSON response with the recipe details
        echo json_encode(["recipe" => $recipe_details]);
    } else {
        echo json_encode(["error" => "Recipe not found."]);
    }
}









if (isset($_POST['update'])) {
    // Get the form data
    $recipe_id = $_POST['recipe_id'];
    $recipe_name = $mysqli->real_escape_string($_POST['recipe_name']);
    $ingredients = $mysqli->real_escape_string($_POST['ingredients']);
    $recipe_instructions = $mysqli->real_escape_string($_POST['recipe_instructions']);
    
    // Check if an image has been uploaded
    if ($_FILES['food_image']['name']) {
        // Handle image upload
        $food_image = $_FILES['food_image']['name'];
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($food_image);

        // Check if the file is an image
        $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        if (getimagesize($_FILES['food_image']['tmp_name']) !== false) {
            // Move the uploaded image to the target directory
            if (move_uploaded_file($_FILES['food_image']['tmp_name'], $target_file)) {
                $image_uploaded = true;
            } else {
                echo json_encode(["error" => "Sorry, there was an error uploading your file."]);
                exit;
            }
        } else {
            echo json_encode(["error" => "File is not an image."]);
            exit;
        }
    } else {
        // If no new image is uploaded, use the old one
        $food_image = $_POST['existing_image'];  // Assuming existing image is passed as hidden field
    }

    // Update the recipe in the database
    $stmt = $mysqli->prepare("UPDATE recipes SET recipe_name = ?, ingredients = ?, recipe_instructions = ?, food_image = ? WHERE recipe_id = ?");
    $stmt->bind_param('ssssi', $recipe_name, $ingredients, $recipe_instructions, $food_image, $recipe_id);

    if ($stmt->execute()) {
        echo json_encode(["success" => "Recipe updated successfully!"]);
    } else {
        echo json_encode(["error" => "Error: " . $stmt->error]);
    }

    // Close the statement
    $stmt->close();
} else {
    echo json_encode(["error" => "Invalid request method."]);
}








 // Close the connection
} catch (Exception $e) {
    // Handle errors
    echo json_encode(["error" => "Database error occurred: " . $e->getMessage()]);
}







?>
