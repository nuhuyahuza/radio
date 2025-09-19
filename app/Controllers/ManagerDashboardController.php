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
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        return [
            'pending_bookings' => $this->bookingModel->countByStatus('pending'),
            'todays_bookings' => $this->bookingModel->countTodaysBookings(),
            'available_slots' => $this->slotModel->countAvailableSlots(),
            'monthly_revenue' => $this->bookingModel->getMonthlyRevenue()
        ];
    }

    /**
     * Get pending bookings
     */
    private function getPendingBookings($limit = 10)
    {
        return $this->bookingModel->getByStatusWithDetails('pending', $limit);
    }

    /**
     * Get today's schedule
     */
    private function getTodaysSchedule()
    {
        return $this->slotModel->getTodaysSlotsWithBookings();
    }
}