// Function to populate the recipe category select options
function populateCategoryOptions() {
    const categorySelect = document.getElementById("recipe-category");

    // Make a request to fetch categories from the database
    fetch('removerecipe.php?action=getCategories') // PHP script to fetch categories
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

// Function to filter recipes based on the selected category
function filterRecipes() {
    const category = document.getElementById("recipe-category").value; // Get selected category
    const recipeList = document.getElementById("recipe-list");

    // If category is selected, fetch recipes for that category
    if (category) {
        fetch(`removerecipe.php?action=getRecipes&category=${category}`)
            .then(response => response.json())
            .then(data => {
                console.log('Fetched recipes:', data); // Log fetched recipes for debugging
                if (data.recipes) {
                    // Clear previous recipes in the list
                    recipeList.innerHTML = '';

                    // Populate recipe list with fetched recipes
                    data.recipes.forEach(recipe => {
                        const option = document.createElement('option');
                        option.value = recipe.recipe_name; // The value to be sent when a recipe is selected
                        option.textContent = recipe.recipe_name; // The name displayed in the list
                        
                        // Add option to the list
                        recipeList.appendChild(option);
                    });
                } else {
                    console.error('Error fetching recipes:', data.error); // Log error if no recipes returned
                }
            })
            .catch(error => console.error('Error:', error)); // Log any network or fetch errors
    }
}

// Ensure only one change listener is added
function setupRecipeListListener() {
    const recipeList = document.getElementById("recipe-list");
    recipeList.removeEventListener('change', handleRecipeSelection); // Remove previous listener if it exists
    recipeList.addEventListener('change', handleRecipeSelection); // Add the event listener
}

// Function to handle recipe selection
function handleRecipeSelection(event) {
    const selectedRecipeName = event.target.value; // Get the selected recipe name
    if (selectedRecipeName) {
        console.log('Selected recipe:', selectedRecipeName); // For debugging
        loadRecipeDetails(selectedRecipeName); // Load recipe details for the selected recipe
    }
}

// Set up listeners once
document.addEventListener('DOMContentLoaded', () => {
    const categoryDropdown = document.getElementById("recipe-category");
    categoryDropdown.addEventListener('change', () => {
        filterRecipes();
        setupRecipeListListener(); // Ensure recipe list listener is set up
    });
});

// Function to load the recipe details
function loadRecipeDetails(recipeName) {
    if (recipeName) {
        fetch(`view_recipe.php?action=getRecipeDetails&recipe_name=${recipeName}`)
            .then(response => response.json())
            .then(data => {
                console.log("API Response:", data);

                if (data.recipe) {
                    // Update the form fields
                    document.getElementById("original-recipe-name").value = data.recipe.recipe_name;

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
                        imagePreview.src = "http://localhost/online%20recipe%20collection/placeholder.png"; // Fallback image
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





// Image preview when file is selected
document.getElementById('recipe-image').addEventListener('change', function() {
    const file = this.files[0];
    const reader = new FileReader();

    reader.onload = function(e) {
        document.getElementById('image-preview').src = e.target.result;
    };

    if (file) {
        reader.readAsDataURL(file);
    }
});




function updateRecipe() {
    var originalRecipeName = document.getElementById('original-recipe-name').value;

    var recipeName = document.getElementById('recipe-name').value;
    var ingredients = document.getElementById('ingredients').value;
    var recipeInstructions = document.getElementById('recipe').value;
    var category = document.getElementById('category').value;
    var image = document.getElementById('recipe-image').files[0];

    // Log the data values being sent
    console.log('Original Recipe Name:', originalRecipeName);

    console.log('Updating Recipe:');
    console.log('Recipe Name:', recipeName);
    console.log('Ingredients:', ingredients);
    console.log('Recipe Instructions:', recipeInstructions);
    console.log('Category:', category);
    console.log('Image File:', image ? image.name : 'No image selected');

    // Use AJAX or fetch API to send data to the server for updating
    var formData = new FormData();
    formData.append('original-recipe-name', originalRecipeName);

    formData.append('recipe_name', recipeName);
    formData.append('ingredients', ingredients);
    formData.append('recipe_instructions', recipeInstructions);
    formData.append('category', category);
    if (image) {
        formData.append('food_image', image);
        console.log('File to upload:', image.name);
    }

    fetch('update_recipe.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('Recipe updated successfully!');
            showPopup('Recipe updated successfully!');
        } else {
            console.log('Failed to update recipe!');
            showPopup('Failed to update recipe!');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showPopup('An error occurred while updating the recipe.');
    });
}




// Function to show the popup
function showPopup(message) {
    const popup = document.getElementById("popup");
    const popupMessage = document.getElementById("popup-message");
    popupMessage.textContent = message;
    document.getElementById("overlay").style.display = "block";
    popup.style.display = "block";
}

// Function to close the popup
function closePopup() {
    document.getElementById("overlay").style.display = "none";
    document.getElementById("popup").style.display = "none";
}
