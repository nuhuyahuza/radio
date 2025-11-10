<?php

namespace App\Controllers;

use App\Models\Booking;
use App\Models\Slot;
use App\Models\User;
use App\Utils\Session;
use App\Utils\NotificationService;
use App\Middleware\AuthMiddleware;
use App\Database\Database;

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
     * Get booking details for Admin (JSON for modal)
     */
    public function getAdminBookingDetails($bookingId)
    {
        AuthMiddleware::requireRole('admin');
        header('Content-Type: application/json');

        try {
            $booking = $this->bookingModel->findWithDetails($bookingId);

            if (!$booking) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Booking not found'
                ]);
                return;
            }

            // Build small HTML snippet for the modal body
            $html = '';
            $html .= '<div class="row g-3">';
            $html .= '  <div class="col-md-6">';
            $html .= '    <div class="card h-100">';
            $html .= '      <div class="card-body">';
            $html .= '        <h6 class="text-muted mb-2">Advertiser</h6>';
            $html .= '        <div class="fw-semibold">' . htmlspecialchars($booking['advertiser_name'] ?? '') . '</div>';
            $html .= '        <div class="text-muted small">' . htmlspecialchars($booking['advertiser_email'] ?? '') . '</div>';
            $html .= '        <div class="text-muted small">' . htmlspecialchars($booking['advertiser_phone'] ?? '') . '</div>';
            $html .= '        <div class="text-muted small">' . htmlspecialchars($booking['advertiser_company'] ?? '') . '</div>';
            $html .= '      </div>';
            $html .= '    </div>';
            $html .= '  </div>';
            $html .= '  <div class="col-md-6">';
            $html .= '    <div class="card h-100">';
            $html .= '      <div class="card-body">';
            $html .= '        <h6 class="text-muted mb-2">Slot</h6>';
            $html .= '        <div class="fw-semibold">' . htmlspecialchars($booking['station_name'] ?? '') . '</div>';
            $html .= '        <div class="text-muted small">' . htmlspecialchars($booking['date'] ?? '') . ' ' . htmlspecialchars($booking['start_time'] ?? '') . ' - ' . htmlspecialchars($booking['end_time'] ?? '') . '</div>';
            $html .= '        <div class="text-muted small">Price: ' . htmlspecialchars((string)($booking['price'] ?? '')) . '</div>';
            $html .= '        <div class="text-muted small">Status: <span class="badge bg-' . ($booking['status'] === 'approved' ? 'success' : ($booking['status'] === 'rejected' ? 'danger' : 'warning')) . '">' . htmlspecialchars($booking['status']) . '</span></div>';
            $html .= '      </div>';
            $html .= '    </div>';
            $html .= '  </div>';
            $html .= '</div>';

            echo json_encode([
                'success' => true,
                'booking' => $booking,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to load booking details: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Show bookings management page (for admin)
     */
    public function showBookings()
    {
        AuthMiddleware::requireRole('admin');
        
        $currentUser = Session::getUser();
        
        // Include admin bookings view
        include __DIR__ . '/../../public/views/admin/bookings.php';
    }

    /**
     * Show bookings management page (for manager)
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
     * Get bookings data for AJAX requests (Admin)
     */
    public function getBookingsData()
    {
        AuthMiddleware::requireRole('admin');
        
        header('Content-Type: application/json');
        
        try {
            $page = (int)($_GET['page'] ?? 1);
            $limit = 20;
            $offset = ($page - 1) * $limit;
            
            // Get filters
            $status = $_GET['status'] ?? null;
            $adType = $_GET['ad_type'] ?? null;
            
            // Build query with filters
            $whereClauses = [];
            $params = [];
            
            if ($status && $status !== 'all') {
                $whereClauses[] = "b.status = ?";
                $params[] = $status;
            }
            
            if ($adType && $adType !== 'all') {
                if ($adType === 'traditional') {
                    $whereClauses[] = "b.slot_id IS NOT NULL";
                } else {
                    $whereClauses[] = "b.ad_type = ?";
                    $params[] = $adType;
                }
            }
            
            $whereSql = !empty($whereClauses) ? "WHERE " . implode(" AND ", $whereClauses) : "";
            
            $db = Database::getInstance();
            
            // Get total count
            $countSql = "
                SELECT COUNT(*) as total
                FROM bookings b
                {$whereSql}
            ";
            $totalResult = $db->fetch($countSql, $params);
            $total = $totalResult['total'] ?? 0;
            
            // Get bookings
            $sql = "
                SELECT 
                    b.*,
                    s.date,
                    s.start_time,
                    s.end_time,
                    s.price,
                    COALESCE(st.name, (SELECT name FROM stations LIMIT 1), 'Zaa Radio') as station_name,
                    u.name as advertiser_name,
                    u.email as advertiser_email,
                    u.company as advertiser_company,
                    approver.name as approved_by_name,
                    (SELECT COUNT(*) FROM booking_sessions WHERE booking_id = b.id) as session_count,
                    CASE 
                        WHEN b.ad_type IS NOT NULL THEN b.ad_type
                        ELSE 'traditional'
                    END as booking_type
                FROM bookings b
                LEFT JOIN slots s ON b.slot_id = s.id
                LEFT JOIN stations st ON s.station_id = st.id
                JOIN users u ON b.advertiser_id = u.id
                LEFT JOIN users approver ON b.approved_by = approver.id
                {$whereSql}
                ORDER BY b.created_at DESC
                LIMIT ? OFFSET ?
            ";
            
            $params[] = $limit;
            $params[] = $offset;
            
            $bookings = $db->fetchAll($sql, $params);
            
            // Format bookings for frontend
            $formattedBookings = [];
            foreach ($bookings as $booking) {
                // For campaign bookings, show campaign period and session count
                $isCampaign = !empty($booking['ad_type']);
                $sessionCount = (int)($booking['session_count'] ?? ($booking['slot_id'] ? 1 : 0));
                
                $formattedBookings[] = [
                    'id' => $booking['id'],
                    'advertiser_name' => $booking['advertiser_name'],
                    'advertiser_email' => $booking['advertiser_email'],
                    'date' => $isCampaign ? ($booking['campaign_start'] ?? 'N/A') : ($booking['date'] ?? 'N/A'),
                    'end_date' => $isCampaign ? ($booking['campaign_end'] ?? null) : null,
                    'start_time' => $isCampaign ? 'Multiple' : ($booking['start_time'] ?? 'N/A'),
                    'end_time' => $isCampaign ? 'Sessions' : ($booking['end_time'] ?? 'N/A'),
                    'duration' => $isCampaign ? $sessionCount . ' sessions' : ($booking['duration'] ?? 'N/A'),
                    'total_amount' => (float)($booking['total_amount'] ?? 0),
                    'status' => $booking['status'],
                    'ad_type' => $booking['ad_type'] ?? null,
                    'booking_type' => $booking['booking_type'],
                    'session_count' => $sessionCount,
                    'station_name' => $booking['station_name'] ?? 'Zaa Radio',
                    'created_at' => $booking['created_at']
                ];
            }
            
            echo json_encode([
                'success' => true,
                'bookings' => $formattedBookings,
                'pagination' => [
                    'current_page' => $page,
                    'per_page' => $limit,
                    'total' => $total,
                    'total_pages' => ceil($total / $limit)
                ]
            ]);
            
        } catch (\Exception $e) {
            error_log("Get bookings data error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'message' => 'Failed to fetch bookings data: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update booking status (AJAX, admin)
     */
    public function updateBookingStatus($bookingId)
    {
        AuthMiddleware::requireRole('admin');
        header('Content-Type: application/json');
        
        try {
            // Validate CSRF token from header
            $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
            if (!Session::verifyCsrfToken($csrfToken)) {
                echo json_encode(['success' => false, 'message' => 'Invalid security token']);
                return;
            }
            
            $input = json_decode(file_get_contents('php://input'), true);
            $status = $input['status'] ?? null;
            
            if (!in_array($status, ['approved', 'rejected', 'cancelled'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                return;
            }
            
            $booking = $this->bookingModel->find($bookingId);
            if (!$booking) {
                echo json_encode(['success' => false, 'message' => 'Booking not found']);
                return;
            }
            
            $user = Session::getUser();
            
            if ($status === 'approved') {
                $this->bookingModel->approve($bookingId, $user['id']);
                $bookingDetails = $this->bookingModel->findWithDetails($bookingId);
                
                // Use campaign booking confirmation for campaign bookings
                if (!empty($bookingDetails['ad_type'])) {
                    $bookingDetails['session_count'] = $bookingDetails['session_count'] ?? 0;
                    $this->notificationService->sendCampaignBookingConfirmation($bookingDetails);
                } else {
                    $this->notificationService->sendBookingApproval($bookingDetails);
                }
            } elseif ($status === 'rejected') {
                $reason = $input['reason'] ?? null;
                $this->bookingModel->reject($bookingId, $user['id'], $reason);
                $bookingDetails = $this->bookingModel->findWithDetails($bookingId);
                $this->notificationService->sendBookingRejection($bookingDetails, $reason);
            } else {
                // cancelled
                $this->bookingModel->update($bookingId, ['status' => 'cancelled']);
            }
            
            echo json_encode([
                'success' => true, 
                'message' => 'Booking status updated successfully',
                'status' => $status
            ]);
            
        } catch (\Exception $e) {
            error_log("Update booking status error: " . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false, 
                'message' => 'Failed to update booking status: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Get sample bookings data for demo
     */
    private function getSampleBookings($page, $limit, $filters = [])
    {
        $allBookings = [
            [
                'id' => 1,
                'advertiser_name' => 'Tech Solutions Inc',
                'advertiser_email' => 'contact@techsolutions.com',
                'date' => '2024-09-20',
                'start_time' => '09:00:00',
                'end_time' => '09:30:00',
                'duration' => 30,
                'total_amount' => 150.00,
                'status' => 'pending',
                'created_at' => '2024-09-19 14:30:00'
            ],
            [
                'id' => 2,
                'advertiser_name' => 'Marketing Pro',
                'advertiser_email' => 'info@marketingpro.com',
                'date' => '2024-09-20',
                'start_time' => '10:00:00',
                'end_time' => '11:00:00',
                'duration' => 60,
                'total_amount' => 300.00,
                'status' => 'approved',
                'created_at' => '2024-09-19 10:15:00'
            ],
            [
                'id' => 3,
                'advertiser_name' => 'Local Restaurant',
                'advertiser_email' => 'owner@localrestaurant.com',
                'date' => '2024-09-21',
                'start_time' => '12:00:00',
                'end_time' => '12:15:00',
                'duration' => 15,
                'total_amount' => 75.00,
                'status' => 'rejected',
                'created_at' => '2024-09-19 08:45:00'
            ],
            [
                'id' => 4,
                'advertiser_name' => 'Fitness Center',
                'advertiser_email' => 'admin@fitnesscenter.com',
                'date' => '2024-09-21',
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
                'duration' => 60,
                'total_amount' => 250.00,
                'status' => 'approved',
                'created_at' => '2024-09-18 16:20:00'
            ],
            [
                'id' => 5,
                'advertiser_name' => 'Real Estate Agency',
                'advertiser_email' => 'sales@realestate.com',
                'date' => '2024-09-22',
                'start_time' => '16:00:00',
                'end_time' => '16:30:00',
                'duration' => 30,
                'total_amount' => 200.00,
                'status' => 'pending',
                'created_at' => '2024-09-19 12:10:00'
            ]
        ];
        
        // Apply filters
        $filteredBookings = $allBookings;
        
        if (isset($filters['status']) && $filters['status'] !== '') {
            $filteredBookings = array_filter($filteredBookings, function($booking) use ($filters) {
                return $booking['status'] === $filters['status'];
            });
        }
        
        if (isset($filters['advertiser']) && $filters['advertiser'] !== '') {
            $filteredBookings = array_filter($filteredBookings, function($booking) use ($filters) {
                return stripos($booking['advertiser_name'], $filters['advertiser']) !== false ||
                       stripos($booking['advertiser_email'], $filters['advertiser']) !== false;
            });
        }
        
        // Pagination
        $total = count($filteredBookings);
        $totalPages = ceil($total / $limit);
        $offset = ($page - 1) * $limit;
        $bookings = array_slice($filteredBookings, $offset, $limit);
        
        return [
            'bookings' => array_values($bookings),
            'pagination' => [
                'current_page' => $page,
                'total_pages' => $totalPages,
                'total' => $total,
                'showing' => count($bookings),
                'per_page' => $limit
            ]
        ];
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

