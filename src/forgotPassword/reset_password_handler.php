<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establish a database connection
    $db = new mysqli("localhost", "root", "cosc4360-McCurry", "GoFit");

    // Check for connection errors
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Validate and sanitize user input
    $token = $_POST['token'];
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    // Check if passwords match
    if ($newPassword === $confirmPassword) {
        // Verify the token and reset the password
        $query = "SELECT userID FROM `user` WHERE reset_token=?";
        $stmt = $db->prepare($query);

        if ($stmt) {
            $stmt->bind_param("s", $token);
            if ($stmt->execute()) {
                $stmt->store_result();

                if ($stmt->num_rows == 1) {
                    // Token is valid, update the user's password
                    $stmt->close();

                    // Hash the new password before updating it
                    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

                    // Update the user's password in the database
                    $updateQuery = "UPDATE `user` SET password=?, reset_token=NULL WHERE reset_token=?";
                    $updateStmt = $db->prepare($updateQuery);

                    if ($updateStmt) {
                        $updateStmt->bind_param("ss", $hashedPassword, $token);
                        if ($updateStmt->execute()) {
                            // Password successfully reset, redirect to the login page
                            header("Location: /signin/signin.html");
                            exit();
                        } else {
                            echo "<script>alert('Error updating password: " . $updateStmt->error . "');</script>";
                        }
                        $updateStmt->close();
                    } else {
                        echo "<script>alert('Error preparing update statement: " . $db->error . "');</script>";
                    }
                } else {
                    echo "<script>alert('Invalid or expired token.');</script>";
                }
                $stmt->close();
            } else {
                echo "<script>alert('Error executing statement: " . $stmt->error . "');</script>";
            }
        }
    } else {
        // Passwords do not match, handle the error accordingly
        echo "<script>alert('Passwords do not match.');</script>";
    }

    // Close the database connection
    $db->close();
}
?>
