<?php
// Correct autoload path for Composer
require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

function sendOTPEmail($to_email, $to_name, $otp_code) {
    $mail = new PHPMailer(true);
    
    try {
        // Server settings
        $mail->SMTPDebug = SMTP::DEBUG_OFF;  // Set to SMTP::DEBUG_SERVER for testing
        $mail->isSMTP();
        $mail->Host       = SMTP_HOST;
        $mail->SMTPAuth   = true;
        $mail->Username   = SMTP_USER;
        $mail->Password   = SMTP_PASS;
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = SMTP_PORT;
        
        // Recipients
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to_email, $to_name);
        
        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification - TrustNet Security';
        $mail->Body    = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; background: linear-gradient(135deg, #0A0F1C 0%, #0F172A 100%); }
                    .header { text-align: center; padding: 20px; }
                    .otp-code { font-size: 32px; font-weight: bold; color: #00D1FF; text-align: center; padding: 20px; letter-spacing: 5px; }
                    .content { background: rgba(255,255,255,0.1); padding: 20px; border-radius: 10px; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2 style='color: #00D1FF;'>TrustNet Security</h2>
                    </div>
                    <div class='content'>
                        <p style='color: white;'>Hello <strong>{$to_name}</strong>,</p>
                        <p style='color: white;'>Your OTP for email verification is:</p>
                        <div class='otp-code'>{$otp_code}</div>
                        <p style='color: white;'>This OTP is valid for 10 minutes.</p>
                        <p style='color: white;'>If you didn't request this, please ignore this email.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $mail->AltBody = "Your OTP for email verification is: {$otp_code}. Valid for 10 minutes.";
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}
?>