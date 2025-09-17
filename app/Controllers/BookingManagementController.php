<?php

namespace App\Controllers;

use App\Models\Booking;
use App\Models\Slot;
use App\Models\User;
use App\Utils\Session;
use App\Utils\NotificationService;
use App\Middleware\AuthMiddleware;

/**
 * Booking Management Controller
 * Handles booking approval/rejection workflow for station managers
 */
class BookingManagementController
{
    private $bookingModel;
    private $slotModel;
    private $userModel;
    private $notificationService;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->slotModel = new Slot();
        $this->userModel = new User();
        $this->notificationService = new NotificationService();
    }

    /**
     * Show bookings management page
     */
    public function showBookingsManagement()
    {
        AuthMiddleware::requireManager();
        
        $user = Session::getUser();
        
        // Get filter parameters
        $status = $_GET['status'] ?? 'all';
        $page = (int)($_GET['page'] ?? 1);
        $limit = 20;
        $offset = ($page - 1) * $limit;
        
        // Get bookings based on filter
        if ($status === 'all') {
            $bookings = $this->bookingModel->findAllWithDetails($limit, $offset);
            $totalBookings = $this->bookingModel->count();
        } else {
            $bookings = $this->bookingModel->findByStatus($status);
            $totalBookings = $this->bookingModel->countWhere('status', $status);
        }
        
        // Get booking statistics
        $stats = $this->bookingModel->getStats();
        
        // Get pending bookings for quick actions
        $pendingBookings = $this->bookingModel->findPending();
        
        include __DIR__ . '/../../public/views/manager/bookings.php';
    }

    /**
     * Show booking details
     */
    public function showBookingDetails($bookingId)
    {
        AuthMiddleware::requireManager();
        
        $booking = $this->bookingModel->findWithDetails($bookingId);
        
        if (!$booking) {
            Session::setFlash('error', 'Booking not found.');
            header('Location: /manager/bookings');
            exit;
        }
        
        include __DIR__ . '/../../public/views/manager/booking-details.php';
    }

    /**
     * Approve booking
     */
    public function approveBooking($bookingId)
    {
        AuthMiddleware::requireManager();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToBookings();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToBookings();
            return;
        }

        try {
            $booking = $this->bookingModel->find($bookingId);
            
            if (!$booking) {
                Session::setFlash('error', 'Booking not found.');
                $this->redirectToBookings();
                return;
            }

            if ($booking['status'] !== 'pending') {
                Session::setFlash('error', 'This booking has already been processed.');
                $this->redirectToBookings();
                return;
            }

            $user = Session::getUser();
            
            // Approve booking
            $this->bookingModel->approve($bookingId, $user['id']);
            
            // Get booking details for notification
            $bookingDetails = $this->bookingModel->findWithDetails($bookingId);
            
            // Send approval notification
            $this->notificationService->sendBookingApproval($bookingDetails);
            
            // Log activity
            AuthMiddleware::logActivity('booking_approved', "Booking #$bookingId approved by manager");
            
            Session::setFlash('success', 'Booking approved successfully!');
            header('Location: /manager/bookings');
            exit;

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to approve booking: ' . $e->getMessage());
            $this->redirectToBookings();
            return;
        }
    }

    /**
     * Reject booking
     */
    public function rejectBooking($bookingId)
    {
        AuthMiddleware::requireManager();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToBookings();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToBookings();
            return;
        }

        $reason = trim($_POST['reason'] ?? '');

        try {
            $booking = $this->bookingModel->find($bookingId);
            
            if (!$booking) {
                Session::setFlash('error', 'Booking not found.');
                $this->redirectToBookings();
                return;
            }

            if ($booking['status'] !== 'pending') {
                Session::setFlash('error', 'This booking has already been processed.');
                $this->redirectToBookings();
                return;
            }

            $user = Session::getUser();
            
            // Reject booking
            $this->bookingModel->reject($bookingId, $user['id'], $reason);
            
            // Make slot available again
            $this->slotModel->markAsAvailable($booking['slot_id']);
            
            // Get booking details for notification
            $bookingDetails = $this->bookingModel->findWithDetails($bookingId);
            
            // Send rejection notification
            $this->notificationService->sendBookingRejection($bookingDetails, $reason);
            
            // Log activity
            AuthMiddleware::logActivity('booking_rejected', "Booking #$bookingId rejected by manager: $reason");
            
            Session::setFlash('success', 'Booking rejected successfully!');
            header('Location: /manager/bookings');
            exit;

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to reject booking: ' . $e->getMessage());
            $this->redirectToBookings();
            return;
        }
    }

    /**
     * Cancel booking
     */
    public function cancelBooking($bookingId)
    {
        AuthMiddleware::requireManager();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToBookings();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToBookings();
            return;
        }

        $reason = trim($_POST['reason'] ?? '');

        try {
            $booking = $this->bookingModel->find($bookingId);
            
            if (!$booking) {
                Session::setFlash('error', 'Booking not found.');
                $this->redirectToBookings();
                return;
            }

            if (!in_array($booking['status'], ['pending', 'approved'])) {
                Session::setFlash('error', 'This booking cannot be cancelled.');
                $this->redirectToBookings();
                return;
            }

            $user = Session::getUser();
            
            // Cancel booking
            $this->bookingModel->cancel($bookingId, $reason);
            
            // Make slot available again
            $this->slotModel->markAsAvailable($booking['slot_id']);
            
            // Get booking details for notification
            $bookingDetails = $this->bookingModel->findWithDetails($bookingId);
            
            // Send cancellation notification
            $this->notificationService->sendBookingRejection($bookingDetails, $reason ?: 'Booking cancelled by station manager');
            
            // Log activity
            AuthMiddleware::logActivity('booking_cancelled', "Booking #$bookingId cancelled by manager: $reason");
            
            Session::setFlash('success', 'Booking cancelled successfully!');
            header('Location: /manager/bookings');
            exit;

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to cancel booking: ' . $e->getMessage());
            $this->redirectToBookings();
            return;
        }
    }

    /**
     * Bulk approve bookings
     */
    public function bulkApprove()
    {
        AuthMiddleware::requireManager();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToBookings();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToBookings();
            return;
        }

        $bookingIds = $_POST['booking_ids'] ?? [];
        
        if (empty($bookingIds)) {
            Session::setFlash('error', 'No bookings selected.');
            $this->redirectToBookings();
            return;
        }

        $user = Session::getUser();
        $approvedCount = 0;
        $errors = [];

        foreach ($bookingIds as $bookingId) {
            try {
                $booking = $this->bookingModel->find($bookingId);
                
                if ($booking && $booking['status'] === 'pending') {
                    $this->bookingModel->approve($bookingId, $user['id']);
                    
                    // Get booking details for notification
                    $bookingDetails = $this->bookingModel->findWithDetails($bookingId);
                    $this->notificationService->sendBookingApproval($bookingDetails);
                    
                    $approvedCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to approve booking #$bookingId: " . $e->getMessage();
            }
        }

        if ($approvedCount > 0) {
            Session::setFlash('success', "$approvedCount booking(s) approved successfully!");
        }
        
        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
        }

        header('Location: /manager/bookings');
        exit;
    }

    /**
     * Bulk reject bookings
     */
    public function bulkReject()
    {
        AuthMiddleware::requireManager();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToBookings();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToBookings();
            return;
        }

        $bookingIds = $_POST['booking_ids'] ?? [];
        $reason = trim($_POST['reason'] ?? '');
        
        if (empty($bookingIds)) {
            Session::setFlash('error', 'No bookings selected.');
            $this->redirectToBookings();
            return;
        }

        $user = Session::getUser();
        $rejectedCount = 0;
        $errors = [];

        foreach ($bookingIds as $bookingId) {
            try {
                $booking = $this->bookingModel->find($bookingId);
                
                if ($booking && $booking['status'] === 'pending') {
                    $this->bookingModel->reject($bookingId, $user['id'], $reason);
                    
                    // Make slot available again
                    $this->slotModel->markAsAvailable($booking['slot_id']);
                    
                    // Get booking details for notification
                    $bookingDetails = $this->bookingModel->findWithDetails($bookingId);
                    $this->notificationService->sendBookingRejection($bookingDetails, $reason);
                    
                    $rejectedCount++;
                }
            } catch (\Exception $e) {
                $errors[] = "Failed to reject booking #$bookingId: " . $e->getMessage();
            }
        }

        if ($rejectedCount > 0) {
            Session::setFlash('success', "$rejectedCount booking(s) rejected successfully!");
        }
        
        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
        }

        header('Location: /manager/bookings');
        exit;
    }

    /**
     * Get bookings data for AJAX requests
     */
    public function getBookingsData()
    {
        AuthMiddleware::requireManager();
        
        header('Content-Type: application/json');
        
        try {
            $status = $_GET['status'] ?? 'all';
            $limit = (int)($_GET['limit'] ?? 50);
            
            if ($status === 'all') {
                $bookings = $this->bookingModel->findAllWithDetails($limit);
            } else {
                $bookings = $this->bookingModel->findByStatus($status);
            }
            
            echo json_encode($bookings);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch bookings data']);
        }
    }

    /**
     * Redirect to bookings management page
     */
    private function redirectToBookings()
    {
        header('Location: /manager/bookings');
        exit;
    }
}

