<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: /signin/signin.html"); // Redirect to the login page if not logged in
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Establish a database connection if needed
    $db_host = "localhost";
    $db_user = "root";
    $db_password = "cosc4360-McCurry";
    $db_name = "GoFit";

    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch user data from the database based on userID from the session
    $userID = $_SESSION['userID'];

    $query = "SELECT name, email, dateofbirth, gender, meal_preference, weight FROM users WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($name, $email, $dateofbirth, $gender, $meal_preference, $weight);

    // Fetch the user's data
    if ($stmt->fetch()) {
        // Store the fetched data in an array
        $userData = [
            'name' => $name,
            'email' => $email,
            'dateofbirth' => $dateofbirth,
            'gender' => $gender,
            'meal_preference' => $meal_preference,
            'weight' => $weight,
        ];

        // Return user data as JSON
        header('Content-Type: application/json');
        echo json_encode($userData);
    } else {
        // User not found
        echo 'User not found';
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission to update user information here
    // You need to parse the POST data, update the user's information in the database, and return a response accordingly

    // Establish a new database connection for the POST request
    $db_host = "localhost";
    $db_user = "root";
    $db_password = "cosc4360-McCurry";
    $db_name = "GoFit";

    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve user ID from the session
    $userID = $_SESSION['userID'];

    // Retrieve and sanitize form data
    $name = custom_sanitize($_POST['name']);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $dateofbirth = custom_sanitize($_POST['dateofbirth']);
    $gender = custom_sanitize($_POST['gender']);
    $meal_preference = custom_sanitize($_POST['meal_preference']);
    $weight = filter_var($_POST['weight'], FILTER_VALIDATE_FLOAT);

    // Update user information in the database
    $query = "UPDATE users SET name=?, email=?, dateofbirth=?, gender=?, meal_preference=?, weight=? WHERE userID=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssssd", $name, $email, $dateofbirth, $gender, $meal_preference, $weight, $userID);

    if ($stmt->execute()) {
        // User information updated successfully
        echo "User information updated successfully.";
    } else {
        // Error updating user information
        echo "Error updating user information: " . $stmt->error;
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
}

// Custom filter function to sanitize input
function custom_sanitize($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
?>
