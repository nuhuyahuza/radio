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
        AuthMiddleware::requireRole('advertiser');

        $currentUser = Session::getUser();
        
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

    /**
     * Show advertiser bookings
     */
    public function showBookings()
    {
        // Check if user is advertiser
        AuthMiddleware::requireRole('advertiser');

        $currentUser = Session::getUser();
        
        // Get all bookings for this advertiser
        $bookings = $this->bookingModel->getByAdvertiser($currentUser['id']);
        
        // Set variables for the view
        $pageTitle = 'My Bookings';
        $currentPage = 'bookings';
        
        include __DIR__ . '/../../public/views/advertiser/bookings.php';
    }

    /**
     * Show booking details
     */
    public function showBookingDetails($bookingId)
    {
        // Check if user is advertiser
        AuthMiddleware::requireRole('advertiser');

        $currentUser = Session::getUser();
        
        // Get booking details
        $booking = $this->bookingModel->find($bookingId);
        
        if (!$booking) {
            Session::setFlash('error', 'Booking not found.');
            header('Location: /advertiser/bookings');
            exit;
        }

        // Check if booking belongs to this advertiser
        if ($booking['advertiser_id'] != $currentUser['id']) {
            Session::setFlash('error', 'Access denied.');
            header('Location: /advertiser/bookings');
            exit;
        }
        
        // Set variables for the view
        $pageTitle = 'Booking Details';
        $currentPage = 'bookings';
        
        include __DIR__ . '/../../public/views/advertiser/booking-details.php';
    }

    /**
     * Show advertiser profile
     */
    public function showProfile()
    {
        // Check if user is advertiser
        AuthMiddleware::requireRole('advertiser');

        $currentUser = Session::getUser();
        
        // Set variables for the view
        $pageTitle = 'My Profile';
        $currentPage = 'profile';
        
        include __DIR__ . '/../../public/views/advertiser/profile.php';
    }
}