<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Slot;
use App\Models\Booking;
use App\Utils\Session;
use App\Utils\NotificationService;
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
    private $notificationService;
    private $db;

    public function __construct()
    {
        $this->userModel = new User();
        $this->slotModel = new Slot();
        $this->bookingModel = new Booking();
        $this->notificationService = new NotificationService();
        $this->db = Database::getInstance();
    }

    /**
     * Show booking calendar
     */
    public function showBookingCalendar()
    {
        $token = \App\Utils\Session::getCsrfToken() ?: \App\Utils\Session::setCsrfToken();
        // Set double-submit cookie (readable by JS)
        setcookie('XSRF-TOKEN', $token, [
            'expires' => 0,
            'path' => '/',
            'secure' => (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'),
            'httponly' => false,
            'samesite' => 'Lax'
        ]);
        include __DIR__ . '/../../public/views/booking.php';
    }

    /**
     * Process booking form submission - now creates a draft only
     */
    public function createBooking()
    {
        \App\Utils\Session::start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
            } else {
                $this->redirectToBooking();
            }
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse(['success' => false, 'message' => 'Invalid security token. Please try again.'], 400);
            } else {
                Session::setFlash('error', 'Invalid security token. Please try again.');
                $this->redirectToBooking();
            }
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
        if (empty($slotId)) { $errors[] = 'Please select a valid time slot.'; }
        if (empty($advertiserName)) { $errors[] = 'Name is required.'; }
        if (empty($advertiserEmail) || !filter_var($advertiserEmail, FILTER_VALIDATE_EMAIL)) { $errors[] = 'Valid email is required.'; }
        if (empty($advertiserPhone)) { $errors[] = 'Phone number is required.'; }
        if (empty($advertisementMessage)) { $errors[] = 'Advertisement message is required.'; }
        if (!empty($errors)) {
            if ($this->isAjaxRequest()) {
                $this->jsonResponse(['success' => false, 'message' => implode(' ', $errors)], 400);
            } else {
                Session::setFlash('error', implode(' ', $errors));
                $this->redirectToBooking();
            }
            return;
        }

        // Validate slot is available
        $slot = $this->slotModel->find($slotId);
        if (!$slot) {
            if ($this->isAjaxRequest()) { $this->jsonResponse(['success' => false, 'message' => 'Selected slot not found.'], 404); return; }
            Session::setFlash('error', 'Selected slot not found.');
            $this->redirectToBooking();
            return;
        }
        if ($slot['status'] !== 'available' || $this->bookingModel->isSlotBooked($slotId)) {
            if ($this->isAjaxRequest()) { $this->jsonResponse(['success' => false, 'message' => 'Selected slot is no longer available.'], 409); return; }
            Session::setFlash('error', 'Selected slot is no longer available.');
            $this->redirectToBooking();
            return;
        }

        // Build draft and store in session
        $draft = [
            'slot' => [
                'id' => $slot['id'],
                'date' => $slot['date'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'price' => $slot['price'],
                'station_name' => 'Zaa Radio'
            ],
            'advertiser' => [
                'name' => $advertiserName,
                'email' => $advertiserEmail,
                'phone' => $advertiserPhone,
                'company' => $companyName,
            ],
            'message' => $advertisementMessage
        ];
        Session::set('booking_draft', $draft);

        // Return success -> redirect to draft summary (no DB writes yet)
        if ($this->isAjaxRequest()) {
            $this->jsonResponse(['success' => true, 'redirect' => '/booking-summary']);
            return;
        }
        header('Location: /booking-summary');
        exit;
    }

    /**
     * Show draft booking summary (from session)
     */
    public function showDraftSummary()
    {
        $draft = Session::get('booking_draft');
        if ($draft) {
            // Map to view variables expected by summary template
            $booking = [
                'id' => null,
                'status' => 'draft',
                'date' => $draft['slot']['date'],
                'start_time' => $draft['slot']['start_time'],
                'end_time' => $draft['slot']['end_time'],
                'station_name' => $draft['slot']['station_name'],
                'total_amount' => $draft['slot']['price'],
                'advertiser_name' => $draft['advertiser']['name'],
                'advertiser_email' => $draft['advertiser']['email'],
                'advertiser_phone' => $draft['advertiser']['phone'],
                'company_name' => $draft['advertiser']['company'],
                'message' => $draft['message'],
            ];
        }
        // Always include the summary view; it can hydrate from sessionStorage if no server draft
        include __DIR__ . '/../../public/views/booking-summary.php';
    }

    /**
     * Confirm draft -> persist booking and send notifications
     */
    public function confirmDraft()
    {
        \App\Utils\Session::start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirectToBooking(); return; }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) { Session::setFlash('error', 'Invalid security token. Please try again.'); $this->redirectToBooking(); return; }

        // Prefer client-provided draft (from sessionStorage)
        $clientPayload = $_POST['draft_payload'] ?? '';
        if ($clientPayload) {
            try { $draft = json_decode($clientPayload, true, 512, JSON_THROW_ON_ERROR); }
            catch (\Throwable $e) { $draft = null; }
        }
        if (empty($draft)) { $draft = Session::get('booking_draft'); }
        if (!$draft) { Session::setFlash('error', 'No booking draft found.'); $this->redirectToBooking(); return; }

        // Minimal validation
        $slotId = (int)($draft['slot']['id'] ?? 0);
        if (!$slotId) { Session::setFlash('error', 'Invalid slot.'); $this->redirectToBooking(); return; }
        $slot = $this->slotModel->find($slotId);
        if (!$slot || $slot['status'] !== 'available' || $this->bookingModel->isSlotBooked($slotId)) {
            Session::setFlash('error', 'Selected slot is no longer available.');
            $this->redirectToBooking();
            return;
        }

        try {
            $this->db->beginTransaction();

            // Find or create advertiser
            $email = trim($draft['advertiser']['email'] ?? '');
            $advertiser = $email ? $this->userModel->findByEmail($email) : null;
            if (!$advertiser) {
                $tempPassword = $this->generateTemporaryPassword();
                $advertiserId = $this->userModel->createUser([
                    'name' => trim($draft['advertiser']['name'] ?? ''),
                    'email' => $email,
                    'password' => $tempPassword,
                    'role' => 'advertiser',
                    'phone' => trim($draft['advertiser']['phone'] ?? ''),
                    'company' => trim($draft['advertiser']['company'] ?? ''),
                    'is_active' => true,
                    'email_verified_at' => date('Y-m-d H:i:s')
                ]);
                $this->notificationService->sendAccountCreation($advertiserId, $email, trim($draft['advertiser']['name'] ?? ''), $tempPassword);
            } else {
                $advertiserId = $advertiser['id'];
            }

            // Persist booking
            $bookingId = $this->bookingModel->create([
                'advertiser_id' => $advertiserId,
                'slot_id' => $slotId,
                'status' => 'pending',
                'message' => trim($draft['message'] ?? ''),
                'total_amount' => $slot['price'],
                'payment_status' => 'pending'
            ]);

            // Mark slot as booked
            $this->slotModel->markAsBooked($slotId);

            $this->db->commit();

            // Send booking confirmation
            $this->notificationService->sendBookingConfirmation([
                'id' => $bookingId,
                'advertiser_id' => $advertiserId,
                'advertiser_name' => trim($draft['advertiser']['name'] ?? ''),
                'advertiser_email' => $email,
                'date' => $slot['date'],
                'start_time' => $slot['start_time'],
                'end_time' => $slot['end_time'],
                'station_name' => 'Zaa Radio',
                'total_amount' => $slot['price']
            ]);

            // Clear any server-side draft
            Session::set('booking_draft', null);

            Session::setFlash('success', 'Your booking has been submitted successfully!');
            header("Location: /booking-summary/$bookingId");
            exit;
        } catch (\Exception $e) {
            $this->db->rollback();
            Session::setFlash('error', 'Confirmation failed: ' . $e->getMessage());
            header('Location: /booking-summary');
            exit;
        }
    }

    /**
     * Cancel draft and return to booking
     */
    public function cancelDraft()
    {
        \App\Utils\Session::start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { $this->redirectToBooking(); return; }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) { Session::setFlash('error', 'Invalid security token. Please try again.'); $this->redirectToBooking(); return; }
        Session::set('booking_draft', null);
        Session::setFlash('success', 'Booking cancelled.');
        $this->redirectToBooking();
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
     * Check if request is AJAX
     */
    private function isAjaxRequest()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }

    /**
     * Send JSON response
     */
    private function jsonResponse($data, $httpCode = 200)
    {
        http_response_code($httpCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
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