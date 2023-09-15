<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve data from the sign-in form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // TODO: Implement validation and sanitation for the data.

    // Connect to the MySQL database (Replace with your database credentials)
    $db = new mysqli('localhost', 'your_db_username', 'your_db_password', 'your_db_name');

    if ($db->connect_error) {
        die('Connection failed: ' . $db->connect_error);
    }

    // Query the database to check if the username exists
    $query = "SELECT * FROM users WHERE username = ?";
    $stmt = $db->prepare($query);

    if ($stmt) {
        $stmt->bind_param('s', $username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // Username exists, verify the password
            $user = $result->fetch_assoc();
            if (password_verify($password, $user['password'])) {
                // Password is correct, redirect to the home page (index.html)
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
