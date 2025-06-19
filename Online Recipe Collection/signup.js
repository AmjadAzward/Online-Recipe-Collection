document.addEventListener("DOMContentLoaded", () => {
    const form = document.getElementById("signup-form");
    const messageContainer = document.getElementById("message-container");
    const popupContainer = document.getElementById("popup-container");

    const loginButton = document.getElementById("go-to-login");
    const homeButton = document.getElementById("go-to-home");

    form.addEventListener("submit", (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        fetch("signup.php", {
            method: "POST",
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            messageContainer.textContent = data.message;

            if (data.success) {
                popupContainer.style.display = "flex"; // Show success popup
                form.reset(); // Clear form fields

                // Optionally hide message container on success
                messageContainer.style.display = "none";
            } else {
                messageContainer.classList.add("error"); // Display error message
            }
        })
        .catch(error => {
            messageContainer.textContent = "An error occurred. Please try again.";
            messageContainer.classList.add("error");
        });
    });

    // Event listeners for popup buttons
    loginButton.addEventListener("click", () => {
        window.location.href = "login.html"; // Navigate to login page
    });

    homeButton.addEventListener("click", () => {
        window.location.href = "index.html"; // Navigate to homepage
    });
});
