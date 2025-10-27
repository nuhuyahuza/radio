<?php
/**
 * Check Availability API - Returns available slots for given campaign parameters
 */

// Load Composer autoloader
require_once __DIR__ . '/../../vendor/autoload.php';

use App\Models\BookingSession;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

try {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (!$data) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    $adType = $data['ad_type'] ?? '';
    $startDate = $data['start_date'] ?? '';
    $endDate = $data['end_date'] ?? '';
    $startTime = $data['start_time'] ?? '';
    $endTime = $data['end_time'] ?? '';
    $recurrence = $data['recurrence'] ?? 'daily';
    $weekdays = $data['weekdays'] ?? [];
    $jingleTimes = $data['jingle_times'] ?? [];
    $duration = (int)($data['duration'] ?? 30); // duration in seconds

    // Validate required fields
    if (empty($adType) || empty($startDate) || empty($endDate)) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Missing required fields']);
        exit;
    }

    $availableSlots = [];
    $bookedSlots = [];
    $sessionModel = new BookingSession();

    // Generate potential sessions based on ad type
    switch ($adType) {
        case 'jingle':
            // Jingles: Multiple times per day over campaign period
            if (empty($jingleTimes)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Jingle times required']);
                exit;
            }
            
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $interval = new DateInterval('P1D');
            
            for ($d = clone $start; $d <= $end; $d->add($interval)) {
                $dateStr = $d->format('Y-m-d');
                
                foreach ($jingleTimes as $time) {
                    $endTimeCalc = date('H:i:s', strtotime($time) + $duration);
                    
                    // Check if this slot is already booked
                    $isBooked = $sessionModel->exists([
                        'session_date' => $dateStr,
                        'start_time' => $time,
                        'end_time' => $endTimeCalc,
                        'status' => ['pending', 'approved']
                    ]);
                    
                    $slot = [
                        'date' => $dateStr,
                        'start_time' => $time,
                        'end_time' => $endTimeCalc
                    ];
                    
                    if ($isBooked) {
                        $bookedSlots[] = $slot;
                    } else {
                        $availableSlots[] = $slot;
                    }
                }
            }
            break;

        case 'lpm':
            // LPMs: Continuous daily time range, recurring daily or on specific weekdays
            if (empty($startTime) || empty($endTime)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Start and end time required for LPM']);
                exit;
            }
            
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            $interval = new DateInterval('P1D');
            
            for ($d = clone $start; $d <= $end; $d->add($interval)) {
                $dateStr = $d->format('Y-m-d');
                $dayOfWeek = strtolower($d->format('l'));
                
                // Check if this day matches recurrence pattern
                $includeDay = false;
                if ($recurrence === 'daily') {
                    $includeDay = true;
                } elseif ($recurrence === 'weekdays' && !empty($weekdays)) {
                    $includeDay = in_array($dayOfWeek, array_map('strtolower', $weekdays));
                }
                
                if ($includeDay) {
                    // Check if this slot is already booked
                    $isBooked = $sessionModel->exists([
                        'session_date' => $dateStr,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'status' => ['pending', 'approved']
                    ]);
                    
                    $slot = [
                        'date' => $dateStr,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'day_of_week' => $dayOfWeek
                    ];
                    
                    if ($isBooked) {
                        $bookedSlots[] = $slot;
                    } else {
                        $availableSlots[] = $slot;
                    }
                }
            }
            break;

        case 'talkshow':
            // Talkshows: Longer segments, one-off or recurring weekly
            if (empty($startTime) || empty($endTime)) {
                http_response_code(400);
                echo json_encode(['success' => false, 'message' => 'Start and end time required for Talkshow']);
                exit;
            }
            
            $start = new DateTime($startDate);
            $end = new DateTime($endDate);
            
            if ($recurrence === 'once') {
                // One-time talkshow
                $dateStr = $startDate;
                
                $isBooked = $sessionModel->exists([
                    'session_date' => $dateStr,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => ['pending', 'approved']
                ]);
                
                $slot = [
                    'date' => $dateStr,
                    'start_time' => $startTime,
                    'end_time' => $endTime
                ];
                
                if ($isBooked) {
                    $bookedSlots[] = $slot;
                } else {
                    $availableSlots[] = $slot;
                }
            } elseif ($recurrence === 'weekly') {
                // Weekly recurring talkshow
                $targetWeekday = !empty($weekdays) ? strtolower($weekdays[0]) : strtolower($start->format('l'));
                $interval = new DateInterval('P1D');
                
                for ($d = clone $start; $d <= $end; $d->add($interval)) {
                    $dateStr = $d->format('Y-m-d');
                    $dayOfWeek = strtolower($d->format('l'));
                    
                    if ($dayOfWeek === $targetWeekday) {
                        $isBooked = $sessionModel->exists([
                            'session_date' => $dateStr,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'status' => ['pending', 'approved']
                        ]);
                        
                        $slot = [
                            'date' => $dateStr,
                            'start_time' => $startTime,
                            'end_time' => $endTime,
                            'day_of_week' => $dayOfWeek
                        ];
                        
                        if ($isBooked) {
                            $bookedSlots[] = $slot;
                        } else {
                            $availableSlots[] = $slot;
                        }
                    }
                }
            }
            break;

        default:
            http_response_code(400);
            echo json_encode(['success' => false, 'message' => 'Invalid ad type']);
            exit;
    }

    // Return results
    echo json_encode([
        'success' => true,
        'available_slots' => $availableSlots,
        'booked_slots' => $bookedSlots,
        'total_requested' => count($availableSlots) + count($bookedSlots),
        'total_available' => count($availableSlots),
        'total_booked' => count($bookedSlots),
        'all_available' => count($bookedSlots) === 0
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Server error: ' . $e->getMessage()
    ]);
}

