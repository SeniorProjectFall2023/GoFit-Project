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
    $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $dateofbirth = $_POST['dateofbirth'];
    $gender = $_POST['gender'];
    $meal_preference = $_POST['meal_preference'];
    $weight = (float)$_POST['weight'];

    // Check if the username is already taken
    $check_query = "SELECT userID FROM `user` WHERE username=?";
    $check_stmt = $db->prepare($check_query);

    if (!$check_stmt) {
        echo "<script>alert('Error preparing statement: " . $db->error . "');</script>";
        header("Location: /signup/signup.html?error=Error%20preparing%20statement:%20" . urlencode($db->error));
    } else {
        $check_stmt->bind_param("s", $username);
        if ($check_stmt->execute()) {
            $check_stmt->store_result();

            if ($check_stmt->num_rows > 0) {
                echo "<script>alert('Username already exists. Please choose a different username.');</script>";
                echo "<script>window.location.href='/signup/signup.html?error=Username%20already%20exists.';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Error executing statement: " . $check_stmt->error . "');</script>";
            header("Location: /signup/signup.html?error=Error%20executing%20statement:%20" . urlencode($check_stmt->error));
        }
        $check_stmt->close();
    }

    // Insert user data into the database
    $insert_query = "INSERT INTO `user` (username, name, email, password, dateofbirth, gender, meal_preference, weight) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $insert_stmt = $db->prepare($insert_query);

    if (!$insert_stmt) {
        echo "<script>alert('Error preparing statement: " . $db->error . "');</script>";
        header("Location: /signup/signup.html?error=Error%20preparing%20statement:%20" . urlencode($db->error));
    } else {
        $insert_stmt->bind_param("sssssssd", $username, $name, $email, $password, $dateofbirth, $gender, $meal_preference, $weight);
        if ($insert_stmt->execute()) {
            // Sign-up successful, create a session
            $_SESSION['user_name'] = $username; // Store the username
            $_SESSION['name'] = $name;
            $_SESSION['email'] = $email;
            $_SESSION['dateofbirth'] = $dateofbirth;
            $_SESSION['gender'] = $gender;
            $_SESSION['meal_preference'] = $meal_preference;
            $_SESSION['weight'] = $weight;

            // Send user data to the chatbot (assuming chatbot.html is your chatbot page)
            $userData = [
                'name' => $name,
                'email' => $email,
                'dateofbirth' => $dateofbirth,
                'gender' => $gender,
                'meal_preference' => $meal_preference,
                'weight' => $weight,
            ];
            $userDataJson = json_encode($userData);
            $redirectUrl = "/chatbot.html?user_data=" . urlencode($userDataJson);
            header("Location: " . $redirectUrl);

            // Redirect to the login page
            header("Location: /signin/signin.html");
            exit();
        } else {
            echo "<script>alert('Error executing statement: " . $insert_stmt->error . "');</script>";
            header("Location: /signup/signup.html?error=Error%20executing%20statement:%20" . urlencode($insert_stmt->error));
        }
        $insert_stmt->close();
    }

    // Close the database connection
    $db->close();
}
?>
