<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Models\Slot;
use App\Utils\Session;
use App\Middleware\AuthMiddleware;

/**
 * Admin Dashboard Controller
 * Handles admin dashboard functionality
 */
class AdminDashboardController
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
     * Show admin dashboard
     */
    public function showDashboard()
    {
        // Check if user is admin
        AuthMiddleware::requireRole('admin');

        $currentUser = Session::getUser();
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent bookings
        $recentBookings = $this->getRecentBookings(10);
        
        // Include dashboard view
        include __DIR__ . '/../../public/views/admin/dashboard.php';
    }

    /**
     * Get dashboard statistics with ad type breakdown
     */
    private function getDashboardStats()
    {
        // Get base stats
        $stats = [
            'total_bookings' => $this->bookingModel->count(),
            'total_revenue' => $this->bookingModel->getTotalRevenue(),
            'active_advertisers' => $this->userModel->countActiveUsersByRole('advertiser'),
            'pending_bookings' => $this->bookingModel->countByStatus('pending')
        ];
        
        // Get ad type breakdown
        $adTypeQuery = "
            SELECT ad_type, COUNT(*) as count, SUM(total_amount) as revenue
            FROM bookings
            WHERE ad_type IS NOT NULL
            GROUP BY ad_type
        ";
        $adTypeStats = $this->bookingModel->fetchAll($adTypeQuery);
        
        $stats['jingle_count'] = 0;
        $stats['lpm_count'] = 0;
        $stats['talkshow_count'] = 0;
        
        foreach ($adTypeStats as $row) {
            $stats[$row['ad_type'] . '_count'] = $row['count'];
            $stats[$row['ad_type'] . '_revenue'] = $row['revenue'];
        }
        
        return $stats;
    }

    /**
     * Get recent bookings with session counts
     */
    private function getRecentBookings($limit = 10)
    {
        $bookings = $this->bookingModel->getRecentWithDetails($limit);
        
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
     * Show settings page
     */
    public function showSettings()
    {
        // Check if user is admin
        AuthMiddleware::requireRole('admin');

        $currentUser = Session::getUser();
        
        // Include settings view
        include __DIR__ . '/../../public/views/admin/settings.php';
    }
}