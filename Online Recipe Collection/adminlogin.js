

document.getElementById('loginForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent default form submission

    // Fetch form data
    const formData = new FormData(this);

    // Send AJAX request to the server
    fetch('adminlogin.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Save admin username to localStorage
            localStorage.setItem("adminUsername", formData.get("username"));
            
            window.location.href = 'index.html'; // Redirect on success
        } else {
            // Display error message above the login button
            document.getElementById('error-message').textContent = data.message;
        }
    })
    .catch(error => console.error('Error:', error));
});




