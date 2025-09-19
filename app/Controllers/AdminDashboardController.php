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

        $currentUser = Session::get('user');
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats();
        
        // Get recent bookings
        $recentBookings = $this->getRecentBookings(10);
        
        // Include dashboard view
        include __DIR__ . '/../../public/views/admin/dashboard.php';
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            'total_bookings' => $this->bookingModel->count(),
            'total_revenue' => $this->bookingModel->getTotalRevenue(),
            'active_advertisers' => $this->userModel->countActiveUsersByRole('advertiser'),
            'pending_bookings' => $this->bookingModel->countByStatus('pending')
        ];
    }

    /**
     * Get recent bookings
     */
    private function getRecentBookings($limit = 10)
    {
        return $this->bookingModel->getRecentWithDetails($limit);
    }
}