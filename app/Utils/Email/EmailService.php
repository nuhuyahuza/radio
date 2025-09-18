<?php

namespace App\Utils\Email;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception as MailerException;

/**
 * Email Service
 * Handles all email operations using PHPMailer
 */
class EmailService
{
    private $mailer;
    private $fromEmail;
    private $fromName;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->fromEmail = $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@zaaradio.com';
        $this->fromName = $_ENV['MAIL_FROM_NAME'] ?? 'Zaa Radio';
        
        $this->configureMailer();
    }

    /**
     * Configure PHPMailer settings
     */
    private function configureMailer()
    {
        try {
            // Server settings
            $host = $_ENV['MAIL_HOST'] ?? '';
            $username = $_ENV['MAIL_USERNAME'] ?? '';
            $password = $_ENV['MAIL_PASSWORD'] ?? '';
            $encryption = $_ENV['MAIL_ENCRYPTION'] ?? 'tls';
            $port = (int)($_ENV['MAIL_PORT'] ?? 587);

            if (!empty($host) && !empty($username) && !empty($password)) {
                $this->mailer->isSMTP();
                $this->mailer->Host = $host;
                $this->mailer->SMTPAuth = true;
                $this->mailer->Username = $username;
                $this->mailer->Password = $password;
                $this->mailer->SMTPSecure = $encryption;
                $this->mailer->Port = $port;
                // Allow self-signed certs in dev
                $this->mailer->SMTPOptions = [
                    'ssl' => [
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                        'allow_self_signed' => true,
                    ]
                ];
            } else {
                // Fallback to PHP mail() if SMTP not configured
                $this->mailer->isMail();
            }

            // Recipients
            $this->mailer->setFrom($this->fromEmail, $this->fromName);
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';

            // Optional debug logging
            $debug = (int)($_ENV['MAIL_DEBUG'] ?? 0);
            if ($debug > 0) {
                $this->mailer->SMTPDebug = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
                $logDir = dirname(__DIR__, 2) . '/storage/logs';
                if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
                $logFile = $logDir . '/mail.log';
                $this->mailer->Debugoutput = function ($str, $level) use ($logFile) {
                    @file_put_contents($logFile, '[' . date('c') . "] level=$level " . $str . "\n", FILE_APPEND);
                };
            }

        } catch (MailerException $e) {
            error_log("Email configuration error: " . $e->getMessage());
        }
    }

    /**
     * Send booking confirmation email
     */
    public function sendBookingConfirmation($toEmail, $toName, $bookingData)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->Subject = 'Booking Confirmation - Zaa Radio';

            $template = $this->getBookingConfirmationTemplate($bookingData);
            $this->mailer->Body = $template;

            $this->mailer->send();
            return true;

        } catch (MailerException $e) {
            error_log("Booking confirmation email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send booking approval email
     */
    public function sendBookingApproval($toEmail, $toName, $bookingData)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->Subject = 'Booking Approved - Zaa Radio';

            $template = $this->getBookingApprovalTemplate($bookingData);
            $this->mailer->Body = $template;

            $this->mailer->send();
            return true;

        } catch (MailerException $e) {
            error_log("Booking approval email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send booking rejection email
     */
    public function sendBookingRejection($toEmail, $toName, $bookingData, $reason = null)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->Subject = 'Booking Update - Zaa Radio';

            $template = $this->getBookingRejectionTemplate($bookingData, $reason);
            $this->mailer->Body = $template;

            $this->mailer->send();
            return true;

        } catch (MailerException $e) {
            error_log("Booking rejection email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send account creation email
     */
    public function sendAccountCreation($toEmail, $toName, $temporaryPassword)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->Subject = 'Welcome to Zaa Radio - Account Created';

            $template = $this->getAccountCreationTemplate($toName, $temporaryPassword);
            $this->mailer->Body = $template;

            $this->mailer->send();
            return true;

        } catch (MailerException $e) {
            error_log("Account creation email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send password reset email
     */
    public function sendPasswordReset($toEmail, $toName, $resetLink)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->Subject = 'Password Reset - Zaa Radio';

            $template = $this->getPasswordResetTemplate($toName, $resetLink);
            $this->mailer->Body = $template;

            $this->mailer->send();
            return true;

        } catch (MailerException $e) {
            error_log("Password reset email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Send slot reminder email
     */
    public function sendSlotReminder($toEmail, $toName, $slotData)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->Subject = 'Upcoming Radio Slot Reminder - Zaa Radio';

            $template = $this->getSlotReminderTemplate($slotData);
            $this->mailer->Body = $template;

            $this->mailer->send();
            return true;

        } catch (MailerException $e) {
            error_log("Slot reminder email error: " . $e->getMessage());
            return false;
        }

    }

    /**
     * Send payment reminder email
     */
    public function sendPaymentReminder($toEmail, $toName, $bookingData)
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($toEmail, $toName);
            $this->mailer->Subject = 'Payment Reminder - Zaa Radio';

            $amount = '$' . number_format($bookingData['total_amount'], 2);
            $date = date('F j, Y', strtotime($bookingData['date']));
            $template = "<p>Dear " . htmlspecialchars($toName) . ",</p>"
                      . "<p>This is a friendly reminder to complete your payment of <strong>{$amount}</strong> for the booking scheduled on <strong>{$date}</strong>.</p>"
                      . "<p>Booking ID: #" . (int)$bookingData['id'] . "</p>";
            $this->mailer->Body = $template;

            $this->mailer->send();
            return true;
        } catch (MailerException $e) {
            error_log("Payment reminder email error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get booking confirmation email template
     */
    private function getBookingConfirmationTemplate($bookingData)
    {
        $date = date('F j, Y', strtotime($bookingData['date']));
        $time = date('g:i A', strtotime($bookingData['start_time'])) . ' - ' . date('g:i A', strtotime($bookingData['end_time']));
        $amount = '$' . number_format($bookingData['total_amount'], 2);

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .highlight { color: #667eea; font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Booking Confirmed!</h1>
                    <p>Your radio advertisement slot has been successfully booked</p>
                </div>
                <div class='content'>
                    <h2>Hello {$bookingData['advertiser_name']},</h2>
                    <p>Thank you for choosing Zaa Radio for your advertising needs! Your booking has been confirmed and is pending approval.</p>
                    
                    <div class='booking-details'>
                        <h3>Booking Details</h3>
                        <p><strong>Booking ID:</strong> #{$bookingData['id']}</p>
                        <p><strong>Date:</strong> {$date}</p>
                        <p><strong>Time:</strong> {$time}</p>
                        <p><strong>Station:</strong> {$bookingData['station_name']}</p>
                        <p><strong>Amount:</strong> <span class='highlight'>{$amount}</span></p>
                        <p><strong>Status:</strong> Pending Approval</p>
                    </div>
                    
                    <h3>What happens next?</h3>
                    <ul>
                        <li>Our station manager will review your booking</li>
                        <li>You'll receive an approval notification within 24 hours</li>
                        <li>Payment will be processed upon approval</li>
                        <li>Your advertisement will air at the scheduled time</li>
                    </ul>
                    
                    <p>If you have any questions, please contact us at <a href='mailto:support@zaaradio.com'>support@zaaradio.com</a></p>
                </div>
                <div class='footer'>
                    <p>© 2024 Zaa Radio. All rights reserved.</p>
                    <p>This is an automated message, please do not reply.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Get booking approval email template
     */
    private function getBookingApprovalTemplate($bookingData)
    {
        $date = date('F j, Y', strtotime($bookingData['date']));
        $time = date('g:i A', strtotime($bookingData['start_time'])) . ' - ' . date('g:i A', strtotime($bookingData['end_time']));

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #28a745 0%, #20c997 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .highlight { color: #28a745; font-weight: bold; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>✅ Booking Approved!</h1>
                    <p>Your radio advertisement slot has been approved</p>
                </div>
                <div class='content'>
                    <h2>Great news, {$bookingData['advertiser_name']}!</h2>
                    <p>Your booking has been approved and is confirmed for broadcast.</p>
                    
                    <div class='booking-details'>
                        <h3>Approved Booking Details</h3>
                        <p><strong>Booking ID:</strong> #{$bookingData['id']}</p>
                        <p><strong>Date:</strong> {$date}</p>
                        <p><strong>Time:</strong> {$time}</p>
                        <p><strong>Station:</strong> {$bookingData['station_name']}</p>
                        <p><strong>Status:</strong> <span class='highlight'>Approved</span></p>
                    </div>
                    
                    <h3>Important Reminders</h3>
                    <ul>
                        <li>Your advertisement will air at the scheduled time</li>
                        <li>Please ensure your content is ready for broadcast</li>
                        <li>You'll receive a reminder 24 hours before your slot</li>
                        <li>Contact us immediately if you need to make changes</li>
                    </ul>
                    
                    <p>Thank you for choosing Zaa Radio!</p>
                </div>
                <div class='footer'>
                    <p>© 2024 Zaa Radio. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Get booking rejection email template
     */
    private function getBookingRejectionTemplate($bookingData, $reason)
    {
        $date = date('F j, Y', strtotime($bookingData['date']));
        $time = date('g:i A', strtotime($bookingData['start_time'])) . ' - ' . date('g:i A', strtotime($bookingData['end_time']));

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #dc3545 0%, #e74c3c 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .booking-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>❌ Booking Update</h1>
                    <p>Your booking request could not be approved</p>
                </div>
                <div class='content'>
                    <h2>Hello {$bookingData['advertiser_name']},</h2>
                    <p>We regret to inform you that your booking request could not be approved at this time.</p>
                    
                    <div class='booking-details'>
                        <h3>Booking Details</h3>
                        <p><strong>Booking ID:</strong> #{$bookingData['id']}</p>
                        <p><strong>Date:</strong> {$date}</p>
                        <p><strong>Time:</strong> {$time}</p>
                        <p><strong>Station:</strong> {$bookingData['station_name']}</p>
                        <p><strong>Status:</strong> Rejected</p>
                    </div>
                    
                    " . ($reason ? "<p><strong>Reason:</strong> {$reason}</p>" : "") . "
                    
                    <h3>What you can do next</h3>
                    <ul>
                        <li>Try booking a different time slot</li>
                        <li>Contact us to discuss alternative options</li>
                        <li>Check our calendar for other available slots</li>
                    </ul>
                    
                    <p>We apologize for any inconvenience and hope to serve you in the future.</p>
                </div>
                <div class='footer'>
                    <p>© 2024 Zaa Radio. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Get account creation email template
     */
    private function getAccountCreationTemplate($name, $temporaryPassword)
    {
        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .credentials { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; border-left: 4px solid #667eea; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🎉 Welcome to Zaa Radio!</h1>
                    <p>Your account has been created successfully</p>
                </div>
                <div class='content'>
                    <h2>Hello {$name},</h2>
                    <p>Welcome to Zaa Radio! Your account has been created and you can now book radio advertisement slots.</p>
                    
                    <div class='credentials'>
                        <h3>Your Login Credentials</h3>
                        <p><strong>Email:</strong> Your registered email address</p>
                        <p><strong>Temporary Password:</strong> <code style='background: #f8f9fa; padding: 4px 8px; border-radius: 4px;'>{$temporaryPassword}</code></p>
                        <p><em>Please change your password after your first login for security.</em></p>
                    </div>
                    
                    <h3>Getting Started</h3>
                    <ul>
                        <li>Log in to your account using the credentials above</li>
                        <li>Browse available radio slots on our calendar</li>
                        <li>Book your preferred time slot</li>
                        <li>Manage your bookings from your dashboard</li>
                    </ul>
                    
                    <p><a href='http://localhost:8080/login' style='background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>Login to Your Account</a></p>
                </div>
                <div class='footer'>
                    <p>© 2024 Zaa Radio. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Get password reset email template
     */
    private function getPasswordResetTemplate($name, $resetLink)
    {
        // $resetLink is a full URL

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .reset-link { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; text-align: center; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>🔐 Password Reset</h1>
                    <p>Reset your Zaa Radio account password</p>
                </div>
                <div class='content'>
                    <h2>Hello {$name},</h2>
                    <p>We received a request to reset your password for your Zaa Radio account.</p>
                    
                    <div class='reset-link'>
                        <p>Click the button below to reset your password:</p>
                        <a href='{$resetLink}' style='background: #667eea; color: white; padding: 12px 24px; text-decoration: none; border-radius: 6px; display: inline-block;'>Reset Password</a>
                        <p style='margin-top: 15px; font-size: 14px; color: #666;'>This link will expire in 1 hour for security reasons.</p>
                    </div>
                    
                    <p>If you didn't request this password reset, please ignore this email. Your password will remain unchanged.</p>
                </div>
                <div class='footer'>
                    <p>© 2024 Zaa Radio. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }

    /**
     * Get slot reminder email template
     */
    private function getSlotReminderTemplate($slotData)
    {
        $date = date('F j, Y', strtotime($slotData['date']));
        $time = date('g:i A', strtotime($slotData['start_time'])) . ' - ' . date('g:i A', strtotime($slotData['end_time']));

        return "
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset='UTF-8'>
            <style>
                body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                .header { background: linear-gradient(135deg, #ffc107 0%, #ff8c00 100%); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
                .content { background: #f9f9f9; padding: 30px; border-radius: 0 0 10px 10px; }
                .reminder-details { background: white; padding: 20px; border-radius: 8px; margin: 20px 0; }
                .footer { text-align: center; margin-top: 30px; color: #666; font-size: 14px; }
            </style>
        </head>
        <body>
            <div class='container'>
                <div class='header'>
                    <h1>⏰ Reminder</h1>
                    <p>Your radio slot is coming up soon</p>
                </div>
                <div class='content'>
                    <h2>Hello {$slotData['advertiser_name']},</h2>
                    <p>This is a friendly reminder that your radio advertisement slot is scheduled soon.</p>
                    
                    <div class='reminder-details'>
                        <h3>Upcoming Slot Details</h3>
                        <p><strong>Date:</strong> {$date}</p>
                        <p><strong>Time:</strong> {$time}</p>
                        <p><strong>Station:</strong> {$slotData['station_name']}</p>
                        <p><strong>Booking ID:</strong> #{$slotData['booking_id']}</p>
                    </div>
                    
                    <h3>Final Preparations</h3>
                    <ul>
                        <li>Ensure your advertisement content is ready</li>
                        <li>Test any audio files or scripts</li>
                        <li>Contact us if you need to make any last-minute changes</li>
                        <li>Be ready for your scheduled broadcast time</li>
                    </ul>
                    
                    <p>Good luck with your advertisement!</p>
                </div>
                <div class='footer'>
                    <p>© 2024 Zaa Radio. All rights reserved.</p>
                </div>
            </div>
        </body>
        </html>";
    }
}