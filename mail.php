<?php
// Import PHPMailer classes into the global namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Load Composer's autoloader
require 'mailer/autoload.php';

// Function to send email
function sendVerificationEmail($to, $subject, $body) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'samahomar831@gmail.com';  // Your Gmail address
        $mail->Password   = 'abti qnpd gzqc lsln';      // Your Gmail password or App Password
        $mail->SMTPSecure = "ssl";                      // Encryption
        $mail->Port       = 465;                        // SMTP Port

        // Recipients
        $mail->setFrom('samahomar831@gmail.com', 'Lab_techCare');
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);                            // Set email format to HTML
        $mail->Subject = $subject;
        $mail->Body    = $body;
        $mail->CharSet = "UTF-8";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return false;
    }
}
?>
