<?php
// Start a PHP session
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the sign-in form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // TODO: Implement validation and sanitation for the data.

    // Database credentials (Update these with your database information)
    $db_host = 'localhost';
    $db_username = 'root';
    $db_password = '';
    $db_name = 'GoFit';

    // Connect to the MySQL database
    $db = new mysqli($db_host, $db_username, $db_password, $db_name);

    if ($db->connect_error) {
        die('Connection failed: ' . $db->connect_error);
    }

    // Query the database to check if the username exists
    $query = "SELECT * FROM GoFit WHERE username = ?";
    $stmt = $db->prepare($query);

    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Username exists, verify the password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password is correct, set the session variable
                $_SESSION['username'] = $username;
                // Redirect to the home page (index.html)
                header('Location: ../index.html');
                exit(); // Make sure to exit to prevent further script execution
            }
        }
        $stmt->close();
    }

    // If username or password is incorrect, display an error message
    $error_message = 'Invalid username or password. Please try again.';
    $db->close();
} else {
    // Handle non-POST requests here if needed
}
?>
