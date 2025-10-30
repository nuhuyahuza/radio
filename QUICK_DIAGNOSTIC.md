# 🚀 Quick Diagnostic - 3 Simple Steps

## ✅ Migration Complete! Now Let's Test Everything

### Step 1️⃣: System Health Check
Open in your browser:
```
http://localhost/radio/check-system.php
```

**What to look for:**
- All checks should say `"status": "OK"`
- If you see `"status": "ERROR"`, the JSON will show you the fix

### Step 2️⃣: Test Booking
Open in your browser:
```
http://localhost/radio/test-booking.php
```

**What to expect:**
- ✅ **SUCCESS**: You'll see `"success": true` with booking details
- ❌ **ERROR**: You'll see the exact error message and line number

### Step 3️⃣: Try Real Booking
1. Go to: `http://localhost/radio/book`
2. Fill in the booking form:
   - Select "LPM" as ad type
   - Pick today's date
   - Select 2 PM to 5 PM
   - Click "Check Availability"
3. Press **F12** to open Developer Tools
4. Go to **Console** tab
5. Click "Confirm Booking"

**If it fails:**
- Copy the error from Console
- Check the **Network** tab → Find the failed request → Click **Response**
- Copy that too

---

## 🎯 Most Likely Issues & Quick Fixes

### Issue: `slot_id cannot be NULL`
**Fix:** Run in phpMyAdmin:
```sql
ALTER TABLE bookings MODIFY COLUMN slot_id INT DEFAULT NULL;
```

### Issue: `Unknown column 'ad_type'`
**Fix:** You didn't run the full migration. Run in phpMyAdmin:
```sql
ALTER TABLE bookings ADD COLUMN ad_type ENUM('jingle','lpm','talkshow') DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN recurrence_pattern VARCHAR(32) DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN campaign_start DATE DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN campaign_end DATE DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN frequency_per_day INT DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN weekdays VARCHAR(32) DEFAULT NULL;
```

### Issue: `Table 'booking_sessions' doesn't exist`
**Fix:** Run in phpMyAdmin:
```sql
CREATE TABLE IF NOT EXISTS booking_sessions (
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  session_date DATE NOT NULL,
  start_time TIME NOT NULL,
  end_time TIME NOT NULL,
  status ENUM('pending','approved','rejected','cancelled') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
  INDEX idx_session_date (session_date),
  INDEX idx_booking_id (booking_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Issue: `Unknown column 'is_active' in users table`
**Fix:** Run in phpMyAdmin:
```sql
ALTER TABLE users ADD COLUMN is_active TINYINT(1) DEFAULT 1;
ALTER TABLE users ADD COLUMN email_verified_at DATETIME DEFAULT NULL;
```

---

## 📊 What Each Tool Does

| Tool | URL | Purpose |
|------|-----|---------|
| **System Check** | `/check-system.php` | Verifies database schema is correct |
| **Booking Test** | `/test-booking.php` | Tests the booking flow with sample data |
| **Real Booking** | `/book` | The actual booking page users will use |

---

## 💡 Pro Tips

1. **Always run System Check first** - it will tell you exactly what's missing
2. **Check PHP error log** at `C:\xampp\php\logs\php_error_log` for detailed errors
3. **Use Browser Console (F12)** to see JavaScript errors
4. **Check Network tab** to see API responses

---

## 📝 What's Been Fixed

✅ Database migration SQL corrected  
✅ Models synchronized with schema  
✅ Detailed error logging added  
✅ Email failures won't break bookings  
✅ Diagnostic tools created  

**Everything is ready to work once the database migration is confirmed!** 🎉


