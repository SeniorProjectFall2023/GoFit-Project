<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish a database connection
    $db = new mysqli("localhost", "root", "cosc4360-McCurry", "GoFit");

    // Check for connection errors
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Validate and sanitize user input
    $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
    $password = $_POST['password'];

    // Query the database to check if the user exists
    $query = "SELECT userID, username, password FROM `user` WHERE username=?";
    $stmt = $db->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Error preparing statement: " . $db->error . "');</script>";
        header("Location: /signin/signin.html?error=Error%20preparing%20statement:%20" . urlencode($db->error));
    } else {
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($userID, $db_username, $db_password);
                $stmt->fetch();

                if (password_verify($password, $db_password)) {
                    // Sign-in successful, create a session
                    session_start();
                    $_SESSION['user_name'] = $db_username; // Store the username
                    $_SESSION['userID'] = $userID; // Store the user's ID

                    // Redirect to the home page
                    header("Location: /index.html");
                    exit();
                } else {
                    echo "<script>alert('Incorrect password.');</script>";
                    echo "<script>window.location.href='/signin/signin.html?error=Incorrect%20password.';</script>";
                }
            } else {
                echo "<script>alert('User not found.');</script>";
                echo "<script>window.location.href='/signin/signin.html?error=User%20not%20found.';</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error executing statement: " . $stmt->error . "');</script>";
            header("Location: /signin/signin.html?error=Error%20executing%20statement:%20" . urlencode($stmt->error));
        }
    }

    // Close the database connection
    $db->close();
}
?>
