<?php

namespace App\Models;

/**
 * User Model
 * Handles user-related database operations
 */
class User extends BaseModel
{
    protected $table = 'users';
    protected $fillable = [
        'name', 'email', 'password', 'role', 'phone', 'company', 
        'is_active', 'email_verified_at', 'last_login_at'
    ];
    protected $hidden = ['password'];

    /**
     * Find user by email
     */
    public function findByEmail($email)
    {
        return $this->whereFirst('email', $email);
    }

    /**
     * Find active users by role
     */
    public function findByRole($role)
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = ? AND is_active = 1";
        return $this->db->fetchAll($sql, [$role]);
    }

    /**
     * Find active users
     */
    public function findActive()
    {
        return $this->where('is_active', 1);
    }

    /**
     * Verify user password
     */
    public function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }

    /**
     * Hash password
     */
    public function hashPassword($password)
    {
        return password_hash($password, PASSWORD_DEFAULT);
    }

    /**
     * Update last login time
     */
    public function updateLastLogin($userId)
    {
        $sql = "UPDATE {$this->table} SET last_login_at = ? WHERE id = ?";
        return $this->db->execute($sql, [date('Y-m-d H:i:s'), $userId]);
    }

    /**
     * Create user with hashed password
     */
    public function createUser($data)
    {
        if (isset($data['password'])) {
            $data['password'] = $this->hashPassword($data['password']);
        }
        
        return $this->create($data);
    }

    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword)
    {
        $hashedPassword = $this->hashPassword($newPassword);
        return $this->update($userId, ['password' => $hashedPassword]);
    }

    /**
     * Get user statistics
     */
    public function getStats()
    {
        $stats = [];
        
        // Total users
        $stats['total'] = $this->count();
        
        // Users by role
        $roles = ['admin', 'station_manager', 'advertiser'];
        foreach ($roles as $role) {
            $stats[$role] = $this->countWhere('role', $role);
        }
        
        // Active users
        $stats['active'] = $this->countWhere('is_active', 1);
        
        // Recently registered (last 30 days)
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
        $result = $this->db->fetch($sql);
        $stats['recent'] = $result['count'];
        
        return $stats;
    }

    /**
     * Search users
     */
    public function search($query, $role = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE (name LIKE ? OR email LIKE ? OR company LIKE ?)";
        $params = ["%$query%", "%$query%", "%$query%"];
        
        if ($role) {
            $sql .= " AND role = ?";
            $params[] = $role;
        }
        
        $sql .= " ORDER BY created_at DESC";
        
        return $this->db->fetchAll($sql, $params);
    }

    /**
     * Get user with bookings count
     */
    public function findWithBookingsCount($userId)
    {
        $sql = "
            SELECT u.*, 
                   COUNT(b.id) as bookings_count,
                   COUNT(CASE WHEN b.status = 'approved' THEN 1 END) as approved_bookings,
                   COUNT(CASE WHEN b.status = 'pending' THEN 1 END) as pending_bookings
            FROM {$this->table} u
            LEFT JOIN bookings b ON u.id = b.advertiser_id
            WHERE u.id = ?
            GROUP BY u.id
        ";
        
        return $this->db->fetch($sql, [$userId]);
    }

    /**
     * Check if email exists
     */
    public function emailExists($email, $excludeId = null)
    {
        $sql = "SELECT COUNT(*) as count FROM {$this->table} WHERE email = ?";
        $params = [$email];
        
        if ($excludeId) {
            $sql .= " AND id != ?";
            $params[] = $excludeId;
        }
        
        $result = $this->db->fetch($sql, $params);
        return $result['count'] > 0;
    }

    /**
     * Get user's recent activity
     */
    public function getRecentActivity($userId, $limit = 10)
    {
        $sql = "
            SELECT 'booking' as type, b.created_at, b.status, s.date, s.start_time, s.end_time
            FROM bookings b
            JOIN slots s ON b.slot_id = s.id
            WHERE b.advertiser_id = ?
            UNION ALL
            SELECT 'audit' as type, al.created_at, al.action, NULL, NULL, NULL
            FROM audit_logs al
            WHERE al.user_id = ?
            ORDER BY created_at DESC
            LIMIT ?
        ";
        
        return $this->db->fetchAll($sql, [$userId, $userId, $limit]);
    }
}
