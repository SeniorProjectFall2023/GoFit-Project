<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['userID'])) {
    header("Location: /signin/signin.html"); // Redirect to the login page if not logged in
    exit();
}

// Function to establish a database connection
function connectToDatabase() {
    $db_host = "localhost";
    $db_user = "root";
    $db_password = "cosc4360-McCurry";
    $db_name = "GoFit";
    $conn = new mysqli($db_host, $db_user, $db_password, $db_name);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    // Fetch user data from the database and return it as JSON
    $conn = connectToDatabase();

    // Fetch user data based on userID from the session
    $userID = $_SESSION['userID'];

    $query = "SELECT email, dateofbirth, gender, meal_preference, weight, height, activity_level FROM user WHERE userID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $userID);
    $stmt->execute();
    $stmt->bind_result($email, $dateofbirth, $gender, $meal_preference, $weight, $height, $activity_level);

    // Fetch the user's data
    if ($stmt->fetch()) {
        // Store the fetched data in an array
        $userData = [
            'email' => $email,
            'dateofbirth' => $dateofbirth,
            'gender' => $gender,
            'meal_preference' => $meal_preference,
            'weight' => $weight,
            'height' => $height, // Added height field
            'activity_level' => $activity_level, // Added activity_level field
        ];

        // Return user data as JSON
        header('Content-Type: application/json');
        echo json_encode($userData);
    } else {
        // User not found
        echo json_encode(['error' => 'User not found']);
    }

    // Close the statement and database connection
    $stmt->close();
    $conn->close();
} elseif ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle form submission to update user information here
    $conn = connectToDatabase();

    // Retrieve user ID from the session
    $userID = $_SESSION['userID'];

    // Retrieve and sanitize form data, including the new fields
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $dateofbirth = custom_sanitize($_POST['dateofbirth']);
    $gender = custom_sanitize($_POST['gender']);
    $meal_preference = custom_sanitize($_POST['meal_preference']);
    $weight = filter_var($_POST['weight'], FILTER_VALIDATE_FLOAT);
    $height = filter_var($_POST['height'], FILTER_VALIDATE_FLOAT); // Added height field
    $activity_level = custom_sanitize($_POST['activity_level']); // Added activity_level field

    // Update user information in the database
    $query = "UPDATE user SET email=?, dateofbirth=?, gender=?, meal_preference=?, weight=?, height=?, activity_level=? WHERE userID=?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssssdsd", $email, $dateofbirth, $gender, $meal_preference, $weight, $height, $activity_level, $userID);

    if ($stmt->execute()) {
        // User information updated successfully
        echo json_encode(["success" => true, "message" => "User information updated successfully"]);
    } else {
        // Error updating user information
        echo json_encode(["success" => false, "message" => "Error updating user information: " . $stmt->error]);
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
