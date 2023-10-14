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

        // SMTP configuration
        $mail->isSMTP();
        $mail->Host = 'smtp.elasticemail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'fayzan23@gmail.com'; // Replace with your Elastic Email username
        $mail->Password = '5A3A1BD816AC17D84661469B5A246EA8A128'; // Replace with your Elastic Email password
        $mail->Port = 2525; // Elastic Email SMTP port
        $mail->SMTPSecure = 'tls'; // Use TLS encryption

        // Sender (your email address)
        $mail->setFrom('fayzan23@gmail.com', 'Fayzan Bhatti'); // Replace with your email and name

        // Recipient (the email address you want to send the message to)
        $mail->addAddress('fayzan23@gmail.com'); // Replace with your email address

        // Email content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body = "<p>Name: $name</p><p>Email: $email</p><p>Message: $message</p>";

        // Send the email
        if ($mail->send()) {
            // Display success message with a green background
            echo '<div style="background-color: green; color: white; padding: 10px;">Message Successfully Sent!</div>';
        } else {
            // Display error message
            echo "ERROR: Message Could Not Be Sent!";
        }
    } catch (Exception $e) {
        // Display error message
        echo "ERROR: Message Could Not Be Sent!";
    }
}
?>
