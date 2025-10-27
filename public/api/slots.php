<?php
/**
 * Slots API - Returns slots and booking sessions in JSON format for calendar display
 * Uses actual database queries to fetch slot data
 */

// Load Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\Slot;
use App\Models\Booking;
use App\Models\BookingSession;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');

try {
    $slotModel = new Slot();
    $bookingModel = new Booking();
    $sessionModel = new BookingSession();
    
    // Get date range from query params (optional)
    $startDate = $_GET['start'] ?? date('Y-m-01');
    $endDate = $_GET['end'] ?? date('Y-m-t', strtotime('+2 months'));
    
    // Fetch traditional slots
    $slots = $slotModel->findByDateRange($startDate, $endDate);
    
    // Check if booking_sessions table exists before querying
    $sessions = [];
    try {
        // Fetch booking sessions with ad type information
        $sql = "
            SELECT 
                bs.id,
                bs.session_date as date,
                bs.start_time,
                bs.end_time,
                bs.status,
                b.ad_type,
                b.advertiser_id,
                u.name as advertiser_name,
                u.company as company_name
            FROM booking_sessions bs
            JOIN bookings b ON bs.booking_id = b.id
            LEFT JOIN users u ON b.advertiser_id = u.id
            WHERE bs.session_date BETWEEN ? AND ?
            ORDER BY bs.session_date, bs.start_time
        ";
        
        $sessions = $sessionModel->fetchAll($sql, [$startDate, $endDate]);
    } catch (\Exception $e) {
        // Table doesn't exist yet - this is okay before migration
        error_log("Booking sessions table not found: " . $e->getMessage());
        $sessions = [];
    }
    
    $events = [];
    
    // Convert slots to FullCalendar format
    foreach ($slots as $slot) {
        $status = $slot['status'];
        $title = '';
        $color = '';
        
        if ($status === 'available') {
            $title = 'Available - GH₵' . number_format($slot['price'], 2);
            $color = '#28a745'; // Green
        } elseif ($status === 'booked') {
            $title = 'Booked';
            $color = '#dc3545'; // Red
        } elseif ($status === 'pending') {
            $title = 'Pending';
            $color = '#ffc107'; // Yellow
        }
        
        $events[] = [
            'id' => 'slot_' . $slot['id'],
            'title' => $title,
            'start' => $slot['date'] . 'T' . $slot['start_time'],
            'end' => $slot['date'] . 'T' . $slot['end_time'],
            'color' => $color,
            'extendedProps' => [
                'type' => 'slot',
                'slotId' => $slot['id'],
                'status' => $status,
                'price' => $slot['price'],
                'description' => $slot['description'] ?? ''
            ]
        ];
    }
    
    // Convert booking sessions to FullCalendar format with ad type colors
    foreach ($sessions as $session) {
        $adType = $session['ad_type'];
        $status = $session['status'];
        
        // Set color based on ad type
        $color = '';
        $typeLabel = '';
        
        switch ($adType) {
            case 'jingle':
                $color = '#28a745'; // Green
                $typeLabel = 'Jingle';
                break;
            case 'lpm':
                $color = '#007bff'; // Blue
                $typeLabel = 'LPM';
                break;
            case 'talkshow':
                $color = '#fd7e14'; // Orange
                $typeLabel = 'Talkshow';
                break;
            default:
                $color = '#6c757d'; // Gray
                $typeLabel = 'Booking';
        }
        
        // Adjust opacity for different statuses
        if ($status === 'pending') {
            $color = $color . 'CC'; // Add transparency
        } elseif ($status === 'cancelled') {
            $color = '#6c757d'; // Gray for cancelled
        }
        
        $advertiserName = $session['company_name'] ?: $session['advertiser_name'];
        $title = $typeLabel . ($advertiserName ? ' - ' . $advertiserName : '');
        
        $events[] = [
            'id' => 'session_' . $session['id'],
            'title' => $title,
            'start' => $session['date'] . 'T' . $session['start_time'],
            'end' => $session['date'] . 'T' . $session['end_time'],
            'color' => $color,
            'extendedProps' => [
                'type' => 'booking_session',
                'sessionId' => $session['id'],
                'adType' => $adType,
                'status' => $status,
                'advertiser' => $advertiserName
            ]
        ];
    }
    
    echo json_encode($events);
    
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => true,
        'message' => 'Failed to fetch slots: ' . $e->getMessage()
    ]);
}
?>