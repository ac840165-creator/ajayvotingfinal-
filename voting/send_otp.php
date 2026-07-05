<?php
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function sendOTP($email, $otp) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'ravalvijay14905@gmail.com'; 
        $mail->Password = 'ipnhbxxtasnomoqe';  
        $mail->SMTPSecure = 'tls';
        $mail->Port = 587;

        $mail->setFrom('ravalvijay14905@gmail.com', 'E-Voting');
        $mail->addAddress($email);

        $mail->isHTML(true);
        $mail->Subject = 'OTP Verification';
        $mail->Body = "<h2>Your OTP: $otp</h2>";

        $mail->send();
        $_SESSION['otp_status'] = 'sent';
        return true;

    } catch (Exception $e) {
        error_log("PHPMailer Error: " . $mail->ErrorInfo);
        $_SESSION['otp_status'] = 'failed_local';
        return false;
    }
}