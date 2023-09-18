<?php
// Initialize a session if not already started
session_start();

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which form was submitted based on the "form_type" field
    if (isset($_POST["form_type"])) {
        if ($_POST["form_type"] === "change_password") {
            // Handle change password form submission
            handlePasswordChange();
        } elseif ($_POST["form_type"] === "update_user_info") {
            // Handle update user information form submission
            updateUserInfo();
        }
    }
}

// Function to handle password change
function handlePasswordChange() {
    // Check if the user is logged in (you can implement your own login logic here)
    if (!isset($_SESSION["user_id"])) {
        header("Location: /signin/signin.html"); // Redirect to login page if not logged in
        exit;
    }

    // Retrieve form data
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    // Example validation: Ensure the current password matches some stored value
    $storedPassword = "hashed_password"; // Replace with the actual stored password
    if (password_verify($currentPassword, $storedPassword)) {
        // Check if the new password matches the confirmed password
        if ($newPassword === $confirmPassword) {
            // Example: Update the user's password in the database
            // Implement your database update logic here

            // Redirect back to settings.html with a success message
            header("Location: settings.html?password_change_success=1");
            exit;
        } else {
            // Passwords do not match, show an error message
            header("Location: settings.html?password_change_error=Passwords do not match.");
            exit;
        }
    } else {
        // Current password is incorrect, show an error message
        header("Location: settings.html?password_change_error=Current password is incorrect.");
        exit;
    }
}

// Function to update user information
function updateUserInfo() {
    // Check if the user is logged in (you can implement your own login logic here)
    if (!isset($_SESSION["user_id"])) {
        header("Location: /signin/signin.html"); // Redirect to login page if not logged in
        exit;
    }

    // Retrieve and sanitize form data (you can add more validation)
    $username = htmlspecialchars($_POST["username"]);
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $weight = htmlspecialchars($_POST["weight"]);
    $dateofbirth = htmlspecialchars($_POST["dateofbirth"]);
    $fitness_goal = htmlspecialchars($_POST["meal_preference"]);
    $gender = htmlspecialchars($_POST["gender"]);

    // Example: Update user information in the database
    // Implement your database update logic here

    // Redirect back to settings.html with a success message
    header("Location: settings.html?user_info_update_success=1");
    exit;
}
?>
