<?php

namespace App\Utils;

use App\Database\Database;
use App\Utils\Email\EmailService;

/**
 * Notification Service
 * Handles both database notifications and email notifications
 */
class NotificationService
{
    private $db;
    private $emailService;

    public function __construct()
    {
        $this->db = Database::getInstance();
        $this->emailService = new EmailService();
    }

    /**
     * Create a database notification
     */
    public function createNotification($userId, $type, $title, $message, $data = null)
    {
        try {
            $sql = "
                INSERT INTO notifications (user_id, type, title, message, data, is_read) 
                VALUES (?, ?, ?, ?, ?, ?)
            ";
            
            $dataJson = $data ? json_encode($data) : null;
            
            $this->db->execute($sql, [
                $userId,
                $type,
                $title,
                $message,
                $dataJson,
                false
            ]);
            
            return $this->db->lastInsertId();
            
        } catch (\Exception $e) {
            error_log("Notification creation error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        try {
            $sql = "
                UPDATE notifications 
                SET is_read = 1, read_at = NOW() 
                WHERE id = ? AND user_id = ?
            ";
            
            return $this->db->execute($sql, [$notificationId, $userId]);
            
        } catch (\Exception $e) {
            error_log("Mark notification as read error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get user notifications
     */
    public function getUserNotifications($userId, $limit = 10, $unreadOnly = false)
    {
        try {
            $sql = "
                SELECT * FROM notifications 
                WHERE user_id = ?
            ";
            
            $params = [$userId];
            
            if ($unreadOnly) {
                $sql .= " AND is_read = 0";
            }
            
            $sql .= " ORDER BY created_at DESC LIMIT ?";
            $params[] = $limit;
            
            return $this->db->fetchAll($sql, $params);
            
        } catch (\Exception $e) {
            error_log("Get user notifications error: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Get unread notification count
     */
    public function getUnreadCount($userId)
    {
        try {
            $sql = "SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0";
            $result = $this->db->fetch($sql, [$userId]);
            return $result['count'];
            
        } catch (\Exception $e) {
            error_log("Get unread count error: " . $e->getMessage());
            return 0;
        }
    }

    /**
     * Send booking confirmation notification
     */
    public function sendBookingConfirmation($bookingData)
    {
        // Create database notification
        $this->createNotification(
            $bookingData['advertiser_id'],
            'booking_received',
            'Booking Confirmation',
            'Your booking has been received and is pending approval.',
            [
                'booking_id' => $bookingData['id'],
                'slot_date' => $bookingData['date'],
                'slot_time' => $bookingData['start_time'] . ' - ' . $bookingData['end_time']
            ]
        );

        // Send email notification
        $this->emailService->sendBookingConfirmation(
            $bookingData['advertiser_email'],
            $bookingData['advertiser_name'],
            $bookingData
        );
    }

    /**
     * Send booking approval notification
     */
    public function sendBookingApproval($bookingData)
    {
        // Create database notification
        $this->createNotification(
            $bookingData['advertiser_id'],
            'booking_approved',
            'Booking Approved',
            'Your booking has been approved and is confirmed for broadcast.',
            [
                'booking_id' => $bookingData['id'],
                'slot_date' => $bookingData['date'],
                'slot_time' => $bookingData['start_time'] . ' - ' . $bookingData['end_time']
            ]
        );

        // Send email notification
        $this->emailService->sendBookingApproval(
            $bookingData['advertiser_email'],
            $bookingData['advertiser_name'],
            $bookingData
        );
    }

    /**
     * Send booking rejection notification
     */
    public function sendBookingRejection($bookingData, $reason = null)
    {
        // Create database notification
        $this->createNotification(
            $bookingData['advertiser_id'],
            'booking_rejected',
            'Booking Rejected',
            'Your booking request could not be approved at this time.',
            [
                'booking_id' => $bookingData['id'],
                'slot_date' => $bookingData['date'],
                'slot_time' => $bookingData['start_time'] . ' - ' . $bookingData['end_time'],
                'reason' => $reason
            ]
        );

        // Send email notification
        $this->emailService->sendBookingRejection(
            $bookingData['advertiser_email'],
            $bookingData['advertiser_name'],
            $bookingData,
            $reason
        );
    }

    /**
     * Send account creation notification
     */
    public function sendAccountCreation($userId, $email, $name, $temporaryPassword)
    {
        // Create database notification
        $this->createNotification(
            $userId,
            'account_created',
            'Account Created',
            'Your account has been created successfully. Please log in to start booking slots.',
            ['temporary_password' => $temporaryPassword]
        );

        // Send email notification
        $this->emailService->sendAccountCreation($email, $name, $temporaryPassword);
    }

    /**
     * Send slot reminder notification
     */
    public function sendSlotReminder($bookingData)
    {
        // Create database notification
        $this->createNotification(
            $bookingData['advertiser_id'],
            'slot_reminder',
            'Upcoming Slot Reminder',
            'Your radio slot is scheduled soon. Please ensure your content is ready.',
            [
                'booking_id' => $bookingData['id'],
                'slot_date' => $bookingData['date'],
                'slot_time' => $bookingData['start_time'] . ' - ' . $bookingData['end_time']
            ]
        );

        // Send email notification
        $this->emailService->sendSlotReminder(
            $bookingData['advertiser_email'],
            $bookingData['advertiser_name'],
            $bookingData
        );
    }

    /**
     * Send payment reminder notification
     */
    public function sendPaymentReminder($bookingData)
    {
        // Create database notification
        $this->createNotification(
            $bookingData['advertiser_id'],
            'payment_reminder',
            'Payment Reminder',
            'Please complete your payment for the upcoming slot.',
            [
                'booking_id' => $bookingData['id'],
                'amount' => $bookingData['total_amount'],
                'due_date' => $bookingData['date']
            ]
        );

        // Send email notification (custom template for payment)
        $this->emailService->sendPaymentReminder(
            $bookingData['advertiser_email'],
            $bookingData['advertiser_name'],
            $bookingData
        );
    }

    /**
     * Send system notification to all users
     */
    public function sendSystemNotification($type, $title, $message, $data = null)
    {
        try {
            // Get all active users
            $sql = "SELECT id FROM users WHERE is_active = 1";
            $users = $this->db->fetchAll($sql);
            
            foreach ($users as $user) {
                $this->createNotification(
                    $user['id'],
                    $type,
                    $title,
                    $message,
                    $data
                );
            }
            
            return true;
            
        } catch (\Exception $e) {
            error_log("System notification error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Clean up old notifications
     */
    public function cleanupOldNotifications($daysOld = 30)
    {
        try {
            $sql = "
                DELETE FROM notifications 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL ? DAY) 
                AND is_read = 1
            ";
            
            return $this->db->execute($sql, [$daysOld]);
            
        } catch (\Exception $e) {
            error_log("Cleanup notifications error: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStats()
    {
        try {
            $stats = [];
            
            // Total notifications
            $sql = "SELECT COUNT(*) as count FROM notifications";
            $result = $this->db->fetch($sql);
            $stats['total'] = $result['count'];
            
            // Unread notifications
            $sql = "SELECT COUNT(*) as count FROM notifications WHERE is_read = 0";
            $result = $this->db->fetch($sql);
            $stats['unread'] = $result['count'];
            
            // Notifications by type
            $sql = "
                SELECT type, COUNT(*) as count 
                FROM notifications 
                GROUP BY type 
                ORDER BY count DESC
            ";
            $stats['by_type'] = $this->db->fetchAll($sql);
            
            // Recent notifications (last 7 days)
            $sql = "
                SELECT COUNT(*) as count 
                FROM notifications 
                WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
            ";
            $result = $this->db->fetch($sql);
            $stats['recent'] = $result['count'];
            
            return $stats;
            
        } catch (\Exception $e) {
            error_log("Get notification stats error: " . $e->getMessage());
            return [];
        }
    }
}
