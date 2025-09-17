<?php
/**
 * Slots API - Returns available slots in JSON format
 * Uses actual database queries to fetch slot data
 */

// Load Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Slot;
use App\Models\Booking;

header('Content-Type: application/json');

try {
    $slotModel = new Slot();
    $bookingModel = new Booking();
    
    // Get date range (next 30 days)
    $startDate = date('Y-m-d');
    $endDate = date('Y-m-d', strtotime('+30 days'));
    
    // Fetch slots from database
    $slots = $slotModel->findByDateRange($startDate, $endDate);
    
    // Transform slots for FullCalendar
    $calendarSlots = [];
    
    foreach ($slots as $slot) {
        // Check if slot is booked
        $isBooked = $bookingModel->isSlotBooked($slot['id']);
        $status = $isBooked ? 'booked' : $slot['status'];
        
        // Determine color based on status
        $color = '#28a745'; // Green for available
        if ($status === 'booked') {
            $color = '#dc3545'; // Red for booked
        } elseif ($status === 'cancelled') {
            $color = '#6c757d'; // Gray for cancelled
        } elseif ($status === 'maintenance') {
            $color = '#ffc107'; // Yellow for maintenance
        }
        
        // Create time slot title
        $startTime = date('g:i A', strtotime($slot['start_time']));
        $endTime = date('g:i A', strtotime($slot['end_time']));
        $title = $startTime . ' - ' . $endTime;
        
        // Add price to title if available
        if ($slot['price'] > 0) {
            $title .= ' ($' . number_format($slot['price'], 0) . ')';
        }
        
        $calendarSlots[] = [
            'id' => $slot['id'],
            'title' => $title,
            'start' => $slot['date'] . 'T' . $slot['start_time'],
            'end' => $slot['date'] . 'T' . $slot['end_time'],
            'status' => $status,
            'price' => floatval($slot['price']),
            'color' => $color,
            'description' => $slot['description'] ?? '',
            'extendedProps' => [
                'status' => $status,
                'price' => floatval($slot['price']),
                'slotId' => $slot['id'],
                'date' => $slot['date'],
                'startTime' => $slot['start_time'],
                'endTime' => $slot['end_time']
            ]
        ];
    }
    
    // Return JSON response
    echo json_encode($calendarSlots);
    
} catch (Exception $e) {
    // Log error (in production, use proper logging)
    error_log("Slots API Error: " . $e->getMessage());
    
    // Return error response
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to fetch slots',
        'message' => 'An error occurred while retrieving slot data'
    ]);
}
?>
