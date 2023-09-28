<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

// Enable error reporting for debugging (remove in production)
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
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    // Check if the email exists in the database
    $query = "SELECT userID, username FROM `user` WHERE email=?";
    $stmt = $db->prepare($query);

    if (!$stmt) {
        echo "<script>alert('Error preparing statement: " . $db->error . "');</script>";
    } else {
        $stmt->bind_param("s", $email);
        if ($stmt->execute()) {
            $stmt->store_result();

            if ($stmt->num_rows == 1) {
                $stmt->bind_result($userID, $username);
                $stmt->fetch();

                // Generate a unique reset token and save it in the database
                $resetToken = bin2hex(random_bytes(32));
                $updateQuery = "UPDATE `user` SET reset_token=? WHERE userID=?";
                $updateStmt = $db->prepare($updateQuery);

                if ($updateStmt) {
                    $updateStmt->bind_param("si", $resetToken, $userID);
                    if ($updateStmt->execute()) {
                        // Send an email with a reset link to the user's email address
                        $resetLink = "http://143.110.228.26/forgotPassword/reset_password_form.html?token=" . $resetToken;

                        // Compose the email message
                        $subject = 'Password Reset Link';
                        $from = 'fayzan23@gmail.com';
                        $fromName = 'GoFit ChatBot Team';
                        $to = $email;
                        $bodyHtml = "<p>Hello $username,</p>
                            <p>You have requested a password reset. Please click the following link to reset your password:</p>
                            <p><a href='$resetLink'>$resetLink</a></p>
                            <p>If you did not request this, please ignore this email.</p>
                            <p>Regards,<br>The GoFit ChatBot Team</p>";

                        // Send the email using Elastic Email API
                        $elasticEmailApiKey = 'C85A2F0F8037A1687985B513E5D2C31596C33195608130C320109FF3569C930D7769D838370EF5E8760B73F8114BF7AE';

                        $data = [
                            'apikey' => $elasticEmailApiKey,
                            'subject' => $subject,
                            'from' => $from,
                            'fromName' => $fromName,
                            'to' => $to,
                            'bodyHtml' => $bodyHtml,
                        ];

                        $url = 'https://api.elasticemail.com/v2/email/send';

                        $ch = curl_init($url);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                        $response = curl_exec($ch);

                        if ($response !== false) {
                            echo "<script>alert('Password reset link sent to your email.');</script>";
                            echo "<script>window.location.href = '../index.html';</script>"; // Redirect to the home page
                        } else {
                            echo "<script>alert('Error sending the password reset email.');</script>";
                        }

                        curl_close($ch);
                    } else {
                        echo "<script>alert('Error updating reset token: " . $updateStmt->error . "');</script>";
                    }
                    $updateStmt->close();
                } else {
                    echo "<script>alert('Error preparing update statement: " . $db->error . "');</script>";
                }
            } else {
                echo "<script>alert('Email not found.');</script>";
            }
            $stmt->close();
        } else {
            echo "<script>alert('Error executing statement: " . $stmt->error . "');</script>";
        }
    }

    // Close the database connection
    $db->close();
}
?>
