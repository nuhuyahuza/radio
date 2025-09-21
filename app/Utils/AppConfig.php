<?php

namespace App\Utils;

use App\Models\Settings;

/**
 * Application Configuration Helper
 * Provides easy access to application settings
 */
class AppConfig
{
    private static $settings = null;
    private static $settingsModel = null;

    /**
     * Initialize settings
     */
    private static function initialize()
    {
        if (self::$settings === null) {
            self::$settingsModel = new Settings();
            self::$settings = self::$settingsModel->getAppConfig();
        }
    }

    /**
     * Get setting value
     */
    public static function get($key, $default = null)
    {
        self::initialize();
        return self::$settings[$key] ?? $default;
    }

    /**
     * Get application name
     */
    public static function getAppName()
    {
        return self::get('app_name', 'Zaa Radio');
    }

    /**
     * Get application description
     */
    public static function getAppDescription()
    {
        return self::get('app_description', 'Radio Station Booking System');
    }

    /**
     * Get application logo
     */
    public static function getAppLogo()
    {
        return self::get('app_logo', '/assets/images/logo.png');
    }

    /**
     * Get contact email
     */
    public static function getContactEmail()
    {
        return self::get('contact_email', 'admin@zaaradio.com');
    }

    /**
     * Get contact phone
     */
    public static function getContactPhone()
    {
        return self::get('contact_phone', '+233 123 456 789');
    }

    /**
     * Get contact address
     */
    public static function getContactAddress()
    {
        return self::get('contact_address', 'Accra, Ghana');
    }

    /**
     * Get currency symbol
     */
    public static function getCurrencySymbol()
    {
        return self::get('currency_symbol', 'GH₵');
    }

    /**
     * Get currency code
     */
    public static function getCurrency()
    {
        return self::get('currency', 'GHS');
    }

    /**
     * Get timezone
     */
    public static function getTimezone()
    {
        return self::get('timezone', 'Africa/Accra');
    }

    /**
     * Get date format
     */
    public static function getDateFormat()
    {
        return self::get('date_format', 'Y-m-d');
    }

    /**
     * Get time format
     */
    public static function getTimeFormat()
    {
        return self::get('time_format', 'H:i');
    }

    /**
     * Get items per page
     */
    public static function getItemsPerPage()
    {
        return self::get('items_per_page', 10);
    }

    /**
     * Check if maintenance mode is enabled
     */
    public static function isMaintenanceMode()
    {
        return self::get('maintenance_mode', false);
    }

    /**
     * Check if registration is enabled
     */
    public static function isRegistrationEnabled()
    {
        return self::get('registration_enabled', true);
    }

    /**
     * Check if email notifications are enabled
     */
    public static function isEmailNotificationsEnabled()
    {
        return self::get('email_notifications', true);
    }

    /**
     * Check if SMS notifications are enabled
     */
    public static function isSmsNotificationsEnabled()
    {
        return self::get('sms_notifications', false);
    }

    /**
     * Get booking advance days
     */
    public static function getBookingAdvanceDays()
    {
        return self::get('booking_advance_days', 30);
    }

    /**
     * Get booking cancellation hours
     */
    public static function getBookingCancellationHours()
    {
        return self::get('booking_cancellation_hours', 24);
    }

    /**
     * Check if auto approve bookings is enabled
     */
    public static function isAutoApproveBookings()
    {
        return self::get('auto_approve_bookings', false);
    }

    /**
     * Get payment gateway
     */
    public static function getPaymentGateway()
    {
        return self::get('payment_gateway', 'manual');
    }

    /**
     * Get social media URLs
     */
    public static function getSocialMedia()
    {
        return [
            'facebook' => self::get('social_facebook', ''),
            'twitter' => self::get('social_twitter', ''),
            'instagram' => self::get('social_instagram', ''),
            'youtube' => self::get('social_youtube', '')
        ];
    }

    /**
     * Format currency amount
     */
    public static function formatCurrency($amount)
    {
        return self::getCurrencySymbol() . number_format($amount, 2);
    }

    /**
     * Format date
     */
    public static function formatDate($date, $format = null)
    {
        $format = $format ?: self::getDateFormat();
        return date($format, is_string($date) ? strtotime($date) : $date);
    }

    /**
     * Format time
     */
    public static function formatTime($time, $format = null)
    {
        $format = $format ?: self::getTimeFormat();
        return date($format, is_string($time) ? strtotime($time) : $time);
    }

    /**
     * Format date and time
     */
    public static function formatDateTime($datetime, $dateFormat = null, $timeFormat = null)
    {
        $dateFormat = $dateFormat ?: self::getDateFormat();
        $timeFormat = $timeFormat ?: self::getTimeFormat();
        return date($dateFormat . ' ' . $timeFormat, is_string($datetime) ? strtotime($datetime) : $datetime);
    }

    /**
     * Clear cached settings (call after updating settings)
     */
    public static function clearCache()
    {
        self::$settings = null;
    }
}
