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
        AuthMiddleware::requireManager();
        
        header('Content-Type: application/json');
        
        try {
            $stationId = 1; // For now, we'll use station ID 1
            $startDate = $_GET['start'] ?? date('Y-m-d');
            $endDate = $_GET['end'] ?? date('Y-m-d', strtotime('+30 days'));
            
            $slots = $this->slotModel->findByDateRange($startDate, $endDate, $stationId);
            
            // Transform for calendar display
            $calendarSlots = [];
            foreach ($slots as $slot) {
                $calendarSlots[] = [
                    'id' => $slot['id'],
                    'title' => date('g:i A', strtotime($slot['start_time'])) . ' - ' . date('g:i A', strtotime($slot['end_time'])),
                    'start' => $slot['date'] . 'T' . $slot['start_time'],
                    'end' => $slot['date'] . 'T' . $slot['end_time'],
                    'status' => $slot['status'],
                    'price' => floatval($slot['price']),
                    'description' => $slot['description'],
                    'color' => $this->getSlotColor($slot['status'])
                ];
            }
            
            echo json_encode($calendarSlots);
            
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode(['error' => 'Failed to fetch slots data']);
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
        header('Location: /manager/slots');
        exit;
    }
}
