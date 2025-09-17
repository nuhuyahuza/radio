<?php
/**
 * Slots API - Returns available slots in JSON format
 * This is a placeholder implementation for Todo 04
 */

header('Content-Type: application/json');

// Sample data - will be replaced with actual database queries in later todos
$slots = [
    [
        'id' => 1,
        'title' => 'Morning Drive (6:00 AM - 9:00 AM)',
        'start' => date('Y-m-d', strtotime('+1 day')) . 'T06:00:00',
        'end' => date('Y-m-d', strtotime('+1 day')) . 'T09:00:00',
        'status' => 'available',
        'price' => 150.00,
        'color' => '#28a745'
    ],
    [
        'id' => 2,
        'title' => 'Midday (12:00 PM - 2:00 PM)',
        'start' => date('Y-m-d', strtotime('+1 day')) . 'T12:00:00',
        'end' => date('Y-m-d', strtotime('+1 day')) . 'T14:00:00',
        'status' => 'available',
        'price' => 100.00,
        'color' => '#28a745'
    ],
    [
        'id' => 3,
        'title' => 'Evening Rush (5:00 PM - 7:00 PM)',
        'start' => date('Y-m-d', strtotime('+1 day')) . 'T17:00:00',
        'end' => date('Y-m-d', strtotime('+1 day')) . 'T19:00:00',
        'status' => 'available',
        'price' => 200.00,
        'color' => '#28a745'
    ],
    [
        'id' => 4,
        'title' => 'Morning Drive (6:00 AM - 9:00 AM)',
        'start' => date('Y-m-d', strtotime('+2 days')) . 'T06:00:00',
        'end' => date('Y-m-d', strtotime('+2 days')) . 'T09:00:00',
        'status' => 'booked',
        'price' => 150.00,
        'color' => '#dc3545'
    ],
    [
        'id' => 5,
        'title' => 'Midday (12:00 PM - 2:00 PM)',
        'start' => date('Y-m-d', strtotime('+2 days')) . 'T12:00:00',
        'end' => date('Y-m-d', strtotime('+2 days')) . 'T14:00:00',
        'status' => 'available',
        'price' => 100.00,
        'color' => '#28a745'
    ],
    [
        'id' => 6,
        'title' => 'Evening Rush (5:00 PM - 7:00 PM)',
        'start' => date('Y-m-d', strtotime('+2 days')) . 'T17:00:00',
        'end' => date('Y-m-d', strtotime('+2 days')) . 'T19:00:00',
        'status' => 'available',
        'price' => 200.00,
        'color' => '#28a745'
    ]
];

// Add more sample data for the next week
for ($i = 3; $i <= 7; $i++) {
    $date = date('Y-m-d', strtotime("+$i days"));
    
    $slots[] = [
        'id' => $i * 3 + 1,
        'title' => 'Morning Drive (6:00 AM - 9:00 AM)',
        'start' => $date . 'T06:00:00',
        'end' => $date . 'T09:00:00',
        'status' => 'available',
        'price' => 150.00,
        'color' => '#28a745'
    ];
    
    $slots[] = [
        'id' => $i * 3 + 2,
        'title' => 'Midday (12:00 PM - 2:00 PM)',
        'start' => $date . 'T12:00:00',
        'end' => $date . 'T14:00:00',
        'status' => 'available',
        'price' => 100.00,
        'color' => '#28a745'
    ];
    
    $slots[] = [
        'id' => $i * 3 + 3,
        'title' => 'Evening Rush (5:00 PM - 7:00 PM)',
        'start' => $date . 'T17:00:00',
        'end' => $date . 'T19:00:00',
        'status' => 'available',
        'price' => 200.00,
        'color' => '#28a745'
    ];
}

echo json_encode($slots);
?>
