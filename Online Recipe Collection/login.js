// Function to validate the login form
function validateForm() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    if (username === "" || password === "") {
        alert("Please fill out both username and password.");
        return false; // Prevent form submission
    }
    return true; // Allow form submission
}

// Handle login via AJAX
function handleLogin(event) {
    event.preventDefault();  // Prevent default form submission

    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    var xhr = new XMLHttpRequest();
    xhr.open("POST", "login.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onload = function() {
        if (xhr.status === 200) {
            // If the login was successful, redirect to the home page
            if (xhr.responseText === "success") {
                // Save username to localStorage
                localStorage.setItem("username", username);
                window.location.href = "index.html";  // Redirect to home page
            } else {
                // If there was an error, show the error message
                document.getElementById("error-message").innerText = xhr.responseText;
            }
        }
    };

    xhr.send("username=" + encodeURIComponent(username) + "&password=" + encodeURIComponent(password));
}
