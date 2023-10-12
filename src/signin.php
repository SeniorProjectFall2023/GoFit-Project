<?php
session_start();
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
    $query = "SELECT userID, username, password, name, email, dateofbirth, gender, meal_preference, weight, height, activity_level FROM `user` WHERE username=?";
    $stmt = $db->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Error preparing statement: " . $db->error . "');</script>";
        header("Location: /signin/signin.html?error=Error%20preparing%20statement:%20" . urlencode($db->error));
    } else {
        $stmt->bind_param("s", $username);
        if ($stmt->execute()) {
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = $result->fetch_assoc();

                if (password_verify($password, $row['password'])) {
                    // Sign-in successful, create a session
                    $_SESSION['user_name'] = $row['username']; // Store the username
                    $_SESSION['userID'] = $row['userID']; // Store the user's ID
                    $_SESSION['name'] = $row['name'];
                    $_SESSION['email'] = $row['email'];
                    $_SESSION['dateofbirth'] = $row['dateofbirth'];
                    $_SESSION['gender'] = $row['gender'];
                    $_SESSION['meal_preference'] = $row['meal_preference'];
                    $_SESSION['weight'] = $row['weight'];
                    $_SESSION['height'] = $row['height'];
                    $_SESSION['activity_level'] = $row['activity_level'];

                    // Initialize the chat log in the session if it doesn't exist
                    if (!isset($_SESSION['chatLog'])) {
                        $_SESSION['chatLog'] = [];
                    }

                    // Store user data for the chatbot
                    $userData = [
                        'name' => $_SESSION['name'],
                        'email' => $_SESSION['email'],
                        'dateofbirth' => $_SESSION['dateofbirth'],
                        'gender' => $_SESSION['gender'],
                        'meal_preference' => $_SESSION['meal_preference'],
                        'weight' => $_SESSION['weight'],
                        'height' => $_SESSION['height'],
                        'activity_level' => $_SESSION['activity_level']                        
                    ];
                    $_SESSION['user_data'] = $userData;

                    // Initialize the chat log in the session if it doesn't exist
                    if (!isset($_SESSION['chatLog'])) {
                        $_SESSION['chatLog'] = [];
                    }

                    // Redirect to the home page (index.html)
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
