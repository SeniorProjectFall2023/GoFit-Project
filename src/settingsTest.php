<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: /signin/signin.html"); // Redirect to the login page if not logged in
    exit();
}

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
        // ... (same code as before)

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
            // Update session variables with new user information
            $_SESSION['username'] = $username;
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['weight'] = $weight;
            $_SESSION['dateofbirth'] = $dateofbirth;
            $_SESSION['meal_preference'] = $meal_preference;
            $_SESSION['gender'] = $gender;

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

    // Fetch user data and send it as JSON
    if (isset($_SESSION['user_id'])) {
        $userData = array(
            'user_id' => $_SESSION['user_id'],
            'username' => $_SESSION['username'],
            'name' => $_SESSION['name'],
            'email' => $_SESSION['email'],
            'weight' => $_SESSION['weight'],
            'dateofbirth' => $_SESSION['dateofbirth'],
            'meal_preference' => $_SESSION['meal_preference'],
            'gender' => $_SESSION['gender']
        );

        echo json_encode($userData);
    }
}
?>