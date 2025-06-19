// Function to populate the recipe category select options
function populateCategoryOptions() {
    const categorySelect = document.getElementById("recipe-category");

    // Make a request to fetch categories from the database
    fetch('view_recipe.php?action=getCategories') // PHP script to fetch categories
        .then(response => response.json())
        .then(data => {
            if (data.categories) {
                // Clear existing options
                categorySelect.innerHTML = '';

                // Add a default "Select a category" option
                const defaultOption = document.createElement('option');
                defaultOption.value = '';
                defaultOption.textContent = 'Select a category';
                categorySelect.appendChild(defaultOption);

                // Populate select options with fetched categories
                data.categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category.category;
                    option.textContent = category.category;
                    categorySelect.appendChild(option);
                });
            } else {
                console.error('Error fetching categories:', data.error);
            }
        })
        .catch(error => console.error('Error:', error));
}

// Function to filter recipes based on selected category
function filterRecipes() {
    const category = document.getElementById("recipe-category").value; // Get selected category
    const recipeList = document.getElementById("recipe-list");

    // Clear previous recipes
    recipeList.innerHTML = '';

    if (category) {
        // Make a request to fetch recipes for the selected category
        fetch(`view_recipe.php?action=getRecipes&category=${category}`)
            .then(response => response.json())
            .then(data => {
                console.log('Fetched recipes:', data); // Log fetched recipes for debugging
                if (data.recipes) {
                    // Remove any existing event listeners
                    const newRecipeList = recipeList.cloneNode(true);
                    recipeList.parentNode.replaceChild(newRecipeList, recipeList);

                    // Populate recipe list with fetched recipes
                    data.recipes.forEach(recipe => {
                        const option = document.createElement('option');
                        option.value = recipe.recipe_name; // The value to be sent when a recipe is selected
                        option.textContent = recipe.recipe_name; // The name displayed in the list
                        newRecipeList.appendChild(option);
                    });

                    // Add an event listener to the <select> element (recipeList) for the 'change' event
                    newRecipeList.addEventListener('change', function () {
                        const selectedRecipeName = newRecipeList.value; // Get the selected recipe name
                        if (selectedRecipeName) {
                            console.log('Selected recipe:', selectedRecipeName); // For debugging
                            loadRecipeDetails(selectedRecipeName); // Load recipe details for the selected recipe
                        }
                    });

                } else {
                    console.error('Error fetching recipes:', data.error); // Log error if no recipes returned
                }
            })
            .catch(error => console.error('Error:', error)); // Log any network or fetch errors
    }
}

// Function to load the recipe details
function loadRecipeDetails(recipeName) {
    if (recipeName) {
        fetch(`view_recipe.php?action=getRecipeDetails&recipe_name=${recipeName}`)
            .then(response => response.json())
            .then(data => {
                console.log("API Response:", data);

                if (data.recipe) {
                    // Update the form fields
                    document.getElementById("recipe-name").value = data.recipe.recipe_name;
                    document.getElementById("category").value = data.recipe.category;
                    document.getElementById("ingredients").value = data.recipe.ingredients;
                    document.getElementById("recipe").value = data.recipe.recipe_instructions;

                    // Handle the image
                    const imagePreview = document.getElementById("image-preview");

                    // Use the image URL from the API response
                    const imageUrl = data.recipe.image_url || "http://localhost/online%20recipe%20collection/placeholder.png"; // Fallback image

                    console.log("Image URL:", imageUrl);

                    // Debugging for image load
                    imagePreview.onload = () => console.log("Image loaded successfully.");
                    imagePreview.onerror = () => {
                        console.error("Failed to load the image. Setting fallback image.");
                        imagePreview.src = "C:\\Users\\amjad\\Documents\\WhatsApp Image 2024-11-18 at 12.47.58_56418b1b.jpg"; // Fallback image
                        imagePreview.alt = "";
                    };

                    // Set the image source and alt text
                    imagePreview.src = imageUrl;
                    imagePreview.alt = data.recipe.recipe_name ? `${data.recipe.recipe_name} Image` : "Recipe Image";
                } else {
                    console.error("Error fetching recipe details:", data.error);
                }
            })
            .catch(error => console.error("Error fetching data:", error));
    } else {
        console.warn("No recipe name provided.");
    }
}

// Call the function to populate categories on page load
window.onload = function () {
    populateCategoryOptions();
    document.getElementById("recipe-category").addEventListener("change", filterRecipes);
};

// Handle image preview when the user uploads an image
document.getElementById("recipe-image").addEventListener("change", function (e) {
    var reader = new FileReader();
    reader.onload = function (event) {
        document.getElementById("image-preview").src = event.target.result;
    };
    reader.readAsDataURL(e.target.files[0]);
});
