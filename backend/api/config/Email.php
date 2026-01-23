<?php
require_once __DIR__ . '/env.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Email {
    private $mailer;
    
    public function __construct() {
        $this->mailer = new PHPMailer(true);
        
        // Disable debug output
        $this->mailer->SMTPDebug = 0;
        $this->mailer->Debugoutput = function($str, $level) {
            // Suppress all debug output
        };
        
        // Server settings from environment variables
        $this->mailer->isSMTP();
        $this->mailer->Host = env('SMTP_HOST', 'smtp.gmail.com');
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = env('SMTP_USERNAME');
        $this->mailer->Password = env('SMTP_PASSWORD');
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port = env('SMTP_PORT', 587);
        
        // Default sender from environment variables
        $this->mailer->setFrom(
            env('SMTP_FROM_EMAIL'), 
            env('SMTP_FROM_NAME', 'St. Lawrence Junior School')
        );
    }
    
    public function sendReply($to, $toName, $subject, $message) {
        try {
            // Clear any previous recipients and attachments
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            $this->mailer->addAddress($to, $toName);
            $this->mailer->Subject = 'Re: ' . $subject;
            $this->mailer->isHTML(true);
            
            // Attach logo as embedded image
            $logoPath = __DIR__ . '/../../../img/5.jpg';
            if (file_exists($logoPath)) {
                $this->mailer->addEmbeddedImage($logoPath, 'school_logo', 'school_logo.jpg', 'base64', 'image/jpeg');
                $logoSrc = 'cid:school_logo';
            } else {
                // Fallback to a placeholder if logo not found
                $logoSrc = 'https://via.placeholder.com/100x100/0066cc/ffffff?text=SLJS';
            }
            
            $htmlMessage = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <style>
                        body {
                            margin: 0;
                            padding: 0;
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                            background-color: #f4f4f4;
                        }
                        .email-container {
                            max-width: 600px;
                            margin: 20px auto;
                            background: #ffffff;
                            border-radius: 12px;
                            overflow: hidden;
                            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                        }
                        .header {
                            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
                            padding: 40px 30px;
                            text-align: center;
                            position: relative;
                        }
                        .header::after {
                            content: '';
                            position: absolute;
                            bottom: -20px;
                            left: 0;
                            right: 0;
                            height: 20px;
                            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
                            clip-path: polygon(0 0, 100% 0, 100% 100%, 0 0);
                        }
                        .logo {
                            width: 100px;
                            height: 100px;
                            background: white;
                            border-radius: 50%;
                            margin: 0 auto 20px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                            padding: 10px;
                        }
                        .logo img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            border-radius: 50%;
                        }
                        .school-name {
                            color: white;
                            font-size: 24px;
                            font-weight: bold;
                            margin: 0;
                            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
                        }
                        .school-tagline {
                            color: rgba(255,255,255,0.9);
                            font-size: 14px;
                            margin: 5px 0 0;
                        }
                        .content {
                            padding: 50px 30px 30px;
                        }
                        .greeting {
                            font-size: 18px;
                            color: #333;
                            margin-bottom: 20px;
                        }
                        .message-box {
                            background: #f8f9fa;
                            border-left: 4px solid #0066cc;
                            padding: 20px;
                            margin: 25px 0;
                            border-radius: 8px;
                            line-height: 1.6;
                            color: #555;
                        }
                        .signature {
                            margin-top: 30px;
                            padding-top: 20px;
                            border-top: 2px solid #e9ecef;
                        }
                        .signature-name {
                            font-weight: bold;
                            color: #0066cc;
                            margin-bottom: 5px;
                        }
                        .signature-title {
                            color: #666;
                            font-size: 14px;
                        }
                        .footer {
                            background: #f8f9fa;
                            padding: 30px;
                            text-align: center;
                            border-top: 3px solid #0066cc;
                        }
                        .contact-info {
                            display: flex;
                            justify-content: center;
                            gap: 20px;
                            margin: 20px 0;
                            flex-wrap: wrap;
                        }
                        .contact-item {
                            display: flex;
                            align-items: center;
                            gap: 8px;
                            color: #666;
                            font-size: 13px;
                        }
                        .contact-icon {
                            width: 20px;
                            height: 20px;
                            background: #0066cc;
                            border-radius: 50%;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            color: white;
                            font-size: 10px;
                        }
                        .social-links {
                            margin: 20px 0;
                        }
                        .social-link {
                            display: inline-block;
                            width: 35px;
                            height: 35px;
                            background: #0066cc;
                            border-radius: 50%;
                            margin: 0 5px;
                            text-decoration: none;
                            color: white;
                            line-height: 35px;
                            transition: all 0.3s ease;
                        }
                        .social-link:hover {
                            background: #dc3545;
                            transform: translateY(-3px);
                        }
                        .footer-note {
                            color: #999;
                            font-size: 12px;
                            margin-top: 20px;
                            line-height: 1.5;
                        }
                        .cta-button {
                            display: inline-block;
                            padding: 12px 30px;
                            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
                            color: white;
                            text-decoration: none;
                            border-radius: 25px;
                            margin: 20px 0;
                            font-weight: bold;
                            box-shadow: 0 4px 15px rgba(0,102,204,0.3);
                        }
                        @media only screen and (max-width: 600px) {
                            .email-container {
                                margin: 0;
                                border-radius: 0;
                            }
                            .content {
                                padding: 30px 20px;
                            }
                            .contact-info {
                                flex-direction: column;
                                gap: 10px;
                            }
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <!-- Header with Logo -->
                        <div class='header'>
                            <div class='logo'>
                                <img src='{$logoSrc}' alt='St. Lawrence Logo'>
                            </div>
                            <h1 class='school-name'>ST. LAWRENCE JUNIOR SCHOOL</h1>
                            <p class='school-tagline'>KABOWA</p>
                            <p class='school-tagline' style='font-style: italic; font-size: 12px; margin-top: 5px;'>We Strive To Excel</p>
                        </div>
                        
                        <!-- Main Content -->
                        <div class='content'>
                            <p class='greeting'>Dear {$toName},</p>
                            
                            <p style='color: #555; line-height: 1.6;'>
                                Thank you for reaching out to St. Lawrence Junior School. We appreciate your interest and are pleased to respond to your inquiry.
                            </p>
                            
                            <div class='message-box'>
                                " . nl2br(htmlspecialchars($message)) . "
                            </div>
                            
                            <p style='color: #555; line-height: 1.6;'>
                                If you have any further questions or would like to schedule a visit to our campus, please don't hesitate to contact us. We would be delighted to show you our facilities and discuss how we can support your child's educational journey.
                            </p>
                            
                            <div class='signature'>
                                <div class='signature-name'>St. Lawrence Junior School Team</div>
                                <div class='signature-title'>Admissions & Communications Office</div>
                            </div>
                        </div>
                        
                        <!-- Footer -->
                        <div class='footer'>
                            <div style='background: #e8f4f8; padding: 30px; border-radius: 10px; text-align: left; max-width: 500px; margin: 0 auto;'>
                                <h3 style='color: #0066cc; font-size: 22px; margin: 0 0 20px 0; display: flex; align-items: center; gap: 10px;'>
                                    <span style='font-size: 28px;'>📞</span> Contact Us
                                </h3>
                                
                                <p style='color: #333; font-size: 15px; margin: 12px 0; line-height: 1.8;'>
                                    <strong>Phone:</strong> +256 772 420 506 / +256 701 420 506
                                </p>
                                
                                <p style='color: #333; font-size: 15px; margin: 12px 0; line-height: 1.8;'>
                                    <strong>Email:</strong> <a href='mailto:stlawrencejuniorschoolkabowa@gmail.com' style='color: #0066cc; text-decoration: none;'>stlawrencejuniorschoolkabowa@gmail.com</a>
                                </p>
                                
                                <p style='color: #333; font-size: 15px; margin: 12px 0; line-height: 1.8;'>
                                    <strong>Location:</strong> Kabowa, Kampala, Uganda
                                </p>
                                
                                <p style='color: #333; font-size: 15px; margin: 12px 0; line-height: 1.8;'>
                                    <strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM
                                </p>
                            </div>
                            
                            <p class='footer-note' style='margin-top: 30px;'>
                                This email was sent from St. Lawrence Junior School - Kabowa.<br>
                                You are receiving this because you contacted us through our website.<br>
                                © 2026 St. Lawrence Junior School. All rights reserved.
                            </p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $this->mailer->Body = $htmlMessage;
            $this->mailer->AltBody = strip_tags($message);
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
    
    public function sendEmail($to, $subject, $message) {
        try {
            // Clear any previous addresses
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();
            
            // Validate email
            if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
                error_log("Invalid email address: " . $to);
                return false;
            }
            
            // Log attempt
            error_log("=== EMAIL SEND ATTEMPT ===");
            error_log("To: " . $to);
            error_log("Subject: " . $subject);
            
            $this->mailer->addAddress($to);
            $this->mailer->Subject = $subject;
            $this->mailer->isHTML(true);
            
            // Attach logo as embedded image
            $logoPath = __DIR__ . '/../../../img/5.jpg';
            if (file_exists($logoPath)) {
                $this->mailer->addEmbeddedImage($logoPath, 'school_logo', 'school_logo.jpg', 'base64', 'image/jpeg');
                $logoSrc = 'cid:school_logo';
            } else {
                error_log("Logo file not found at: " . $logoPath);
                $logoSrc = 'https://via.placeholder.com/100x100/0066cc/ffffff?text=SLJS';
            }
            
            $htmlMessage = "
                <!DOCTYPE html>
                <html>
                <head>
                    <meta charset='UTF-8'>
                    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
                    <style>
                        body {
                            margin: 0;
                            padding: 0;
                            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                            background-color: #f4f4f4;
                        }
                        .email-container {
                            max-width: 600px;
                            margin: 20px auto;
                            background: #ffffff;
                            border-radius: 12px;
                            overflow: hidden;
                            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
                        }
                        .header {
                            background: linear-gradient(135deg, #0066cc 0%, #0052a3 100%);
                            padding: 40px 30px;
                            text-align: center;
                        }
                        .logo {
                            width: 100px;
                            height: 100px;
                            background: white;
                            border-radius: 50%;
                            margin: 0 auto 20px;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
                            padding: 10px;
                        }
                        .logo img {
                            width: 100%;
                            height: 100%;
                            object-fit: cover;
                            border-radius: 50%;
                        }
                        .school-name {
                            color: white;
                            font-size: 24px;
                            font-weight: bold;
                            margin: 0;
                            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
                        }
                        .school-tagline {
                            color: rgba(255,255,255,0.9);
                            font-size: 14px;
                            margin: 5px 0 0;
                        }
                        .content {
                            padding: 40px 30px;
                        }
                        .message-content {
                            color: #555;
                            line-height: 1.8;
                            font-size: 15px;
                        }
                        .footer {
                            background: #f8f9fa;
                            padding: 30px;
                            text-align: center;
                            border-top: 3px solid #0066cc;
                        }
                        .contact-box {
                            background: #e8f4f8;
                            padding: 25px;
                            border-radius: 10px;
                            text-align: left;
                            max-width: 500px;
                            margin: 20px auto;
                        }
                        .contact-box h3 {
                            color: #0066cc;
                            font-size: 20px;
                            margin: 0 0 15px 0;
                        }
                        .contact-box p {
                            color: #333;
                            font-size: 14px;
                            margin: 10px 0;
                            line-height: 1.6;
                        }
                        .footer-note {
                            color: #999;
                            font-size: 12px;
                            margin-top: 20px;
                            line-height: 1.5;
                        }
                    </style>
                </head>
                <body>
                    <div class='email-container'>
                        <div class='header'>
                            <div class='logo'>
                                <img src='{$logoSrc}' alt='St. Lawrence Logo'>
                            </div>
                            <h1 class='school-name'>ST. LAWRENCE JUNIOR SCHOOL</h1>
                            <p class='school-tagline'>KABOWA</p>
                            <p class='school-tagline' style='font-style: italic; font-size: 12px;'>We Strive To Excel</p>
                        </div>
                        
                        <div class='content'>
                            <div class='message-content'>
                                {$message}
                            </div>
                        </div>
                        
                        <div class='footer'>
                            <div class='contact-box'>
                                <h3>📞 Contact Us</h3>
                                <p><strong>Phone:</strong> +256 772 420 506 / +256 701 420 506</p>
                                <p><strong>Email:</strong> <a href='mailto:stlawrencejuniorschoolkabowa@gmail.com' style='color: #0066cc;'>stlawrencejuniorschoolkabowa@gmail.com</a></p>
                                <p><strong>Location:</strong> Kabowa, Kampala, Uganda</p>
                                <p><strong>Office Hours:</strong> Monday - Friday, 8:00 AM - 5:00 PM</p>
                            </div>
                            
                            <p class='footer-note'>
                                © 2026 St. Lawrence Junior School. All rights reserved.
                            </p>
                        </div>
                    </div>
                </body>
                </html>
            ";
            
            $this->mailer->Body = $htmlMessage;
            $this->mailer->AltBody = strip_tags($message);
            
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Email error: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}
