<?php

namespace App\Controllers;

use App\Models\Booking;
use App\Models\Slot;
use App\Models\User;
use App\Utils\Session;
use App\Database\Database;

/**
 * Reports Controller
 * Handles reports and analytics functionality
 */
class ReportsController
{
    private $bookingModel;
    private $slotModel;
    private $userModel;
    private $db;

    public function __construct()
    {
        $this->bookingModel = new Booking();
        $this->slotModel = new Slot();
        $this->userModel = new User();
        $this->db = Database::getInstance();
    }

    /**
     * Show reports dashboard
     */
    public function showReports()
    {
        $stats = $this->getDashboardStats();
        include __DIR__ . '/../../public/views/admin/reports.php';
    }

    /**
     * Get booking analytics
     */
    public function getBookingAnalytics()
    {
        header('Content-Type: application/json');

        try {
            $startDate = $_GET['start_date'] ?? date('Y-m-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-t');
            $groupBy = $_GET['group_by'] ?? 'day';

            $analytics = $this->getBookingAnalyticsData($startDate, $endDate, $groupBy);
            
            echo json_encode($analytics);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to fetch booking analytics',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get revenue analytics
     */
    public function getRevenueAnalytics()
    {
        header('Content-Type: application/json');

        try {
            $startDate = $_GET['start_date'] ?? date('Y-m-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-t');
            $groupBy = $_GET['group_by'] ?? 'day';

            $analytics = $this->getRevenueAnalyticsData($startDate, $endDate, $groupBy);
            
            echo json_encode($analytics);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to fetch revenue analytics',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get user analytics
     */
    public function getUserAnalytics()
    {
        header('Content-Type: application/json');

        try {
            $startDate = $_GET['start_date'] ?? date('Y-m-01');
            $endDate = $_GET['end_date'] ?? date('Y-m-t');

            $analytics = $this->getUserAnalyticsData($startDate, $endDate);
            
            echo json_encode($analytics);

        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'error' => 'Failed to fetch user analytics',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Export reports to CSV
     */
    public function exportReports()
    {
        $type = $_GET['type'] ?? 'bookings';
        $startDate = $_GET['start_date'] ?? date('Y-m-01');
        $endDate = $_GET['end_date'] ?? date('Y-m-t');

        try {
            switch ($type) {
                case 'bookings':
                    $this->exportBookingsCSV($startDate, $endDate);
                    break;
                case 'revenue':
                    $this->exportRevenueCSV($startDate, $endDate);
                    break;
                case 'users':
                    $this->exportUsersCSV();
                    break;
                default:
                    throw new \Exception('Invalid export type');
            }
        } catch (\Exception $e) {
            Session::setFlash('error', 'Export failed: ' . $e->getMessage());
            header('Location: /admin/reports');
            exit;
        }
    }

    /**
     * Get dashboard statistics
     */
    private function getDashboardStats()
    {
        $stats = [];

        // Total bookings
        $sql = "SELECT COUNT(*) as total FROM bookings";
        $result = $this->db->fetch($sql);
        $stats['total_bookings'] = $result['total'];

        // Total revenue
        $sql = "SELECT SUM(total_amount) as total FROM bookings WHERE status = 'approved'";
        $result = $this->db->fetch($sql);
        $stats['total_revenue'] = $result['total'] ?? 0;

        // Active users
        $sql = "SELECT COUNT(*) as total FROM users WHERE is_active = 1";
        $result = $this->db->fetch($sql);
        $stats['active_users'] = $result['total'];

        // Pending bookings
        $sql = "SELECT COUNT(*) as total FROM bookings WHERE status = 'pending'";
        $result = $this->db->fetch($sql);
        $stats['pending_bookings'] = $result['total'];

        // This month's bookings
        $sql = "SELECT COUNT(*) as total FROM bookings WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $result = $this->db->fetch($sql);
        $stats['this_month_bookings'] = $result['total'];

        // This month's revenue
        $sql = "SELECT SUM(total_amount) as total FROM bookings WHERE status = 'approved' AND MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
        $result = $this->db->fetch($sql);
        $stats['this_month_revenue'] = $result['total'] ?? 0;

        // Top advertisers
        $sql = "
            SELECT u.name, u.email, COUNT(b.id) as booking_count, SUM(b.total_amount) as total_spent
            FROM users u
            JOIN bookings b ON u.id = b.advertiser_id
            WHERE b.status = 'approved'
            GROUP BY u.id
            ORDER BY booking_count DESC
            LIMIT 5
        ";
        $stats['top_advertisers'] = $this->db->fetchAll($sql);

        // Booking status distribution
        $sql = "
            SELECT status, COUNT(*) as count
            FROM bookings
            GROUP BY status
        ";
        $result = $this->db->fetchAll($sql);
        $stats['booking_status_distribution'] = [];
        foreach ($result as $row) {
            $stats['booking_status_distribution'][$row['status']] = $row['count'];
        }

        return $stats;
    }

    /**
     * Get booking analytics data
     */
    private function getBookingAnalyticsData($startDate, $endDate, $groupBy)
    {
        $dateFormat = $groupBy === 'month' ? '%Y-%m' : '%Y-%m-%d';
        
        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '$dateFormat') as period,
                COUNT(*) as bookings,
                COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved,
                COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending,
                COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected
            FROM bookings
            WHERE DATE(created_at) BETWEEN ? AND ?
            GROUP BY period
            ORDER BY period
        ";

        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }

    /**
     * Get revenue analytics data
     */
    private function getRevenueAnalyticsData($startDate, $endDate, $groupBy)
    {
        $dateFormat = $groupBy === 'month' ? '%Y-%m' : '%Y-%m-%d';
        
        $sql = "
            SELECT 
                DATE_FORMAT(created_at, '$dateFormat') as period,
                SUM(total_amount) as revenue,
                COUNT(*) as bookings
            FROM bookings
            WHERE status = 'approved' AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY period
            ORDER BY period
        ";

        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }

    /**
     * Get user analytics data
     */
    private function getUserAnalyticsData($startDate, $endDate)
    {
        $sql = "
            SELECT 
                role,
                COUNT(*) as total,
                COUNT(CASE WHEN is_active = 1 THEN 1 END) as active,
                COUNT(CASE WHEN DATE(created_at) BETWEEN ? AND ? THEN 1 END) as new_registrations
            FROM users
            GROUP BY role
        ";

        return $this->db->fetchAll($sql, [$startDate, $endDate]);
    }

    /**
     * Export bookings to CSV
     */
    private function exportBookingsCSV($startDate, $endDate)
    {
        $sql = "
            SELECT 
                b.id,
                b.status,
                b.total_amount,
                b.created_at,
                u.name as advertiser_name,
                u.email as advertiser_email,
                s.date,
                s.start_time,
                s.end_time
            FROM bookings b
            JOIN users u ON b.advertiser_id = u.id
            JOIN slots s ON b.slot_id = s.id
            WHERE DATE(b.created_at) BETWEEN ? AND ?
            ORDER BY b.created_at DESC
        ";

        $bookings = $this->db->fetchAll($sql, [$startDate, $endDate]);

        $filename = "bookings_export_{$startDate}_to_{$endDate}.csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'Booking ID',
            'Advertiser Name',
            'Advertiser Email',
            'Date',
            'Start Time',
            'End Time',
            'Status',
            'Amount',
            'Created At'
        ]);

        // CSV data
        foreach ($bookings as $booking) {
            fputcsv($output, [
                $booking['id'],
                $booking['advertiser_name'],
                $booking['advertiser_email'],
                $booking['date'],
                $booking['start_time'],
                $booking['end_time'],
                $booking['status'],
                $booking['total_amount'],
                $booking['created_at']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export revenue to CSV
     */
    private function exportRevenueCSV($startDate, $endDate)
    {
        $sql = "
            SELECT 
                DATE(created_at) as date,
                COUNT(*) as bookings,
                SUM(total_amount) as revenue
            FROM bookings
            WHERE status = 'approved' AND DATE(created_at) BETWEEN ? AND ?
            GROUP BY DATE(created_at)
            ORDER BY date
        ";

        $revenue = $this->db->fetchAll($sql, [$startDate, $endDate]);

        $filename = "revenue_export_{$startDate}_to_{$endDate}.csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, ['Date', 'Bookings', 'Revenue']);

        // CSV data
        foreach ($revenue as $row) {
            fputcsv($output, [
                $row['date'],
                $row['bookings'],
                $row['revenue']
            ]);
        }

        fclose($output);
        exit;
    }

    /**
     * Export users to CSV
     */
    private function exportUsersCSV()
    {
        $sql = "SELECT * FROM users ORDER BY created_at DESC";
        $users = $this->db->fetchAll($sql);

        $filename = "users_export_" . date('Y-m-d') . ".csv";
        
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        
        // CSV headers
        fputcsv($output, [
            'ID',
            'Name',
            'Email',
            'Role',
            'Phone',
            'Company',
            'Active',
            'Created At',
            'Last Login'
        ]);

        // CSV data
        foreach ($users as $user) {
            fputcsv($output, [
                $user['id'],
                $user['name'],
                $user['email'],
                $user['role'],
                $user['phone'],
                $user['company'],
                $user['is_active'] ? 'Yes' : 'No',
                $user['created_at'],
                $user['last_login_at']
            ]);
        }

        fclose($output);
        exit;
    }
}

