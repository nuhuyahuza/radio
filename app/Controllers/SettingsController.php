<?php

namespace App\Controllers;

use App\Models\Settings;
use App\Utils\Session;
use App\Middleware\AuthMiddleware;

/**
 * Settings Controller
 * Handles application settings management
 */
class SettingsController
{
    private $settingsModel;

    public function __construct()
    {
        $this->settingsModel = new Settings();
    }

    /**
     * Show settings page
     */
    public function showSettings()
    {
        AuthMiddleware::requireRole('admin');
        
        $currentUser = Session::getUser();
        $settings = $this->settingsModel->getAllSettings();
        
        // Group settings by category
        $groupedSettings = $this->groupSettingsByCategory($settings);
        
        include __DIR__ . '/../../public/views/admin/settings.php';
    }

    /**
     * Update settings
     */
    public function updateSettings()
    {
        AuthMiddleware::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/settings');
            exit;
        }

        // CSRF protection
        if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Invalid security token.');
            header('Location: /admin/settings');
            exit;
        }

        try {
            $updated = 0;
            $errors = [];

            // Process each setting
            foreach ($_POST as $key => $value) {
                if ($key === 'csrf_token') continue;
                
                // Get setting info
                $sql = "SELECT * FROM settings WHERE `key` = ? LIMIT 1";
                $setting = $this->settingsModel->db->fetch($sql, [$key]);
                if (!$setting) continue;

                // Validate based on type
                $validatedValue = $this->validateSettingValue($value, $setting['type']);
                if ($validatedValue === false) {
                    $errors[] = "Invalid value for {$setting['description']}";
                    continue;
                }

                // Update setting
                if ($this->settingsModel->setSetting($key, $validatedValue, $setting['type'], $setting['description'], $setting['is_public'])) {
                    $updated++;
                }
            }

            if (!empty($errors)) {
                Session::setFlash('error', implode(', ', $errors));
            } else {
                Session::setFlash('success', "Settings updated successfully. {$updated} settings changed.");
                // Clear AppConfig cache
                \App\Utils\AppConfig::clearCache();
            }

            // Log activity
            AuthMiddleware::logActivity('settings_updated', "Settings updated by admin");

        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to update settings: ' . $e->getMessage());
            error_log("Settings Update Error: " . $e->getMessage());
        }

        header('Location: /admin/settings');
        exit;
    }

    /**
     * Reset settings to defaults
     */
    public function resetSettings()
    {
        AuthMiddleware::requireRole('admin');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/settings');
            exit;
        }

        // CSRF protection
        if (!Session::verifyCsrfToken($_POST['csrf_token'] ?? '')) {
            Session::setFlash('error', 'Invalid security token.');
            header('Location: /admin/settings');
            exit;
        }

        try {
            $this->settingsModel->initializeDefaults();
            
            // Clear AppConfig cache
            \App\Utils\AppConfig::clearCache();
            
            // Log activity
            AuthMiddleware::logActivity('settings_reset', "Settings reset to defaults by admin");
            
            Session::setFlash('success', 'Settings have been reset to default values.');
        } catch (\Exception $e) {
            Session::setFlash('error', 'Failed to reset settings: ' . $e->getMessage());
            error_log("Settings Reset Error: " . $e->getMessage());
        }

        header('Location: /admin/settings');
        exit;
    }

    /**
     * Get settings data for API
     */
    public function getSettingsData()
    {
        AuthMiddleware::requireRole('admin');
        
        header('Content-Type: application/json');
        
        try {
            $settings = $this->settingsModel->getAllSettings();
            $groupedSettings = $this->groupSettingsByCategory($settings);
            
            echo json_encode([
                'success' => true,
                'data' => $groupedSettings
            ]);
        } catch (\Exception $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => 'Failed to fetch settings data',
                'message' => $e->getMessage()
            ]);
        }
    }

    /**
     * Group settings by category
     */
    private function groupSettingsByCategory($settings)
    {
        $categories = [
            'general' => [
                'title' => 'General Settings',
                'icon' => 'fas fa-cog',
                'settings' => []
            ],
            'appearance' => [
                'title' => 'Appearance',
                'icon' => 'fas fa-palette',
                'settings' => []
            ],
            'contact' => [
                'title' => 'Contact Information',
                'icon' => 'fas fa-address-book',
                'settings' => []
            ],
            'booking' => [
                'title' => 'Booking Settings',
                'icon' => 'fas fa-calendar-check',
                'settings' => []
            ],
            'notifications' => [
                'title' => 'Notifications',
                'icon' => 'fas fa-bell',
                'settings' => []
            ],
            'social' => [
                'title' => 'Social Media',
                'icon' => 'fas fa-share-alt',
                'settings' => []
            ],
            'system' => [
                'title' => 'System Settings',
                'icon' => 'fas fa-server',
                'settings' => []
            ]
        ];

        // Categorize settings
        foreach ($settings as $setting) {
            $key = $setting['key'];
            
            if (strpos($key, 'app_') === 0) {
                $categories['appearance']['settings'][] = $setting;
            } elseif (strpos($key, 'contact_') === 0) {
                $categories['contact']['settings'][] = $setting;
            } elseif (strpos($key, 'booking_') === 0 || strpos($key, 'auto_approve') === 0) {
                $categories['booking']['settings'][] = $setting;
            } elseif (strpos($key, 'notification') === 0 || strpos($key, 'email_') === 0 || strpos($key, 'sms_') === 0) {
                $categories['notifications']['settings'][] = $setting;
            } elseif (strpos($key, 'social_') === 0) {
                $categories['social']['settings'][] = $setting;
            } elseif (in_array($key, ['maintenance_mode', 'registration_enabled', 'payment_gateway'])) {
                $categories['system']['settings'][] = $setting;
            } else {
                $categories['general']['settings'][] = $setting;
            }
        }

        // Remove empty categories
        return array_filter($categories, function($category) {
            return !empty($category['settings']);
        });
    }

    /**
     * Validate setting value based on type
     */
    private function validateSettingValue($value, $type)
    {
        switch ($type) {
            case 'boolean':
                return filter_var($value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);
            case 'integer':
                return filter_var($value, FILTER_VALIDATE_INT);
            case 'float':
                return filter_var($value, FILTER_VALIDATE_FLOAT);
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL);
            case 'url':
                return filter_var($value, FILTER_VALIDATE_URL);
            case 'json':
                json_decode($value);
                return json_last_error() === JSON_ERROR_NONE ? $value : false;
            default:
                return htmlspecialchars(trim($value), ENT_QUOTES, 'UTF-8');
        }
    }
}
