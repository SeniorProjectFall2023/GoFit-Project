<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the form data
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    try {
        // Create a new PHPMailer instance
        $mail = new PHPMailer();

        // SMTP configuration (replace with your Elastic Email API details)
        $mail->isSMTP();
        $mail->Host = 'smtp.elasticemail.com';
        $mail->SMTPAuth = true;
        $mail->Username = '6509A77CDD87F4427D783C83FFF48851D2E450F99E673AECE1B49D1BB993DFEECB40C3436C92C49F3E7EFB495F05148B'; // Replace with your Elastic Email API key
        $mail->Password = ''; // Leave this empty
        $mail->Port = 2525; // Elastic Email SMTP port
        $mail->SMTPSecure = 'tls'; // Use TLS encryption

        // Get the recipient's email address from the form
        $recipientEmail = $_POST['email'];

        // Add the recipient's email address dynamically
        $mail->addAddress($recipientEmail);

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "<p>Name: $name</p><p>Email: $email</p><p>Message: $message</p>";

        // Send the email
        if ($mail->send()) {
            echo "success"; // Return a success response
        } else {
            echo "error"; // Return an error response
        }
    } catch (Exception $e) {
        echo "error"; // Return an error response
    }
}
?>
