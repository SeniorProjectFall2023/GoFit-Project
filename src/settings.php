<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish a database connection if needed
    $db_host = "localhost";
    $db_user = "root";
    $db_password = "cosc4360-McCurry";
    $db_name = "GoFit";
    
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Custom filter function to sanitize input
    function custom_sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    // Validate and sanitize user input
    $form_type = custom_sanitize($_POST['form_type']);

    if ($form_type === "change_password") {
        // Handle password change
        $current_password = custom_sanitize($_POST['password']);
        $new_password = custom_sanitize($_POST['new_password']);
        $confirm_password = custom_sanitize($_POST['confirm_password']);
        
        // Check if the new password matches the confirm password
        if ($new_password !== $confirm_password) {
            echo "Passwords do not match. Please try again.";
        } else {
            // Check the current password for the user from the database
            $user_id = $_SESSION['user_id'];
            $stmt = $conn->prepare("SELECT password FROM user WHERE userID = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stored_password = $row['password'];

            // Verify the current password
            if (password_verify($current_password, $stored_password)) {
                // Password is correct, update the password in the database
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE user SET password = ? WHERE userID = ?");
                $stmt->bind_param("si", $new_password_hash, $user_id);
                if ($stmt->execute()) {
                    // Password updated successfully
                    header("Location: settings.html");
                    exit();
                } else {
                    echo "Error updating password: " . $stmt->error;
                }
            } else {
                // Current password is incorrect, show an error
                echo "Current password is incorrect. Please try again.";
            }
        }
    } elseif ($form_type === "update_user_info") {
        // Handle user information update
        $user_id = $_SESSION['user_id'];
        $username = custom_sanitize($_POST['username']);
        $name = custom_sanitize($_POST['name']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $weight = filter_var($_POST['weight'], FILTER_VALIDATE_FLOAT);
        $dateofbirth = custom_sanitize($_POST['dateofbirth']);
        $meal_preference = custom_sanitize($_POST['meal_preference']);
        $gender = custom_sanitize($_POST['gender']);
        
        // Update the user's information in the database
        $stmt = $conn->prepare("UPDATE user SET username = ?, name = ?, email = ?, weight = ?, dateofbirth = ?, meal_preference = ?, gender = ? WHERE userID = ?");
        $stmt->bind_param("sssdsssi", $username, $name, $email, $weight, $dateofbirth, $meal_preference, $gender, $user_id);
        if ($stmt->execute()) {
            // User information updated successfully
            header("Location: settings.html");
            exit();
        } else {
            echo "Error updating user information: " . $stmt->error;
        }
    }
    $conn->close();
} else {
    // Display a message if the page is accessed without a POST request
    echo "<h1>This page is working.</h1>";
}
?>
