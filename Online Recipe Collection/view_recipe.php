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
    $stmt = $mysqli->query("SELECT recipe_id, recipe_name, category, ingredients, recipe_instructions, food_image FROM recipes WHERE recipe_name = '$recipe_name' LIMIT 1");
    $recipe_details = $stmt->fetch_assoc();

    if ($recipe_details) {
        // Base URL for images
        $imageBaseUrl = "http://localhost/online%20recipe%20collection/"; // URL to the 'uploads' directory

        // Check if the recipe has a food image; if not, set the fallback image URL
        if ($recipe_details['food_image']) {
            // Concatenate base URL and the food image directly (no need for encoding if slashes are part of the image path)
            $recipe_details['image_url'] = $imageBaseUrl . $recipe_details['food_image']; 
        } else {
            // Fallback image if no food image is found
            $recipe_details['image_url'] = $imageBaseUrl . "placeholder.png";
        }

        // Return the JSON response
        echo json_encode(["recipe" => $recipe_details]);
    } else {
        echo json_encode(["error" => "Recipe not found."]);
    }
}





    // Close the connection
    $mysqli->close();
} catch (Exception $e) {
    // Handle errors
    echo json_encode(["error" => "Database error occurred: " . $e->getMessage()]);
}
?>
