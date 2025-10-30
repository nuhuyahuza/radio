-- =====================================================
-- IMPORTANT: Run this SQL in phpMyAdmin
-- Database Migration for Campaign Bookings Support
-- (Jingles, LPMs, Talkshows)
-- =====================================================

-- Step 1: Make slot_id nullable 
-- (Campaign bookings use booking_sessions instead of traditional slots)
ALTER TABLE bookings 
  MODIFY COLUMN slot_id INT DEFAULT NULL;

-- Step 2: Add ad_type and campaign management columns
-- Run these one at a time to handle if columns already exist
ALTER TABLE bookings ADD COLUMN ad_type ENUM('jingle','lpm','talkshow') DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN recurrence_pattern VARCHAR(32) DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN campaign_start DATE DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN campaign_end DATE DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN frequency_per_day INT DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN weekdays VARCHAR(32) DEFAULT NULL;

-- Step 3: Create booking_sessions table for multi-session campaigns
CREATE TABLE IF NOT EXISTS booking_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  session_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  status ENUM('pending','approved','cancelled','completed') NOT NULL DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  INDEX idx_booking_session (booking_id, session_date),
  INDEX idx_session_date (session_date, start_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Done! You should see:
-- - 3 ALTER TABLE statements executed successfully
-- - 1 CREATE TABLE statement executed successfully

