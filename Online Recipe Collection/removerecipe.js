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


function removeRecipe() {
    const recipeName = document.getElementById("recipe-name").value; // Get the recipe name to remove

    if (!recipeName) {
        console.log("No recipe name provided for removal");
        return; // Don't proceed if there's no recipe name
    }

    // Prepare the request
    fetch('removerecipe.php?action=removeRecipe', {
        method: 'POST', // Use POST for the removal request
        headers: {
            'Content-Type': 'application/json' // Set correct header for JSON
        },
        body: JSON.stringify({ recipe_name: recipeName }) // Send data in JSON format
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log("Recipe removed successfully!");
            showPopup('Recipe removed successfully!');
            clearFormFields();
            filterRecipes();
            populateCategoryOptions(); // Reload the recipe list
        } else {
            console.error("Error removing recipe:", data.error);
            showPopup('Error removing recipe: ' + data.error);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showPopup('Error removing recipe.');
    });
}






// Add event listener to the "Remove" button
document.getElementById("remove-button").addEventListener("click", removeRecipe);




function clearFormFields() {
    // Clear text input fields
    document.getElementById("recipe-name").value = '';
    document.getElementById("category").value = '';
    document.getElementById("ingredients").value = '';
    document.getElementById("recipe").value = '';

    // Reset the file input to "nothing selected"
    document.getElementById("recipe-image").value = "";

    // Clear the image preview without showing any "image not found" text
    const imagePreview = document.getElementById("image-preview"); // Assuming the image preview has an ID of "image-preview"
    if (imagePreview) {
        imagePreview.src = 'file:///C:/Users/amjad/Documents/WhatsApp%20Image%202024-11-18%20at%2012.47.58_56418b1b.jpg'; // Set to your default image
        imagePreview.alt = 'Default image preview'; // Set alt text for the image

    }
}





// Add event listener to the "Update" button

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
