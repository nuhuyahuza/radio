<?php

namespace App\Controllers;

use App\Models\Slot;
use App\Models\Booking;
use App\Utils\Session;
use App\Middleware\AuthMiddleware;

/**
 * Slot Controller
 * Handles slot management operations for station managers
 */
class SlotController
{
    private $slotModel;
    private $bookingModel;

    public function __construct()
    {
        $this->slotModel = new Slot();
        $this->bookingModel = new Booking();
    }

    /**
     * Admin/Manager JSON: Get single slot details
     */
    public function getSlotDetailsJson($slotId)
    {
        $this->requireAdminOrManagerJson();
        header('Content-Type: application/json');
        $slot = $this->slotModel->find($slotId);
        if (!$slot) {
            echo json_encode(['success' => false, 'message' => 'Slot not found']);
            return;
        }
        // Build details HTML for modal
        $startTs = strtotime(($slot['date'] ?? '') . ' ' . ($slot['start_time'] ?? ''));
        $endTs = strtotime(($slot['date'] ?? '') . ' ' . ($slot['end_time'] ?? ''));
        $durationMinutes = $startTs && $endTs ? max(0, (int)(($endTs - $startTs) / 60)) : 0;
        $statusBadgeClass = 'info';
        $status = $slot['status'] ?? '';
        if ($status === 'available') { $statusBadgeClass = 'success'; }
        elseif ($status === 'blocked') { $statusBadgeClass = 'warning'; }
        elseif ($status === 'booked') { $statusBadgeClass = 'primary'; }

        $html = '';
        $html .= '<div class="row g-3">';
        $html .= '  <div class="col-md-6">';
        $html .= '    <div class="card h-100">';
        $html .= '      <div class="card-body">';
        $html .= '        <h6 class="text-muted mb-2">Schedule</h6>';
        $html .= '        <div class="fw-semibold">' . htmlspecialchars($slot['date'] ?? '') . '</div>';
        $html .= '        <div class="text-muted small">' . htmlspecialchars($slot['start_time'] ?? '') . ' - ' . htmlspecialchars($slot['end_time'] ?? '') . ' (' . $durationMinutes . ' min)</div>';
        $html .= '      </div>';
        $html .= '    </div>';
        $html .= '  </div>';
        $html .= '  <div class="col-md-6">';
        $html .= '    <div class="card h-100">';
        $html .= '      <div class="card-body">';
        $html .= '        <h6 class="text-muted mb-2">Details</h6>';
        $html .= '        <div>Price: <span class="fw-semibold">GH₵' . htmlspecialchars((string)($slot['price'] ?? '')) . '</span></div>';
        $html .= '        <div>Status: <span class="badge bg-' . $statusBadgeClass . '">' . htmlspecialchars($status) . '</span></div>';
        $html .= '      </div>';
        $html .= '    </div>';
        $html .= '  </div>';
        $html .= '  <div class="col-12">';
        $html .= '    <div class="card">';
        $html .= '      <div class="card-body">';
        $html .= '        <h6 class="text-muted mb-2">Description</h6>';
        $html .= '        <div>' . nl2br(htmlspecialchars($slot['description'] ?? '—')) . '</div>';
        $html .= '      </div>';
        $html .= '    </div>';
        $html .= '  </div>';
        $html .= '</div>';

        echo json_encode(['success' => true, 'slot' => $slot, 'html' => $html]);
    }

    /**
     * Admin/Manager JSON: Create slot
     */
    public function createSlotJson()
    {
        $this->requireAdminOrManagerJson();
        header('Content-Type: application/json');
        if (!$this->verifyJsonCsrf()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $date = trim($input['date'] ?? '');
        $startTime = trim($input['start_time'] ?? '');
        $endTime = trim($input['end_time'] ?? '');
        $price = (float)($input['price'] ?? 0);
        $status = $input['status'] ?? 'available';
        $description = trim($input['description'] ?? '');
        $errors = $this->validateSlotInput($date, $startTime, $endTime, $price, $status);
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
            return;
        }
        try {
            $stationId = 1;
            if ($this->slotModel->hasTimeConflict($stationId, $date, $startTime, $endTime)) {
                echo json_encode(['success' => false, 'message' => 'This time slot conflicts with an existing slot.']);
                return;
            }
            $user = Session::getUser();
            $slotId = $this->slotModel->create([
                'station_id' => $stationId,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price' => $price,
                'status' => $status,
                'description' => $description,
                'created_by' => $user['id'] ?? null
            ]);
            \App\Middleware\AuthMiddleware::logActivity('slot_created', "Slot #$slotId created via admin JSON");
            echo json_encode(['success' => true, 'id' => $slotId]);
        } catch (\Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Failed to create slot']);
        }
    }

    /**
     * Admin/Manager JSON: Update slot
     */
    public function updateSlotJson($slotId)
    {
        $this->requireAdminOrManagerJson();
        header('Content-Type: application/json');
        if (!$this->verifyJsonCsrf()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $date = trim($input['date'] ?? '');
        $startTime = trim($input['start_time'] ?? '');
        $endTime = trim($input['end_time'] ?? '');
        $price = (float)($input['price'] ?? 0);
        $status = $input['status'] ?? 'available';
        $description = trim($input['description'] ?? '');
        $errors = $this->validateSlotInput($date, $startTime, $endTime, $price, $status);
        if (!empty($errors)) {
            echo json_encode(['success' => false, 'message' => implode(' ', $errors)]);
            return;
        }
        $slot = $this->slotModel->find($slotId);
        if (!$slot) {
            echo json_encode(['success' => false, 'message' => 'Slot not found']);
            return;
        }
        // If switching to available ensure no booking conflict
        if (($slot['status'] === 'booked') && $status === 'available' && $this->bookingModel->isSlotBooked($slotId)) {
            echo json_encode(['success' => false, 'message' => 'Cannot make a booked slot available. Cancel booking first.']);
            return;
        }
        $stationId = 1;
        if ($this->slotModel->hasTimeConflict($stationId, $date, $startTime, $endTime, $slotId)) {
            echo json_encode(['success' => false, 'message' => 'This time slot conflicts with an existing slot.']);
            return;
        }
        $this->slotModel->update($slotId, [
            'date' => $date,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'price' => $price,
            'status' => $status,
            'description' => $description
        ]);
        \App\Middleware\AuthMiddleware::logActivity('slot_updated', "Slot #$slotId updated via admin JSON");
        echo json_encode(['success' => true]);
    }

    /**
     * Admin/Manager JSON: Delete slot
     */
    public function deleteSlotJson($slotId)
    {
        $this->requireAdminOrManagerJson();
        header('Content-Type: application/json');
        if (!$this->verifyJsonCsrf()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }
        $slot = $this->slotModel->find($slotId);
        if (!$slot) {
            echo json_encode(['success' => false, 'message' => 'Slot not found']);
            return;
        }
        if ($this->bookingModel->isSlotBooked($slotId)) {
            echo json_encode(['success' => false, 'message' => 'Cannot delete a booked slot.']);
            return;
        }
        $this->slotModel->delete($slotId);
        \App\Middleware\AuthMiddleware::logActivity('slot_deleted', "Slot #$slotId deleted via admin JSON");
        echo json_encode(['success' => true]);
    }

    /**
     * Admin/Manager JSON: Update slot status
     */
    public function updateSlotStatusJson($slotId)
    {
        $this->requireAdminOrManagerJson();
        header('Content-Type: application/json');
        if (!$this->verifyJsonCsrf()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $status = $input['status'] ?? '';
        if (!in_array($status, ['available', 'blocked', 'booked', 'cancelled'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid status']);
            return;
        }
        $slot = $this->slotModel->find($slotId);
        if (!$slot) {
            echo json_encode(['success' => false, 'message' => 'Slot not found']);
            return;
        }
        if ($status === 'available' && $this->bookingModel->isSlotBooked($slotId)) {
            echo json_encode(['success' => false, 'message' => 'Cannot make booked slot available. Cancel booking first.']);
            return;
        }
        $this->slotModel->update($slotId, ['status' => $status]);
        echo json_encode(['success' => true]);
    }

    /**
     * Admin/Manager JSON: Generate slots in bulk
     */
    public function generateSlotsJson()
    {
        $this->requireAdminOrManagerJson();
        header('Content-Type: application/json');
        if (!$this->verifyJsonCsrf()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $startDate = trim($input['start_date'] ?? '');
        $endDate = trim($input['end_date'] ?? '');
        $startTime = trim($input['start_time'] ?? '');
        $endTime = trim($input['end_time'] ?? '');
        $duration = (int)($input['duration'] ?? 30);
        $price = (float)($input['price'] ?? 0);
        $weekdaysOnly = !empty($input['weekdays_only']);
        if (!$startDate || !$endDate || !$startTime || !$endTime || $duration <= 0) {
            echo json_encode(['success' => false, 'message' => 'All fields are required for generation']);
            return;
        }
        $stationId = 1;
        $count = 0;
        $current = strtotime($startDate);
        $end = strtotime($endDate);
        while ($current <= $end) {
            $date = date('Y-m-d', $current);
            $dayOfWeek = date('N', $current); // 1..7
            if ($weekdaysOnly && ($dayOfWeek == 6 || $dayOfWeek == 7)) {
                $current = strtotime('+1 day', $current);
                continue;
            }
            $slotStart = strtotime($date . ' ' . $startTime);
            $slotEndLimit = strtotime($date . ' ' . $endTime);
            while ($slotStart < $slotEndLimit) {
                $slotEnd = $slotStart + ($duration * 60);
                if ($slotEnd > $slotEndLimit) {
                    break;
                }
                $sStart = date('H:i:s', $slotStart);
                $sEnd = date('H:i:s', $slotEnd);
                if (!$this->slotModel->hasTimeConflict($stationId, $date, $sStart, $sEnd)) {
                    $this->slotModel->create([
                        'station_id' => $stationId,
                        'date' => $date,
                        'start_time' => $sStart,
                        'end_time' => $sEnd,
                        'price' => $price,
                        'status' => 'available',
                        'description' => ''
                    ]);
                    $count++;
                }
                $slotStart = $slotEnd;
            }
            $current = strtotime('+1 day', $current);
        }
        echo json_encode(['success' => true, 'count' => $count]);
    }

    /**
     * Admin/Manager JSON: Bulk action on slots
     */
    public function bulkActionJson()
    {
        $this->requireAdminOrManagerJson();
        header('Content-Type: application/json');
        if (!$this->verifyJsonCsrf()) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token']);
            return;
        }
        $input = json_decode(file_get_contents('php://input'), true) ?? [];
        $action = $input['action'] ?? '';
        $ids = $input['slot_ids'] ?? [];
        if (empty($ids) || !in_array($action, ['block', 'unblock', 'delete'])) {
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
            return;
        }
        $updated = 0; $deleted = 0;
        foreach ($ids as $id) {
            $slot = $this->slotModel->find($id);
            if (!$slot) { continue; }
            if ($action === 'delete') {
                if ($this->bookingModel->isSlotBooked($id)) { continue; }
                $this->slotModel->delete($id);
                $deleted++;
            } elseif ($action === 'block') {
                $this->slotModel->update($id, ['status' => 'blocked']);
                $updated++;
            } elseif ($action === 'unblock') {
                if ($this->bookingModel->isSlotBooked($id)) { continue; }
                $this->slotModel->update($id, ['status' => 'available']);
                $updated++;
            }
        }
        echo json_encode(['success' => true, 'updated' => $updated, 'deleted' => $deleted]);
    }

    /**
     * Helper: validate JSON CSRF from header
     */
    private function verifyJsonCsrf(): bool
    {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        return Session::verifyCsrfToken($token);
    }

    /**
     * Helper: ensure admin or station_manager for JSON endpoints
     */
    private function requireAdminOrManagerJson(): void
    {
        $currentUser = Session::getUser();
        $role = $currentUser['role'] ?? '';
        if ($role !== 'admin' && $role !== 'station_manager') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            exit;
        }
    }

    /**
     * Helper: validate slot input
     */
    private function validateSlotInput($date, $startTime, $endTime, $price, $status): array
    {
        $errors = [];
        if (empty($date) || !strtotime($date)) { $errors[] = 'Valid date is required.'; }
        if (empty($startTime) || !strtotime($startTime)) { $errors[] = 'Valid start time is required.'; }
        if (empty($endTime) || !strtotime($endTime)) { $errors[] = 'Valid end time is required.'; }
        if (strtotime($startTime) >= strtotime($endTime)) { $errors[] = 'End time must be after start time.'; }
        if ($price < 0) { $errors[] = 'Price must be a positive number.'; }
        if (!in_array($status, ['available', 'blocked', 'booked', 'cancelled'])) { $errors[] = 'Invalid status.'; }
        return $errors;
    }

    /**
     * Show slots management page
     */
    public function showSlotsManagement()
    {
        AuthMiddleware::requireManager();
        
        $user = Session::getUser();
        $stationId = 1; // For now, we'll use station ID 1
        
        // Get slots for the next 30 days
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        $slots = $this->slotModel->findByDateRange($startDate, $endDate, $stationId);
        
        // Get slot statistics
        $stats = $this->slotModel->getStats($stationId);
        
        include __DIR__ . '/../../public/views/manager/slots.php';
    }

    /**
     * Show slots for admin
     */
    public function showSlots()
    {
        AuthMiddleware::requireRole('admin');
        
        $user = Session::getUser();
        $stationId = 1; // For now, we'll use station ID 1
        
        // Get slots for the next 30 days
        $startDate = date('Y-m-d');
        $endDate = date('Y-m-d', strtotime('+30 days'));
        $slots = $this->slotModel->findByDateRange($startDate, $endDate, $stationId);
        
        // Get slot statistics
        $stats = $this->slotModel->getStats($stationId);
        
        include __DIR__ . '/../../public/views/admin/slots.php';
    }

    /**
     * Show create slot form
     */
    public function showCreateSlot()
    {
        AuthMiddleware::requireManager();
        
        $csrfToken = Session::getCsrfToken();
        include __DIR__ . '/../../public/views/manager/slot-form.php';
    }

    /**
     * Show edit slot form
     */
    public function showEditSlot($slotId)
    {
        AuthMiddleware::requireManager();
        
        $slot = $this->slotModel->find($slotId);
        
        if (!$slot) {
            Session::setFlash('error', 'Slot not found.');
            header('Location: /manager/slots');
            exit;
        }
        
        $csrfToken = Session::getCsrfToken();
        include __DIR__ . '/../../public/views/manager/slot-form.php';
    }

    /**
     * Create new slot
     */
    public function createSlot()
    {
        AuthMiddleware::requireManager();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToSlots();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToSlots();
            return;
        }

        $date = $_POST['date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');

        // Validate input
        $errors = [];

        if (empty($date) || !strtotime($date)) {
            $errors[] = 'Valid date is required.';
        }

        if (empty($startTime) || !strtotime($startTime)) {
            $errors[] = 'Valid start time is required.';
        }

        if (empty($endTime) || !strtotime($endTime)) {
            $errors[] = 'Valid end time is required.';
        }

        if (strtotime($startTime) >= strtotime($endTime)) {
            $errors[] = 'End time must be after start time.';
        }

        if ($price < 0) {
            $errors[] = 'Price must be a positive number.';
        }

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            $this->redirectToSlots();
            return;
        }

        try {
            $user = Session::getUser();
            $stationId = 1; // For now, we'll use station ID 1

            // Check for time conflicts
            if ($this->slotModel->hasTimeConflict($stationId, $date, $startTime, $endTime)) {
                Session::setFlash('error', 'This time slot conflicts with an existing slot.');
                $this->redirectToSlots();
                return;
            }

            // Create slot
            $slotData = [
                'station_id' => $stationId,
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price' => $price,
                'status' => 'available',
                'description' => $description,
                'created_by' => $user['id']
            ];

            $slotId = $this->slotModel->create($slotData);

            // Log activity
            AuthMiddleware::logActivity('slot_created', "New slot created: #$slotId for $date $startTime-$endTime");

            Session::setFlash('success', 'Slot created successfully!');
            header('Location: /manager/slots');
            exit;

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to create slot: ' . $e->getMessage());
            $this->redirectToSlots();
            return;
        }
    }

    /**
     * Update existing slot
     */
    public function updateSlot($slotId)
    {
        AuthMiddleware::requireManager();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToSlots();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToSlots();
            return;
        }

        $date = $_POST['date'] ?? '';
        $startTime = $_POST['start_time'] ?? '';
        $endTime = $_POST['end_time'] ?? '';
        $price = floatval($_POST['price'] ?? 0);
        $description = trim($_POST['description'] ?? '');
        $status = $_POST['status'] ?? 'available';

        // Validate input
        $errors = [];

        if (empty($date) || !strtotime($date)) {
            $errors[] = 'Valid date is required.';
        }

        if (empty($startTime) || !strtotime($startTime)) {
            $errors[] = 'Valid start time is required.';
        }

        if (empty($endTime) || !strtotime($endTime)) {
            $errors[] = 'Valid end time is required.';
        }

        if (strtotime($startTime) >= strtotime($endTime)) {
            $errors[] = 'End time must be after start time.';
        }

        if ($price < 0) {
            $errors[] = 'Price must be a positive number.';
        }

        if (!in_array($status, ['available', 'booked', 'cancelled', 'maintenance'])) {
            $errors[] = 'Invalid status.';
        }

        if (!empty($errors)) {
            Session::setFlash('error', implode(' ', $errors));
            $this->redirectToSlots();
            return;
        }

        try {
            $user = Session::getUser();
            $stationId = 1; // For now, we'll use station ID 1

            // Check if slot exists
            $slot = $this->slotModel->find($slotId);
            if (!$slot) {
                Session::setFlash('error', 'Slot not found.');
                $this->redirectToSlots();
                return;
            }

            // Check for time conflicts (excluding current slot)
            if ($this->slotModel->hasTimeConflict($stationId, $date, $startTime, $endTime, $slotId)) {
                Session::setFlash('error', 'This time slot conflicts with an existing slot.');
                $this->redirectToSlots();
                return;
            }

            // Check if slot is booked and trying to change to available
            if ($slot['status'] === 'booked' && $status === 'available') {
                if ($this->bookingModel->isSlotBooked($slotId)) {
                    Session::setFlash('error', 'Cannot make a booked slot available. Please cancel the booking first.');
                    $this->redirectToSlots();
                    return;
                }
            }

            // Update slot
            $slotData = [
                'date' => $date,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'price' => $price,
                'status' => $status,
                'description' => $description
            ];

            $this->slotModel->update($slotId, $slotData);

            // Log activity
            AuthMiddleware::logActivity('slot_updated', "Slot updated: #$slotId for $date $startTime-$endTime");

            Session::setFlash('success', 'Slot updated successfully!');
            header('Location: /manager/slots');
            exit;

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to update slot: ' . $e->getMessage());
            $this->redirectToSlots();
            return;
        }
    }

    /**
     * Delete slot
     */
    public function deleteSlot($slotId)
    {
        AuthMiddleware::requireManager();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToSlots();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToSlots();
            return;
        }

        try {
            $slot = $this->slotModel->find($slotId);
            
            if (!$slot) {
                Session::setFlash('error', 'Slot not found.');
                $this->redirectToSlots();
                return;
            }

            // Check if slot is booked
            if ($this->bookingModel->isSlotBooked($slotId)) {
                Session::setFlash('error', 'Cannot delete a booked slot. Please cancel the booking first.');
                $this->redirectToSlots();
                return;
            }

            // Delete slot
            $this->slotModel->delete($slotId);

            // Log activity
            AuthMiddleware::logActivity('slot_deleted', "Slot deleted: #$slotId for {$slot['date']} {$slot['start_time']}-{$slot['end_time']}");

            Session::setFlash('success', 'Slot deleted successfully!');
            header('Location: /manager/slots');
            exit;

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to delete slot: ' . $e->getMessage());
            $this->redirectToSlots();
            return;
        }
    }

    /**
     * Cancel slot
     */
    public function cancelSlot($slotId)
    {
        AuthMiddleware::requireManager();
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirectToSlots();
            return;
        }

        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Session::verifyCsrfToken($csrfToken)) {
            Session::setFlash('error', 'Invalid security token. Please try again.');
            $this->redirectToSlots();
            return;
        }

        try {
            $slot = $this->slotModel->find($slotId);
            
            if (!$slot) {
                Session::setFlash('error', 'Slot not found.');
                $this->redirectToSlots();
                return;
            }

            // Cancel slot
            $this->slotModel->cancelSlot($slotId);

            // If there's a booking, reject it
            $bookings = $this->bookingModel->findBySlot($slotId);
            foreach ($bookings as $booking) {
                if ($booking['status'] === 'pending' || $booking['status'] === 'approved') {
                    $this->bookingModel->reject($booking['id'], Session::getUser()['id'], 'Slot cancelled by station manager');
                }
            }

            // Log activity
            AuthMiddleware::logActivity('slot_cancelled', "Slot cancelled: #$slotId for {$slot['date']} {$slot['start_time']}-{$slot['end_time']}");

            Session::setFlash('success', 'Slot cancelled successfully!');
            header('Location: /manager/slots');
            exit;

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to cancel slot: ' . $e->getMessage());
            $this->redirectToSlots();
            return;
        }
    }

    /**
     * Get slots data for AJAX requests
     */
    public function getSlotsData()
    {
        // Allow both admin and station_manager
        $currentUser = Session::getUser();
        $role = $currentUser['role'] ?? '';
        if ($role !== 'admin' && $role !== 'station_manager') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            return;
        }

        header('Content-Type: application/json');

        try {
            $stationId = 1; // For now, we'll use station ID 1
            $startDate = $_GET['start'] ?? date('Y-m-d');
            $endDate = $_GET['end'] ?? date('Y-m-d', strtotime('+30 days'));

            $statusFilter = $_GET['status'] ?? null;
            $dateFilter = $_GET['date'] ?? null;

            if (!empty($dateFilter)) {
                $startDate = $dateFilter;
                $endDate = $dateFilter;
            }

            $slots = $this->slotModel->findByDateRange($startDate, $endDate, $stationId);

            // Apply status filter if provided
            if (!empty($statusFilter)) {
                $slots = array_values(array_filter($slots, function ($s) use ($statusFilter) {
                    return strtolower($s['status']) === strtolower($statusFilter);
                }));
            }

            // Build response expected by admin view
            $resultSlots = [];
            foreach ($slots as $slot) {
                $startTs = strtotime($slot['date'] . ' ' . $slot['start_time']);
                $endTs = strtotime($slot['date'] . ' ' . $slot['end_time']);
                $durationMinutes = max(0, (int)(($endTs - $startTs) / 60));

                $resultSlots[] = [
                    'id' => (int)$slot['id'],
                    'date' => $slot['date'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'duration' => $durationMinutes,
                    'price' => (float)$slot['price'],
                    'status' => $slot['status'],
                    'booked_by' => null,
                ];
            }

            $total = count($resultSlots);
            $response = [
                'success' => true,
                'slots' => $resultSlots,
                'pagination' => [
                    'total_pages' => 1,
                    'current_page' => 1,
                    'total' => $total,
                    'showing' => $total,
                ],
            ];

            echo json_encode($response);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch slots data']);
        }
    }

    /**
     * Get calendar slots (admin and manager)
     */
    public function getSlotsCalendarData()
    {
        $currentUser = Session::getUser();
        $role = $currentUser['role'] ?? '';
        if ($role !== 'admin' && $role !== 'station_manager') {
            http_response_code(403);
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'Forbidden']);
            return;
        }

        header('Content-Type: application/json');
        try {
            $stationId = 1;
            $startDate = $_GET['start_date'] ?? date('Y-m-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-t');
            $slots = $this->slotModel->findByDateRange($startDate, $endDate, $stationId);

            $calendar = [];
            foreach ($slots as $slot) {
                $calendar[] = [
                    'date' => $slot['date'],
                    'start_time' => $slot['start_time'],
                    'end_time' => $slot['end_time'],
                    'status' => $slot['status'],
                    'price' => (float)$slot['price'],
                ];
            }

            echo json_encode(['success' => true, 'slots' => $calendar]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'Failed to fetch calendar slots']);
        }
    }

    /**
     * Get slot color based on status
     */
    private function getSlotColor($status)
    {
        switch ($status) {
            case 'available':
                return '#28a745';
            case 'booked':
                return '#dc3545';
            case 'cancelled':
                return '#6c757d';
            case 'maintenance':
                return '#ffc107';
            default:
                return '#007bff';
        }
    }

    /**
     * Redirect to slots management page
     */
    private function redirectToSlots()
    {
        // Unified slots path
        header('Location: /slots');
        exit;
    }
}