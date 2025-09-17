<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Slot;
use App\Models\Booking;
use App\Utils\Session;
use App\Database\Database;

/**
 * Booking Controller
 * Handles booking-related operations
 */
class BookingController
{
    private $userModel;
    private $slotModel;
    private $bookingModel;
    private $db;

    public function __construct()
    {
        $this->userModel = new User();
        $this->slotModel = new Slot();
        $this->bookingModel = new Booking();
        $this->db = Database::getInstance();
    }

    /**
     * Show booking calendar
     */
    public function showBookingCalendar()
    {
        include __DIR__ . '/../../public/views/booking.php';
    }

    /**
     * Process booking form submission
     */
    public function createBooking()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToBooking();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToBooking();
            return;
        }

        $slotId = (int)($_POST['slot_id'] ?? 0);
        $advertiserName = trim($_POST['advertiser_name'] ?? '');
        $advertiserEmail = trim($_POST['advertiser_email'] ?? '');
        $advertiserPhone = trim($_POST['advertiser_phone'] ?? '');
        $companyName = trim($_POST['company_name'] ?? '');
        $advertisementMessage = trim($_POST['advertisement_message'] ?? '');

        // Validate input
        $errors = [];

        if (empty($slotId)) {
            $errors[] = 'Please select a valid time slot.';
        }

        if (empty($advertiserName)) {
            $errors[] = 'Name is required.';
        }

        if (empty($advertiserEmail) || !filter_var($advertiserEmail, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required.';
        }

        if (empty($advertiserPhone)) {
            $errors[] = 'Phone number is required.';
        }

        if (empty($advertisementMessage)) {
            $errors[] = 'Advertisement message is required.';
        }

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            $this->redirectToBooking();
            return;
        }

        try {
            // Start transaction
            $this->db->beginTransaction();

            // Check if slot exists and is available
            $slot = $this->slotModel->find($slotId);
            if (!$slot) {
                throw new \Exception('Selected slot not found.');
            }

            if ($slot['status'] !== 'available') {
                throw new \Exception('Selected slot is no longer available.');
            }

            // Check if slot is already booked
            if ($this->bookingModel->isSlotBooked($slotId)) {
                throw new \Exception('Selected slot has already been booked.');
            }

            // Find or create advertiser
            $advertiser = $this->userModel->findByEmail($advertiserEmail);
            
            if (!$advertiser) {
                // Create new advertiser
                $advertiserData = [
                    'name' => $advertiserName,
                    'email' => $advertiserEmail,
                    'password' => $this->generateTemporaryPassword(),
                    'role' => 'advertiser',
                    'phone' => $advertiserPhone,
                    'company' => $companyName,
                    'is_active' => true,
                    'email_verified_at' => date('Y-m-d H:i:s')
                ];

                $advertiserId = $this->userModel->createUser($advertiserData);
                
                // Log activity
                \App\Middleware\AuthMiddleware::logActivity('advertiser_created', "New advertiser created via booking: $advertiserEmail");
                
            } else {
                // Update existing advertiser info if needed
                $advertiserId = $advertiser['id'];
                
                $updateData = [];
                if ($advertiser['name'] !== $advertiserName) {
                    $updateData['name'] = $advertiserName;
                }
                if ($advertiser['phone'] !== $advertiserPhone) {
                    $updateData['phone'] = $advertiserPhone;
                }
                if ($advertiser['company'] !== $companyName) {
                    $updateData['company'] = $companyName;
                }
                
                if (!empty($updateData)) {
                    $this->userModel->update($advertiserId, $updateData);
                }
            }

            // Create booking
            $bookingData = [
                'advertiser_id' => $advertiserId,
                'slot_id' => $slotId,
                'status' => 'pending',
                'message' => $advertisementMessage,
                'total_amount' => $slot['price'],
                'payment_status' => 'pending'
            ];

            $bookingId = $this->bookingModel->create($bookingData);

            // Mark slot as booked
            $this->slotModel->markAsBooked($slotId);

            // Commit transaction
            $this->db->commit();

            // Log activity
            \App\Middleware\AuthMiddleware::logActivity('booking_created', "New booking created: #$bookingId for slot #$slotId");

            // Send email notification (placeholder for now)
            $this->sendBookingConfirmationEmail($advertiserEmail, $advertiserName, $slot, $bookingId);

            // Redirect to booking summary
            Session::setFlash('success', 'Your booking has been submitted successfully!');
            header("Location: /booking-summary/$bookingId");
            exit;

        } catch (\Exception $e) {
            // Rollback transaction
            $this->db->rollback();
            
            Session::setFlash('error', 'Booking failed: ' . $e->getMessage());
            $this->redirectToBooking();
            return;
        }
    }

    /**
     * Show booking summary
     */
    public function showBookingSummary($bookingId)
    {
        $booking = $this->bookingModel->findWithDetails($bookingId);
        
        if (!$booking) {
            Session::setFlash('error', 'Booking not found.');
            header('Location: /book');
            exit;
        }

        include __DIR__ . '/../../public/views/booking-summary.php';
    }

    /**
     * Confirm booking (final step)
     */
    public function confirmBooking($bookingId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToBooking();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToBooking();
            return;
        }

        try {
            $booking = $this->bookingModel->find($bookingId);
            
            if (!$booking) {
                throw new \Exception('Booking not found.');
            }

            if ($booking['status'] !== 'pending') {
                throw new \Exception('Booking has already been processed.');
            }

            // Update booking status to confirmed
            $this->bookingModel->update($bookingId, [
                'status' => 'confirmed',
                'payment_status' => 'paid'
            ]);

            // Log activity
            \App\Middleware\AuthMiddleware::logActivity('booking_confirmed', "Booking #$bookingId confirmed by advertiser");

            Session::setFlash('success', 'Your booking has been confirmed! You will receive an email confirmation shortly.');
            header('Location: /booking-success');
            exit;

        } catch (\Exception $e) {
            Session::setFlash('error', 'Confirmation failed: ' . $e->getMessage());
            header("Location: /booking-summary/$bookingId");
            exit;
        }
    }

    /**
     * Show booking success page
     */
    public function showBookingSuccess()
    {
        include __DIR__ . '/../../public/views/booking-success.php';
    }

    /**
     * Generate temporary password for new advertisers
     */
    private function generateTemporaryPassword()
    {
        return bin2hex(random_bytes(8));
    }

    /**
     * Send booking confirmation email (placeholder)
     */
    private function sendBookingConfirmationEmail($email, $name, $slot, $bookingId)
    {
        // This would integrate with PHPMailer in a real implementation
        // For now, we'll just log it
        error_log("Booking confirmation email would be sent to: $email for booking #$bookingId");
    }

    /**
     * Redirect to booking page
     */
    private function redirectToBooking()
    {
        header('Location: /book');
        exit;
    }
}
