<?php

namespace App\Models;

/**
 * Settings Model
 * Handles application settings
 */
class Settings extends BaseModel
{
    protected $table = 'settings';
    protected $fillable = [
        'key', 'value', 'type', 'description', 'is_public'
    ];

    /**
     * Get setting by key
     */
    public function getSetting($key, $default = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE `key` = ? LIMIT 1";
        $setting = $this->db->fetch($sql, [$key]);
        return $setting ? $setting['value'] : $default;
    }

    /**
     * Set setting value
     */
    public function setSetting($key, $value, $type = 'string', $description = '', $isPublic = false)
    {
        $sql = "SELECT * FROM {$this->table} WHERE `key` = ? LIMIT 1";
        $existing = $this->db->fetch($sql, [$key]);
        
        if ($existing) {
            $updateSql = "UPDATE {$this->table} SET value = ?, type = ?, description = ?, is_public = ?, updated_at = ? WHERE id = ?";
            return $this->db->execute($updateSql, [$value, $type, $description, $isPublic ? 1 : 0, date('Y-m-d H:i:s'), $existing['id']]);
        } else {
            $insertSql = "INSERT INTO {$this->table} (`key`, value, type, description, is_public, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)";
            return $this->db->execute($insertSql, [$key, $value, $type, $description, $isPublic ? 1 : 0, date('Y-m-d H:i:s'), date('Y-m-d H:i:s')]);
        }
    }

    /**
     * Get all public settings
     */
    public function getPublicSettings()
    {
        $sql = "SELECT * FROM {$this->table} WHERE is_public = 1";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get all settings
     */
    public function getAllSettings()
    {
        $sql = "SELECT * FROM {$this->table} ORDER BY `key`";
        return $this->db->fetchAll($sql);
    }

    /**
     * Get app configuration
     */
    public function getAppConfig()
    {
        $settings = $this->getAllSettings();
        $config = [];
        
        foreach ($settings as $setting) {
            $config[$setting['key']] = $this->castValue($setting['value'], $setting['type']);
        }
        
        return $config;
    }

    /**
     * Cast value based on type
     */
    private function castValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return (bool) $value;
            case 'integer':
                return (int) $value;
            case 'float':
                return (float) $value;
            case 'json':
                return json_decode($value, true);
            default:
                return $value;
        }
    }

    /**
     * Initialize default settings
     */
    public function initializeDefaults()
    {
        $defaults = [
            'app_name' => ['Zaa Radio', 'string', 'Application name'],
            'app_description' => ['Radio Station Booking System', 'string', 'Application description'],
            'app_logo' => ['/assets/images/logo.png', 'string', 'Application logo path'],
            'contact_email' => ['admin@zaaradio.com', 'string', 'Contact email address'],
            'contact_phone' => ['+233 123 456 789', 'string', 'Contact phone number'],
            'contact_address' => ['Accra, Ghana', 'string', 'Contact address'],
            'currency' => ['GHS', 'string', 'Default currency'],
            'currency_symbol' => ['GH₵', 'string', 'Currency symbol'],
            'timezone' => ['Africa/Accra', 'string', 'Default timezone'],
            'date_format' => ['Y-m-d', 'string', 'Date format'],
            'time_format' => ['H:i', 'string', 'Time format'],
            'items_per_page' => [10, 'integer', 'Items per page'],
            'maintenance_mode' => [false, 'boolean', 'Maintenance mode'],
            'registration_enabled' => [true, 'boolean', 'User registration enabled'],
            'email_notifications' => [true, 'boolean', 'Email notifications enabled'],
            'sms_notifications' => [false, 'boolean', 'SMS notifications enabled'],
            'booking_advance_days' => [30, 'integer', 'Days in advance bookings allowed'],
            'booking_cancellation_hours' => [24, 'integer', 'Hours before booking cancellation allowed'],
            'auto_approve_bookings' => [false, 'boolean', 'Auto approve bookings'],
            'payment_gateway' => ['manual', 'string', 'Payment gateway'],
            'social_facebook' => ['', 'string', 'Facebook page URL'],
            'social_twitter' => ['', 'string', 'Twitter profile URL'],
            'social_instagram' => ['', 'string', 'Instagram profile URL'],
            'social_youtube' => ['', 'string', 'YouTube channel URL'],
        ];

        foreach ($defaults as $key => $data) {
            $this->setSetting($key, $data[0], $data[1], $data[2], true);
        }
    }
}
