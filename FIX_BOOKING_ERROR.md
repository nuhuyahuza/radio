# Fix Booking Error - Database Schema Issue

## 🔴 Problem
Bookings are failing because the `bookings` table has `slot_id INT NOT NULL`, but campaign bookings (Jingles, LPMs, Talkshows) don't use traditional slots - they use the `booking_sessions` table instead.

## ✅ Solution
Run the corrected database migration that makes `slot_id` nullable.

---

## Quick Fix (3 Steps)

### Step 1: Open phpMyAdmin
1. Go to: `http://localhost/phpmyadmin`
2. Click on `zaa_radio` database in the left sidebar

### Step 2: Run This SQL
1. Click the **"SQL"** tab at the top
2. **Copy and paste** the SQL below:

```sql
-- Step 1: Make slot_id nullable (campaign bookings don't use slots)
ALTER TABLE bookings 
  MODIFY COLUMN slot_id INT DEFAULT NULL;

-- Step 2: Add campaign fields
ALTER TABLE bookings ADD COLUMN ad_type ENUM('jingle','lpm','talkshow') DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN recurrence_pattern VARCHAR(32) DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN campaign_start DATE DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN campaign_end DATE DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN frequency_per_day INT DEFAULT NULL;
ALTER TABLE bookings ADD COLUMN weekdays VARCHAR(32) DEFAULT NULL;

-- Step 3: Create sessions table
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

3. Click the **"Go"** button

### Step 3: Verify Success
You should see messages like:
- ✅ `1 row affected` (for ALTER TABLE slot_id)
- ✅ `6 rows affected` (for adding new columns)
- ✅ `Table 'booking_sessions' created successfully`

---

## Test the Fix

1. **Go to**: `http://localhost/radio/book`
2. **Click on any date** on the calendar
3. **Select "Jingle"** card
4. **Fill in the form**:
   - Start Date: Today
   - End Date: 7 days from now
   - Duration: 60 seconds
   - Times: Select 8:00 AM and 5:00 PM
5. **Click "Next: Preview Schedule"**
6. **You should see**: 14 sessions listed
7. **Fill in advertiser info**
8. **Click "Confirm Booking"**
9. **Expected**: ✅ Success! "14 sessions created"

---

## What Changed?

### Before (NOT NULL - Failed)
```sql
slot_id INT NOT NULL,  -- ❌ Campaign bookings can't set this
```

### After (NULLABLE - Works)
```sql
slot_id INT DEFAULT NULL,  -- ✅ Campaign bookings use NULL
```

### How It Works Now

**Traditional Single-Slot Bookings:**
- `slot_id` = 123 (points to slots table)
- `ad_type` = NULL
- Uses existing booking flow

**Campaign Bookings (Jingles/LPMs/Talkshows):**
- `slot_id` = NULL (doesn't use slots table)
- `ad_type` = 'jingle', 'lpm', or 'talkshow'
- Sessions stored in `booking_sessions` table
- Multiple sessions linked to one parent booking

---

## Common Errors & Fixes

### Error: "Column 'slot_id' cannot be null"
**Cause:** Migration not run yet  
**Fix:** Run the SQL above in phpMyAdmin

### Error: "Table 'booking_sessions' doesn't exist"
**Cause:** Migration Step 3 failed  
**Fix:** Run Step 3 of the SQL separately

### Error: "Duplicate column name 'ad_type'"
**Cause:** You already ran the migration partially  
**Fix:** Skip to Step 3 (CREATE TABLE) only

### Error: "Cannot add foreign key constraint"
**Cause:** `bookings` table might have invalid data  
**Fix:** Check if all advertiser_id values exist in users table

---

## Verify Database Structure

After migration, your `bookings` table should have these columns:

```
✅ id (INT, PRIMARY KEY)
✅ advertiser_id (INT, NOT NULL)
✅ slot_id (INT, DEFAULT NULL)  ← Now nullable!
✅ ad_type (ENUM, NULL)  ← New!
✅ recurrence_pattern (VARCHAR, NULL)  ← New!
✅ campaign_start (DATE, NULL)  ← New!
✅ campaign_end (DATE, NULL)  ← New!
✅ frequency_per_day (INT, NULL)  ← New!
✅ weekdays (VARCHAR, NULL)  ← New!
✅ status (ENUM)
✅ message (TEXT)
✅ total_amount (DECIMAL)
... (other existing columns)
```

And a new table `booking_sessions`:
```
✅ id (INT, PRIMARY KEY)
✅ booking_id (INT, NOT NULL, FOREIGN KEY → bookings.id)
✅ session_date (DATE, NOT NULL)
✅ start_time (TIME, NOT NULL)
✅ end_time (TIME, NOT NULL)
✅ status (ENUM, NOT NULL)
✅ created_at (DATETIME)
✅ updated_at (DATETIME)
```

---

## Alternative: Use SQL File

If copy-paste doesn't work:

1. **Open file**: `RUN_THIS_MIGRATION.sql` (in project root)
2. **In phpMyAdmin**: Click "Import" tab
3. **Choose file**: Browse to `RUN_THIS_MIGRATION.sql`
4. **Click "Go"**

---

## Still Having Issues?

### Check Browser Console (F12)
Look for errors when confirming booking:
```javascript
// Good response:
{success: true, parent_booking_id: 123, session_count: 14}

// Bad response:
{success: false, message: "Error message here"}
```

### Check PHP Error Log
Location: `C:\xampp\php\logs\php_error_log`

Look for:
- Database connection errors
- SQL syntax errors
- Constraint violations

### Verify API Endpoint
Visit: `http://localhost/radio/api/slots/check-availability`

Should show JSON response (might be empty, that's OK)

---

## Success Checklist

- [ ] phpMyAdmin shows `slot_id` as NULL allowed
- [ ] phpMyAdmin shows 6 new columns (ad_type, etc.)
- [ ] phpMyAdmin shows `booking_sessions` table exists
- [ ] Calendar loads without errors
- [ ] Clicking on date opens modal
- [ ] Can select ad type (Jingle/LPM/Talkshow)
- [ ] Preview shows available sessions
- [ ] Booking confirmation works
- [ ] Success message shows session count

Once all checked, you're ready to go! 🚀

---

**Last Updated:** October 27, 2025  
**Issue:** Database schema compatibility  
**Status:** ✅ Fixed with corrected migration

