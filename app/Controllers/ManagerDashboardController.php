<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Slot;
use App\Utils\Session;
use App\Middleware\AuthMiddleware;

/**
 * Manager Dashboard Controller
 * Handles station manager dashboard functionality
 */
class ManagerDashboardController
{
    private $userModel;
    private $bookingModel;
    private $slotModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->bookingModel = new Booking();
        $this->slotModel = new Slot();
    }

    /**
     * Show manager dashboard
     */
    public function showDashboard()
    {
        // Check if user is station manager
        AuthMiddleware::requireRole('station_manager');

        $currentUser = Session::getUser();
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get pending bookings
        $pendingBookings = $this->getPendingBookings(10);
        
        // Get today's schedule
        $todaysSchedule = $this->getTodaysSchedule();
        
        // Include dashboard view
        include __DIR__ . '/../../public/views/manager/dashboard.php';
    }

    /**
     * Get dashboard statistics with ad type breakdown
     */
    private function getDashboardStats()
    {
        $stats = [
            'pending_bookings' => $this->bookingModel->countByStatus('pending'),
            'todays_bookings' => $this->bookingModel->countTodaysBookings(),
            'available_slots' => $this->slotModel->countAvailableSlots(),
            'monthly_revenue' => $this->bookingModel->getMonthlyRevenue()
        ];
        
        // Get ad type breakdown for pending bookings
        $adTypeQuery = "
            SELECT ad_type, COUNT(*) as count
            FROM bookings
            WHERE ad_type IS NOT NULL AND status = 'pending'
            GROUP BY ad_type
        ";
        $adTypeStats = $this->bookingModel->fetchAll($adTypeQuery);
        
        $stats['pending_jingles'] = 0;
        $stats['pending_lpms'] = 0;
        $stats['pending_talkshows'] = 0;
        
        foreach ($adTypeStats as $row) {
            $stats['pending_' . $row['ad_type'] . 's'] = $row['count'];
        }
        
        return $stats;
    }

    /**
     * Get pending bookings with session counts
     */
    private function getPendingBookings($limit = 10)
    {
        $bookings = $this->bookingModel->getByStatusWithDetails('pending', $limit);
        
        // Add session count for each booking
        $sessionModel = new \App\Models\BookingSession();
        foreach ($bookings as &$booking) {
            if ($booking['ad_type']) {
                // This is a campaign booking, get session count
                $sessions = $sessionModel->findByBooking($booking['id']);
                $booking['session_count'] = count($sessions);
            } else {
                // Traditional single-slot booking
                $booking['session_count'] = 1;
            }
        }
        
        return $bookings;
    }

    /**
     * Get today's schedule
     */
    private function getTodaysSchedule()
    {
        return $this->slotModel->getTodaysSlotsWithBookings();
    }
}