<?php

namespace App\Models;

/**
 * Slot Model
 * Handles slot-related database operations
 */
class Slot extends BaseModel
{
    protected $table = 'slots';
    protected $fillable = [
        'station_id', 'date', 'start_time', 'end_time', 'price', 
        'status', 'description', 'created_by'
    ];

    /**
     * Find slots by station
     */
    public function findByStation($stationId)
    {
        return $this->where('station_id', $stationId);
    }

    /**
     * Find available slots
     */
    public function findAvailable()
    {
        return $this->where('status', 'available');
    }

    /**
     * Find slots by date range
     */
    public function findByDateRange($startDate, $endDate, $stationId = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE date BETWEEN ? AND ?";
        $params = [$startDate, $endDate];
        
        if ($stationId) {
            $sql .= " AND station_id = ?";
            $params[] = $stationId;
        }
        
        $sql .= " ORDER BY date, start_time";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Count available slots
     */
    public function countAvailableSlots()
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'available'";
        $result = $this->db->fetch($sql);
        return (int) $result['count'];
    }

    /**
     * Get today's slots with bookings
     */
    public function getTodaysSlotsWithBookings()
    {
        $sql = "
            SELECT 
                s.*,
                b.id as booking_id,
                b.status as booking_status,
                u.name as advertiser_name
            FROM {$this->table} s
            LEFT JOIN bookings b ON s.id = b.slot_id AND b.status = 'approved'
            LEFT JOIN users u ON b.advertiser_id = u.id
            WHERE s.date = CURDATE()
            ORDER BY s.start_time
        ";
        
        return $this->db->fetchAll($sql);
    }

    /**
     * Find slots for a specific date
     */
    public function findByDate($date, $stationId = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE date = ?";
        $params = [$date];
        
        if ($stationId) {
            $sql .= " AND station_id = ?";
            $params[] = $stationId;
        }
        
        $sql .= " ORDER BY start_time";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Find available slots for a specific date
     */
    public function findAvailableByDate($date, $stationId = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE date = ? AND status = 'available'";
        $params = [$date];
        
        if ($stationId) {
            $sql .= " AND station_id = ?";
            $params[] = $stationId;
        }
        
        $sql .= " ORDER BY start_time";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Check for time conflicts
     */
    public function hasTimeConflict($stationId, $date, $startTime, $endTime, $excludeId = null)
    {
        $sql = "
            SELECT COUNT(*) as count 
            FROM {$this->table} 
            WHERE station_id = ? 
            AND date = ? 
            AND status != 'cancelled'
            AND (
                (start_time < ? AND end_time > ?) OR
                (start_time < ? AND end_time > ?) OR
                (start_time >= ? AND end_time <= ?)
            )
        ";
        
        $params = [$stationId, $date, $endTime, $startTime, $endTime, $startTime, $startTime, $endTime];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Get slot statistics
     */
    public function getStats($stationId = null)
    {
        $whereClause = $stationId ? "WHERE station_id = ?" : "";
        $params = $stationId ? [$stationId] : [];
        
        $stats = [];
        
        // Total slots
        $sql = "SELECT COUNT(*) as count FROM {$this->table} $whereClause";
        $result = $this->db->fetch($sql, $params);
        $stats['total'] = $result['count'];
        
        // Available slots
        $sql = "SELECT COUNT(*) as count FROM {$this->table} $whereClause AND status = 'available'";
        if ($stationId) {
            $sql = str_replace('WHERE station_id = ?', 'WHERE station_id = ? AND status = \'available\'', $sql);
        } else {
            $sql = str_replace('FROM {$this->table}', 'FROM {$this->table} WHERE status = \'available\'', $sql);
        }
        $result = $this->db->fetch($sql, $params);
        $stats['available'] = $result['count'];
        
        // Booked slots
        $sql = "SELECT COUNT(*) as count FROM {$this->table} $whereClause AND status = 'booked'";
        if ($stationId) {
            $sql = str_replace('WHERE station_id = ?', 'WHERE station_id = ? AND status = \'booked\'', $sql);
        } else {
            $sql = str_replace('FROM {$this->table}', 'FROM {$this->table} WHERE status = \'booked\'', $sql);
        }
        $result = $this->db->fetch($sql, $params);
        $stats['booked'] = $result['count'];
        
        // Cancelled slots
        $sql = "SELECT COUNT(*) as count FROM {$this->table} $whereClause AND status = 'cancelled'";
        if ($stationId) {
            $sql = str_replace('WHERE station_id = ?', 'WHERE station_id = ? AND status = \'cancelled\'', $sql);
        } else {
            $sql = str_replace('FROM {$this->table}', 'FROM {$this->table} WHERE status = \'cancelled\'', $sql);
        }
        $result = $this->db->fetch($sql, $params);
        $stats['cancelled'] = $result['count'];
        
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
                COUNT(*) as slots_count,
                SUM(s.price) as total_revenue,
                AVG(s.price) as avg_price
            FROM {$this->table} s
            WHERE s.date BETWEEN ? AND ? 
            AND s.status = 'booked'
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
     * Get popular time slots
     */
    public function getPopularTimeSlots($stationId = null, $limit = 5)
    {
        $sql = "
            SELECT 
                start_time,
                end_time,
                COUNT(*) as booking_count,
                AVG(price) as avg_price
            FROM {$this->table}
            WHERE status = 'booked'
        ";
        
        $params = [];
        
        if ($stationId) {
            $sql .= " AND station_id = ?";
            $params[] = $stationId;
        }
        
        $sql .= " GROUP BY start_time, end_time ORDER BY booking_count DESC LIMIT ?";
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get slots with booking information
     */
    public function findWithBookings($slotId)
    {
        $sql = "
            SELECT 
                s.*,
                st.name as station_name,
                b.id as booking_id,
                b.status as booking_status,
                b.message as booking_message,
                b.total_amount,
                u.name as advertiser_name,
                u.email as advertiser_email,
                u.company as advertiser_company
            FROM {$this->table} s
            JOIN stations st ON s.station_id = st.id
            LEFT JOIN bookings b ON s.id = b.slot_id
            LEFT JOIN users u ON b.advertiser_id = u.id
            WHERE s.id = ?
        ";
        
        return $this->db->fetch($sql, [$slotId]);
    }

    /**
     * Get upcoming slots
     */
    public function getUpcoming($stationId = null, $limit = 10)
    {
        $sql = "
            SELECT s.*, st.name as station_name
            FROM {$this->table} s
            JOIN stations st ON s.station_id = st.id
            WHERE s.date >= CURDATE()
        ";
        
        $params = [];
        
        if ($stationId) {
            $sql .= " AND s.station_id = ?";
            $params[] = $stationId;
        }
        
        $sql .= " ORDER BY s.date, s.start_time LIMIT ?";
        $params[] = $limit;
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Mark slot as booked
     */
    public function markAsBooked($slotId)
    {
        return $this->update($slotId, ['status' => 'booked']);
    }

    /**
     * Mark slot as available
     */
    public function markAsAvailable($slotId)
    {
        return $this->update($slotId, ['status' => 'available']);
    }

    /**
     * Cancel slot
     */
    public function cancelSlot($slotId)
    {
        return $this->update($slotId, ['status' => 'cancelled']);
    }
}
