<?php
require __DIR__ . '/vendor/autoload.php';


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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

                        // Use PHPMailer to send the email via Brevo SMTP
                        try {
                            $mail = new PHPMailer(true);

                            $mail->isSMTP();
                            $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Enable debugging
                            $mail->SMTPAuth = true;
                            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                            $mail->Host = 'smtp-relay.brevo.com'; // Brevo SMTP server
                            $mail->Port = 587;
                            $mail->Username = 'xsmtpsib-dbed0328278eb4281a1d060186d0f7103901cedd6ce8cdca61e4882f4dcd4ee3-pMDg4TBOkGqdjcSy'; // Your Brevo API key
                            $mail->Password = 'xsmtpsib-dbed0328278eb4281a1d060186d0f7103901cedd6ce8cdca61e4882f4dcd4ee3-pMDg4TBOkGqdjcSy'; // Your Brevo API key

                            $mail->setFrom('fayzan23@gmail.com', 'GoFit ChatBot'); // Sender's email and name
                            $mail->addAddress($email, $username); // Recipient's email and name

                            $mail->Subject = 'Password Reset Link';
                            $mail->isHTML(true);

                            // Define the email body here (you can use HTML)
                            $emailBody = "
                                <p>Hello $username,</p>
                                <p>You have requested a password reset. Please click the following link to reset your password:</p>
                                <p><a href='$resetLink'>$resetLink</a></p>
                                <p>If you did not request this, please ignore this email.</p>
                                <p>Regards,<br>The GoFit ChatBot Team</p>
                            ";

                            $mail->Body = $emailBody;

                            $mail->send();
                            echo "<script>alert('Password reset link sent to your email.');</script>";
                        } catch (Exception $e) {
                            echo "<script>alert('Error sending the password reset email: " . $mail->ErrorInfo . "');</script>";
                        }
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
