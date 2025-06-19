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


// Assuming database connection is already established
// Assuming database connection is already established

if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_GET['action'] == 'removeRecipe') {
    // Get the recipe name from the request body
    $data = json_decode(file_get_contents("php://input"), true);
    $recipe_name = $data['recipe_name'];

    // Query to delete the recipe from the database
    $query = "DELETE FROM recipes WHERE recipe_name = ?";
    if ($stmt = $mysqli->prepare($query)) {
        $stmt->bind_param('s', $recipe_name);
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'error' => 'Failed to remove recipe.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error.']);
    }
}


    // Close the connection
    $mysqli->close();
} catch (Exception $e) {
    // Handle errors
    echo json_encode(["error" => "Database error occurred: " . $e->getMessage()]);
}








// Update recipe details, including recipe name
if (isset($_GET['action']) && $_GET['action'] == 'updateRecipe' && isset($_POST['recipe_name'])) {
    $oldRecipeName = $mysqli->real_escape_string($_POST['old_recipe_name']); // Old recipe name (the one to be changed)
    $newRecipeName = $mysqli->real_escape_string($_POST['recipe_name']); // New recipe name
    $category = $mysqli->real_escape_string($_POST['category']);
    $ingredients = $mysqli->real_escape_string($_POST['ingredients']);
    $recipe_instructions = $mysqli->real_escape_string($_POST['recipe_instructions']);
    $image_url = isset($_POST['image_url']) ? $mysqli->real_escape_string($_POST['image_url']) : null;

    // SQL query to update the recipe, including recipe name change
    $updateQuery = "UPDATE recipes 
                    SET recipe_name = '$newRecipeName', 
                        category = '$category', 
                        ingredients = '$ingredients', 
                        recipe_instructions = '$recipe_instructions', 
                        food_image = '$image_url' 
                    WHERE recipe_name = '$oldRecipeName'";

    if ($mysqli->query($updateQuery) === TRUE) {
        echo json_encode(["message" => "Recipe updated successfully"]);
    } else {
        echo json_encode(["error" => "Error updating recipe: " . $mysqli->error]);
    }
}













?>
