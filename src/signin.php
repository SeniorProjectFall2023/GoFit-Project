<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish a database connection
    $db = new mysqli("localhost", "gofit", "cosc4360-McCurry", "GoFit");

    // Check for connection errors
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Validate and sanitize user input
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    // Query the database to check if the user exists
    $query = "SELECT * FROM user WHERE username='$username'";
    $result = $db->query($query);


    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            // Sign-in successful, create a session
            session_start();
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            
            // Redirect to the home page
            header("Location: index.html");
            exit();
        } else {
            echo "Incorrect password.";
        }
    } else {
        echo "User not found.";
    }

    // Close the database connection
    $db->close();
}
?>
