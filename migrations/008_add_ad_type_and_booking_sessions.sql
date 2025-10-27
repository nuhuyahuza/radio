-- 008_add_ad_type_and_booking_sessions.sql

ALTER TABLE bookings
  ADD COLUMN ad_type ENUM('jingle','lpm','talkshow') NOT NULL DEFAULT 'jingle',
  ADD COLUMN recurrence_pattern VARCHAR(32) DEFAULT NULL,
  ADD COLUMN campaign_start DATE DEFAULT NULL,
  ADD COLUMN campaign_end DATE DEFAULT NULL,
  ADD COLUMN frequency_per_day INT DEFAULT NULL,
  ADD COLUMN weekdays VARCHAR(32) DEFAULT NULL;

CREATE TABLE IF NOT EXISTS booking_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  session_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  status ENUM('pending','approved','cancelled','completed') NOT NULL DEFAULT 'pending',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);