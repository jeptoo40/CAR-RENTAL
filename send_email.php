<?php
// Import PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Include the Composer autoloader
require 'vendor/autoload.php';

try {
    // Create a new PHPMailer instance
    $mail = new PHPMailer(true);

    //Server settings
    $mail->isSMTP(); // Set mailer to use SMTP
    $mail->Host = 'smtp.example.com'; // Your SMTP server
    $mail->SMTPAuth = true;
    $mail->Username = 'your-email@example.com'; // SMTP username
    $mail->Password = 'your-email-password'; // SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use STARTTLS for secure email
    $mail->Port = 587; // Port for STARTTLS

    //Recipients
    $mail->setFrom('your-email@example.com', 'Mailer');
    $mail->addAddress('recipient@example.com', 'Joe User'); // Add a recipient

    // Content
    $mail->isHTML(true);
    $mail->Subject = 'Test Email from PHPMailer';
    $mail->Body    = 'This is a test email sent from PHPMailer using SMTP and OpenSSL.';

    // Send the email
    $mail->send();
    echo 'Message has been sent successfully';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>
