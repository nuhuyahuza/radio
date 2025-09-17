<?php

namespace App\Models;

/**
 * Booking Model
 * Handles booking-related database operations
 */
class Booking extends BaseModel
{
    protected $table = 'bookings';
    protected $fillable = [
        'advertiser_id', 'slot_id', 'status', 'message', 'total_amount',
        'payment_status', 'payment_method', 'payment_reference', 'approved_by',
        'approved_at', 'rejected_reason'
    ];

    /**
     * Find bookings by advertiser
     */
    public function findByAdvertiser($advertiserId)
    {
        $sql = "
            SELECT 
                b.*,
                s.date,
                s.start_time,
                s.end_time,
                s.price,
                st.name as station_name
            FROM {$this->table} b
            JOIN slots s ON b.slot_id = s.id
            JOIN stations st ON s.station_id = st.id
            WHERE b.advertiser_id = ?
            ORDER BY b.created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$advertiserId]);
    }

    /**
     * Find bookings by status
     */
    public function findByStatus($status)
    {
        return $this->where('status', $status);
    }

    /**
     * Find pending bookings
     */
    public function findPending()
    {
        return $this->findByStatus('pending');
    }

    /**
     * Find approved bookings
     */
    public function findApproved()
    {
        return $this->findByStatus('approved');
    }

    /**
     * Find rejected bookings
     */
    public function findRejected()
    {
        return $this->findByStatus('rejected');
    }

    /**
     * Find bookings with full details
     */
    public function findWithDetails($bookingId)
    {
        $sql = "
            SELECT 
                b.*,
                s.date,
                s.start_time,
                s.end_time,
                s.price,
                s.description as slot_description,
                st.name as station_name,
                u.name as advertiser_name,
                u.email as advertiser_email,
                u.phone as advertiser_phone,
                u.company as advertiser_company,
                approver.name as approved_by_name
            FROM {$this->table} b
            JOIN slots s ON b.slot_id = s.id
            JOIN stations st ON s.station_id = st.id
            JOIN users u ON b.advertiser_id = u.id
            LEFT JOIN users approver ON b.approved_by = approver.id
            WHERE b.id = ?
        ";
        
        return $this->db->fetch($sql, [$bookingId]);
    }

    /**
     * Find all bookings with details
     */
    public function findAllWithDetails($limit = null, $offset = 0)
    {
        $sql = "
            SELECT 
                b.*,
                s.date,
                s.start_time,
                s.end_time,
                s.price,
                st.name as station_name,
                u.name as advertiser_name,
                u.email as advertiser_email,
                u.company as advertiser_company,
                approver.name as approved_by_name
            FROM {$this->table} b
            JOIN slots s ON b.slot_id = s.id
            JOIN stations st ON s.station_id = st.id
            JOIN users u ON b.advertiser_id = u.id
            LEFT JOIN users approver ON b.approved_by = approver.id
            ORDER BY b.created_at DESC
        ";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            return $this->db->fetchAll($sql, [$limit, $offset]);
        }
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Approve booking
     */
    public function approve($bookingId, $approvedBy)
    {
        $data = [
            'status' => 'approved',
            'approved_by' => $approvedBy,
            'approved_at' => date('Y-m-d H:i:s'),
            'payment_status' => 'paid'
        ];
        
        return $this->update($bookingId, $data);
    }

    /**
     * Reject booking
     */
    public function reject($bookingId, $rejectedBy, $reason = null)
    {
        $data = [
            'status' => 'rejected',
            'approved_by' => $rejectedBy,
            'rejected_reason' => $reason
        ];
        
        return $this->update($bookingId, $data);
    }

    /**
     * Cancel booking
     */
    public function cancel($bookingId, $reason = null)
    {
        $data = [
            'status' => 'cancelled',
            'rejected_reason' => $reason
        ];
        
        return $this->update($bookingId, $data);
    }

    /**
     * Get booking statistics
     */
    public function getStats($stationId = null, $dateFrom = null, $dateTo = null)
    {
        $whereConditions = [];
        $params = [];
        
        if ($stationId) {
            $whereConditions[] = "s.station_id = ?";
            $params[] = $stationId;
        }
        
        if ($dateFrom) {
            $whereConditions[] = "s.date >= ?";
            $params[] = $dateFrom;
        }
        
        if ($dateTo) {
            $whereConditions[] = "s.date <= ?";
            $params[] = $dateTo;
        }
        
        $whereClause = !empty($whereConditions) ? "WHERE " . implode(" AND ", $whereConditions) : "";
        
        $stats = [];
        
        // Total bookings
        $sql = "
            SELECT COUNT(*) as count 
            FROM {$this->table} b
            JOIN slots s ON b.slot_id = s.id
            $whereClause
        ";
        $result = $this->db->fetch($sql, $params);
        $stats['total'] = $result['count'];
        
        // Bookings by status
        $statuses = ['pending', 'approved', 'rejected', 'cancelled'];
        foreach ($statuses as $status) {
            $sql = "
                SELECT COUNT(*) as count 
                FROM {$this->table} b
                JOIN slots s ON b.slot_id = s.id
                $whereClause AND b.status = ?
            ";
            $statusParams = array_merge($params, [$status]);
            $result = $this->db->fetch($sql, $statusParams);
            $stats[$status] = $result['count'];
        }
        
        // Total revenue
        $sql = "
            SELECT COALESCE(SUM(b.total_amount), 0) as total_revenue
            FROM {$this->table} b
            JOIN slots s ON b.slot_id = s.id
            $whereClause AND b.status = 'approved'
        ";
        $result = $this->db->fetch($sql, $params);
        $stats['revenue'] = $result['total_revenue'];
        
        return $stats;
    }

    /**
     * Get revenue by date range
     */
    public function getRevenueByDateRange($startDate, $endDate, $stationId = null)
    {
        $sql = "
            SELECT 
                DATE(s.date) as date,
                COUNT(*) as bookings_count,
                SUM(b.total_amount) as total_revenue,
                AVG(b.total_amount) as avg_amount
            FROM {$this->table} b
            JOIN slots s ON b.slot_id = s.id
            WHERE s.date BETWEEN ? AND ? 
            AND b.status = 'approved'
        ";
        
        $params = [$startDate, $endDate];
        
        if ($stationId) {
            $sql .= " AND s.station_id = ?";
            $params[] = $stationId;
        }
        
        $sql .= " GROUP BY DATE(s.date) ORDER BY s.date";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get top advertisers
     */
    public function getTopAdvertisers($stationId = null, $limit = 10)
    {
        $sql = "
            SELECT 
                u.id,
                u.name,
                u.company,
                u.email,
                COUNT(b.id) as bookings_count,
                SUM(b.total_amount) as total_spent,
                AVG(b.total_amount) as avg_amount
            FROM {$this->table} b
            JOIN users u ON b.advertiser_id = u.id
            JOIN slots s ON b.slot_id = s.id
            WHERE b.status = 'approved'
        ";
        
        $params = [];
        
        if ($stationId) {
            $sql .= " AND s.station_id = ?";
            $params[] = $stationId;
        }
        
        $sql .= " GROUP BY u.id ORDER BY total_spent DESC LIMIT ?";
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get recent bookings
     */
    public function getRecent($limit = 10, $status = null)
    {
        $sql = "
            SELECT 
                b.*,
                s.date,
                s.start_time,
                s.end_time,
                st.name as station_name,
                u.name as advertiser_name,
                u.company as advertiser_company
            FROM {$this->table} b
            JOIN slots s ON b.slot_id = s.id
            JOIN stations st ON s.station_id = st.id
            JOIN users u ON b.advertiser_id = u.id
        ";
        
        $params = [];
        
        if ($status) {
            $sql .= " WHERE b.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY b.created_at DESC LIMIT ?";
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Check if slot is already booked
     */
    public function isSlotBooked($slotId)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE slot_id = ? AND status IN ('pending', 'approved')";
        $result = $this->db->fetch($sql, [$slotId]);
        return $result['count'] > 0;
    }

    /**
     * Get bookings for a specific slot
     */
    public function findBySlot($slotId)
    {
        $sql = "
            SELECT 
                b.*,
                u.name as advertiser_name,
                u.email as advertiser_email,
                u.company as advertiser_company
            FROM {$this->table} b
            JOIN users u ON b.advertiser_id = u.id
            WHERE b.slot_id = ?
            ORDER BY b.created_at DESC
        ";
        
        return $this->db->fetchAll($sql, [$slotId]);
    }
}
