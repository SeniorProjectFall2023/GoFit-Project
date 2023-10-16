<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish a database connection
    $db = new mysqli("localhost", "root", "cosc4360-McCurry", "GoFit");

    // Check for connection errors
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Custom filter function to sanitize input
    function custom_sanitize($input) {
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    // Validate and sanitize user input
    $name = custom_sanitize($_POST['name']);
    $username = custom_sanitize($_POST['username']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password

    // Check if meal_preference and gender are set in $_POST
    $meal_preference = isset($_POST['meal_preference']) ? custom_sanitize($_POST['meal_preference']) : null;
    $gender = isset($_POST['gender']) ? custom_sanitize($_POST['gender']) : null;

    $weight = filter_var($_POST['weight'], FILTER_VALIDATE_FLOAT);
    $dateofbirth = custom_sanitize($_POST['dateofbirth']);
    
    // Extract the selected height from the dropdown
    $height = custom_sanitize($_POST['height']);

    $activity_level = isset($_POST['activity_level']) ? custom_sanitize($_POST['activity_level']) : null;

    // Perform additional validation as needed

    // Insert user data into the database
    $query = "INSERT INTO `user` (name, username, email, password, meal_preference, gender, weight, dateofbirth, height, activity_level)
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    $stmt = $db->prepare($query);

    if (!$stmt) {
        $errorMessage = "Error: " . $db->error;
        header("Location: /signup/signup.html?error=" . urlencode($errorMessage));
        exit();
    } else {
        $stmt->bind_param("ssssssssss", $name, $username, $email, $password, $meal_preference, $gender, $weight, $dateofbirth, $height, $activity_level);
        if ($stmt->execute()) {
            // Store user information in the session
            $_SESSION['user_name'] = $username; // Store the username
            $_SESSION['userID'] = $stmt->insert_id; // Store the user's ID
            header("Location: /signin/signin.html");
            exit();
        } else {
            $errorMessage = "Error: " . $stmt->error;
            header("Location: /signup/signup.html?error=" . urlencode($errorMessage));
            exit();
        }
        $stmt->close();
    }

    // Close the database connection
    $db->close();
} else {
    // Display a message if the page is accessed without a POST request
    echo "<h1>This page is working.</h1>";
}
