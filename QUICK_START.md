# Quick Start Guide - Multi-Ad-Type Booking System

## ⚡ Get Started in 3 Steps

### Step 1: Run Database Migration

#### Using phpMyAdmin (Easiest)
1. Open http://localhost/phpmyadmin
2. Select `zaa_radio` database
3. Click "SQL" tab
4. Copy the SQL below and paste it
5. Click "Go"

```sql
-- Add ad_type and campaign fields to bookings table
ALTER TABLE bookings
  ADD COLUMN ad_type ENUM('jingle','lpm','talkshow') DEFAULT NULL AFTER slot_id,
  ADD COLUMN recurrence_pattern VARCHAR(32) DEFAULT NULL AFTER ad_type,
  ADD COLUMN campaign_start DATE DEFAULT NULL AFTER recurrence_pattern,
  ADD COLUMN campaign_end DATE DEFAULT NULL AFTER campaign_start,
  ADD COLUMN frequency_per_day INT DEFAULT NULL AFTER campaign_end,
  ADD COLUMN weekdays VARCHAR(32) DEFAULT NULL AFTER frequency_per_day;

-- Create booking_sessions table
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
```

✅ **Done!** Your database is now ready.

### Step 2: Test the Booking Flow

1. **Open your browser** and navigate to:
   ```
   http://localhost/radio/book
   ```

2. **Try a Jingle Campaign:**
   - Click the green "Jingle" card
   - Set campaign dates (e.g., today to 7 days from now)
   - Duration: 60 seconds
   - Select times: 8:00 AM and 5:00 PM
   - Click "Next: Preview Schedule"
   - You should see: **14 sessions** (7 days × 2 times)
   - Fill in your details
   - Click "Confirm Booking"

3. **Check the Calendar:**
   - You should see **green events** on the calendar for your jingle sessions
   - Each session appears on its respective date and time

### Step 3: View in Dashboard

1. **Login** with the advertiser credentials you just used
2. **Navigate to Dashboard**: `/advertiser`
3. **You should see:**
   - A green "Jingle" badge next to your booking
   - "14 sessions" displayed
   - Campaign date range
   - Total amount

## 🎨 Visual Guide

### Ad Type Colors
- 🟢 **Jingles** = Green (#28a745)
- 🔵 **LPMs** = Blue (#007bff)
- 🟠 **Talkshows** = Orange (#fd7e14)

### Booking Types Explained

#### 🎵 Jingles
- **Duration**: 30 seconds to 3 minutes
- **Frequency**: Multiple times per day
- **Best for**: Short adverts, brand jingles, promotions
- **Example**: Play at 8 AM and 5 PM every day for 2 weeks

#### 🎤 Live Presenter Mentions (LPMs)
- **Duration**: Continuous time window (e.g., 2 PM to 5 PM)
- **Frequency**: Daily or specific weekdays
- **Best for**: Live show sponsorships, in-show mentions
- **Example**: Mention during afternoon show, Monday-Friday for 1 month

#### 💬 Talkshows
- **Duration**: 30 minutes to 3 hours
- **Frequency**: One-time or weekly recurring
- **Best for**: Dedicated programs, interviews, special shows
- **Example**: Every Saturday 8-10 AM for 4 weeks

## 📋 Testing Checklist

- [ ] Database migration completed successfully
- [ ] Can access `/book` page without errors
- [ ] Jingle booking creates correct number of sessions
- [ ] LPM booking respects weekday selection
- [ ] Talkshow booking handles weekly recurrence
- [ ] Calendar shows color-coded events
- [ ] Availability checking prevents double-booking
- [ ] Email confirmation received (check spam folder)
- [ ] Dashboard displays ad type badges
- [ ] Session counts are accurate

## 🔧 Troubleshooting

### Issue: "Table 'bookings' doesn't have column 'ad_type'"
**Solution:** Run the database migration in Step 1

### Issue: "Table 'booking_sessions' doesn't exist"
**Solution:** Run the database migration in Step 1

### Issue: Booking form doesn't show ad type options
**Solution:** 
1. Clear browser cache (Ctrl+Shift+Del)
2. Hard refresh the page (Ctrl+F5)
3. Check browser console for JavaScript errors

### Issue: Calendar shows no color coding
**Solution:**
1. Ensure database migration is complete
2. Check `/api/slots` endpoint returns data
3. Clear browser cache

### Issue: Email not received
**Solution:**
1. Check if email service is configured in `app/Utils/Email/EmailService.php`
2. Check spam/junk folder
3. For testing, view notifications in database table `notifications`

## 📞 Need Help?

Check the full documentation in `IMPLEMENTATION_SUMMARY.md` for:
- Detailed feature list
- Technical implementation details
- API documentation
- Code examples

## 🚀 What's Next?

Once basic testing is complete, you can:
1. **Customize pricing** based on ad type in `BookingController.php`
2. **Add payment gateway** integration
3. **Create admin approval workflow** for campaigns
4. **Generate PDF contracts** for multi-session bookings
5. **Add SMS notifications** for session reminders
6. **Build analytics dashboards** for campaign performance

---

**System Status**: ✅ Ready to use  
**Last Updated**: October 27, 2025

