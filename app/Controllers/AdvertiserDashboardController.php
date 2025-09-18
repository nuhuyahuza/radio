<?php

namespace App\Controllers;

use App\Models\User;
use App\Models\Booking;
use App\Utils\Session;
use App\Middleware\AuthMiddleware;

/**
 * Advertiser Dashboard Controller
 * Handles advertiser dashboard functionality
 */
class AdvertiserDashboardController
{
    private $userModel;
    private $bookingModel;

    public function __construct()
    {
        $this->userModel = new User();
        $this->bookingModel = new Booking();
    }

    /**
     * Show advertiser dashboard
     */
    public function showDashboard()
    {
        // Check if user is advertiser
        if (!AuthMiddleware::checkRole('advertiser')) {
            Session::setFlash('error', 'Access denied. Advertiser privileges required.');
            header('Location: /login');
            exit;
        }

        $currentUser = Session::get('user');
        
        // Get dashboard statistics
        $stats = $this->getDashboardStats($currentUser['id']);
        
        // Get recent bookings
        $recentBookings = $this->getRecentBookings($currentUser['id'], 10);
        
        // Include dashboard view
        include __DIR__ . '/../../public/views/advertiser/dashboard.php';
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats($advertiserId)
    {
        return [
            'total_bookings' => $this->bookingModel->countByAdvertiser($advertiserId),
            'approved_bookings' => $this->bookingModel->countByAdvertiserAndStatus($advertiserId, 'approved'),
            'pending_bookings' => $this->bookingModel->countByAdvertiserAndStatus($advertiserId, 'pending'),
            'total_spent' => $this->bookingModel->getTotalSpentByAdvertiser($advertiserId)
        ];
    }

    /**
     * Get recent bookings for advertiser
     */
    private function getRecentBookings($advertiserId, $limit = 10)
    {
        return $this->bookingModel->getRecentByAdvertiser($advertiserId, $limit);
    }
}
