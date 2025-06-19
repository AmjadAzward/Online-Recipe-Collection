// Array of images with corresponding text for each background
const backgrounds = [
    { image: 'https://images.unsplash.com/photo-1677175183792-e6a9b8b14305?fm=jpg&q=60&w=3000&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTl8fGRhcmslMjBmb29kJTIwcGhvdG9ncmFwaHl8ZW58MHx8MHx8fDA%3D', title: 'Welcome to Quick Bites', description: 'Discover delicious recipes\nand elevate your cooking game!' },
    { image: 'https://images.pexels.com/photos/5702766/pexels-photo-5702766.jpeg?cs=srgb&dl=pexels-ekaterina-bolovtsova-5702766.jpg&fm=jpg', title: 'Explore New Flavors', description: 'Find inspiration for every meal\nand try something new!' },
    { image: 'https://img.freepik.com/premium-photo/chef-is-carefully-adding-finishing-touches-deliciouslooking-plate-food_36682-8615.jpg', title: 'Cooking Made Easy', description: 'Simple, tasty recipes\nto enjoy with friends and family!' }
];

let currentIndex = 0;

// Function to change the background image and update content
function changeBackground(direction = 'next') {
    if (direction === 'next') {
        currentIndex = (currentIndex + 1) % backgrounds.length;
    } else if (direction === 'prev') {
        currentIndex = (currentIndex - 1 + backgrounds.length) % backgrounds.length;
    }

    // Change the background image inside the div (image-side)
    const imageElement = document.querySelector('.image-side img');
    imageElement.src = backgrounds[currentIndex].image;

    // Update content title and description based on the current image
    document.querySelector('.content h1').textContent = backgrounds[currentIndex].title;

    // Replace \n in description with line breaks for HTML display
    document.querySelector('.content p').innerHTML = backgrounds[currentIndex].description.replace(/\n/g, '<br>');
}

// Function to navigate to a section when selecting from dropdown
function navigateToRecipe(section) {
    if (section) {
        window.location.href = section;
    }
}

// Automatically change background every 3 seconds
setInterval(() => {
    changeBackground('next');
}, 3000);



function updateNavbar() {
    const authLinks = document.getElementById("auth-links");
    const usernameDisplay = document.getElementById("username-display");
    const logoutButton = document.getElementById("logout-button");
    const manageButton = document.getElementById("manage-button");

    // Get the Feedback menu item
    const feedbackItem = document.querySelector("li a[href='feedback.html']");

    // Check if the user or admin is logged in (using localStorage)
    const username = localStorage.getItem("username");
    const adminUsername = localStorage.getItem("adminUsername");

    if (adminUsername) {
        // Hide login/signup links, show manage button for admin and logout button
        authLinks.querySelector(".login").style.display = "none";
        authLinks.querySelector(".signup").style.display = "none";
        usernameDisplay.style.display = "none"; // No welcome message for admin
        manageButton.style.display = "inline";  // Show the "Manage" button for admin
        logoutButton.style.display = "inline";

        // Change the Feedbacks link to a different admin-specific page
        if (feedbackItem) {
            feedbackItem.href = "Afeedback.html"; // Load admin-specific feedback page
        }
    } else if (username) {
        // If a regular user is logged in, show welcome message and logout button
        authLinks.querySelector(".login").style.display = "none";
        authLinks.querySelector(".signup").style.display = "none";
        usernameDisplay.style.display = "inline";
        usernameDisplay.textContent = `Welcome, ${username}`;
        usernameDisplay.style.fontWeight = 'bold'; // Bold text
        usernameDisplay.style.fontSize = '18px'; // Font size

        manageButton.style.display = "none";   // Hide the "Manage" button for regular users
        logoutButton.style.display = "inline";

        // Ensure Feedbacks link remains the same for users
        if (feedbackItem) feedbackItem.href = "feedback.html";
    } else {
        // If not logged in, show login/signup and hide username, manage, and logout buttons
        authLinks.querySelector(".login").style.display = "inline";
        authLinks.querySelector(".signup").style.display = "inline";
        usernameDisplay.style.display = "none";
        manageButton.style.display = "none";
        logoutButton.style.display = "none";

        // Ensure Feedbacks link remains the same for guests
        if (feedbackItem) feedbackItem.href = "feedback.html";
        feedbackItem.textContent = "Feedbacks";
    }
}


// Call the function on page load
updateNavbar();

// Update navbar on page load
document.addEventListener("DOMContentLoaded", updateNavbar);


// Update navbar on page load
document.addEventListener("DOMContentLoaded", updateNavbar);








// Function to handle logout with a custom confirmation
function logout() {
    // Clear the username from localStorage
    localStorage.removeItem("username");
    localStorage.removeItem("adminUsername");


    // Create a custom confirmation modal for logout
    var modal = document.createElement("div");
    modal.id = "logout-modal";
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100%";
    modal.style.height = "100%";
    modal.style.backgroundColor = "rgba(0, 0, 0, 0.5)";
    modal.style.display = "flex";
    modal.style.justifyContent = "center";
    modal.style.alignItems = "center";
    modal.style.zIndex = "1000";
    
    // Modal content
    var modalContent = document.createElement("div");
    modalContent.style.backgroundColor = "white";
    modalContent.style.padding = "20px";
    modalContent.style.borderRadius = "8px";
    modalContent.style.textAlign = "center";
    modalContent.style.height = "120px"; // Adjusted to accommodate the buttons
    modalContent.style.marginTop = "100px"; // Added margin for better positioning
    modalContent.innerHTML = `
        <p>Are you sure that you want to logout?</p>
        <a href="index.html" id="login-btn" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin-top: 13px; display: inline-block; margin-right: 10px;">Yes</a>
        <a  id="cancel-btn" style="padding: 10px 20px; background-color: #4CAF50; color: white; text-decoration: none; border-radius: 5px; margin-top: 13px; display: inline-block; margin-right: 10px;">No</a>`;

    // Append modal content to the modal
    modal.appendChild(modalContent);

    // Append the modal to the body
    document.body.appendChild(modal);

    // Event listener for the "Log In Again" button
    document.getElementById("login-btn").addEventListener("click", function() {
        window.location.href = "index.html"; // Redirect to the login page when the button is clicked
    });

    // Event listener for the "Cancel" button
    document.getElementById("cancel-btn").addEventListener("click", function() {
        document.body.removeChild(modal); // Remove the modal if cancel is clicked
    });
}



document.addEventListener("DOMContentLoaded", function () {
    loadRecipes();
});

function loadRecipes() {
    fetch('get_recipes.php') // Assume this PHP file fetches recipes from your database
        .then(response => response.json())
        .then(data => {
            const recipeList = document.getElementById('recipe-list');
            recipeList.innerHTML = ''; // Clear existing content
            
            data.forEach(recipe => {
                const recipeDiv = document.createElement('div');
                recipeDiv.classList.add('recipe-item');
                
                // Creating the HTML structure for each recipe
                recipeDiv.innerHTML = `
                    <div class="recipe-thumbnail">
                        <img src="${recipe.food_image}" alt="${recipe.recipe_name}">
                    </div>
                    <div class="recipe-info">
                        <h3>${recipe.recipe_name}</h3>
                        <p><strong>Category:</strong> ${recipe.category}</p>
                        <p><strong>Ingredients:</strong> ${recipe.ingredients}</p>
                        <p><strong>Instructions:</strong> ${recipe.recipe_instructions}</p>
                    </div>
                `;
                
                recipeList.appendChild(recipeDiv);
            });
        })
        .catch(error => console.error('Error fetching recipes:', error));
}
