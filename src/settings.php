<?php
// Initialize a session if not already started
session_start();

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check which form was submitted based on the "form_type" field
    if (isset($_POST["form_type"])) {
        if ($_POST["form_type"] === "change_password") {
            // Handle change password form submission
            handlePasswordChange();
        } elseif ($_POST["form_type"] === "update_user_info") {
            // Handle update user information form submission
            updateUserInfo();
        }
    }
}

// Function to handle password change
function handlePasswordChange() {
    // Establish a database connection
    $db = new mysqli("localhost", "root", "cosc4360-McCurry", "GoFit");

    // Check if the user is logged in (you can implement your own login logic here)
    if (!isset($_SESSION["user_id"])) {
        header("Location: /signin/signin.html"); // Redirect to login page if not logged in
        exit;
    }

    $userID = $_SESSION["user_id"];

    // Retrieve form data
    $currentPassword = $_POST["current_password"];
    $newPassword = $_POST["new_password"];
    $confirmPassword = $_POST["confirm_password"];

    // Add error handling for database connection
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Prepare and execute the query to retrieve the user's current password
    $query = "SELECT password FROM `user` WHERE user_id=?";
    $stmt = $db->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Error preparing statement: " . $db->error . "');</script>";
    } else {
        $stmt->bind_param("s", $userID);
        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($db_password);
                $stmt->fetch();

                if (password_verify($currentPassword, $db_password)) {
                    // Check if the new passwords match
                    if ($newPassword === $confirmPassword) {
                        // Hash and update the new password in the database
                        $hashedNewPassword = password_hash($newPassword, PASSWORD_DEFAULT);
                        $updateQuery = "UPDATE user SET password = ? WHERE user_id = ?";
                        $updateStmt = $db->prepare($updateQuery);
                        $updateStmt->bind_param("ss", $hashedNewPassword, $userID);
                        if ($updateStmt->execute()) {
                            // Redirect back to settings.html with a success message
                            header("Location: settings.html?password_change_success=1");
                            exit;
                        } else {
                            // Error message here.
                        }
                        $updateStmt->close();
                    } else {
                        // Passwords do not match, show an error message
                        header("Location: settings.html?password_change_error=Passwords do not match.");
                        exit;
                    }
                } else {
                    // Current password is incorrect, show an error message
                    header("Location: settings.html?password_change_error=Current password is incorrect.");
                    exit;
                }
            }
        }
    }
    // Close the database connection
    $db->close();
}

// Function to update user information
function updateUserInfo() {
    // Establish a database connection
    $db = new mysqli("localhost", "root", "cosc4360-McCurry", "GoFit");

    // Check if the user is logged in (you can implement your own login logic here)
    if (!isset($_SESSION["user_id"])) {
        header("Location: /signin/signin.html"); // Redirect to login page if not logged in
        exit;
    }

    // Get UserID
    $userID = $_SESSION["user_id"];

    // Retrieve and sanitize form data (you can add more validation)
    $username = htmlspecialchars($_POST["username"]);
    $name = htmlspecialchars($_POST["name"]);
    $email = htmlspecialchars($_POST["email"]);
    $weight = htmlspecialchars($_POST["weight"]);
    $dateofbirth = htmlspecialchars($_POST["dateofbirth"]);
    $meal_preference = htmlspecialchars($_POST["meal_preference"]);
    $gender = htmlspecialchars($_POST["gender"]);

    // Add error handling for database connection
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Prepare and execute the query to update user information
    $updateQuery = "UPDATE user SET username = ?, name = ?, email = ?, weight = ?, dateofbirth = ?, meal_preference = ?, gender = ? WHERE user_id = ?";
    $updateStmt = $db->prepare($updateQuery);
    $updateStmt->bind_param("ssssssss", $username, $name, $email, $weight, $dateofbirth, $meal_preference, $gender, $userID);
    if ($updateStmt->execute()) {
        // Redirect back to settings.html with a success message
        header("Location: settings.html?user_info_update_success=1");
        exit;
    } else {
        // Error message here.
    }

    // Close the database connection
    $db->close();
}
?>
