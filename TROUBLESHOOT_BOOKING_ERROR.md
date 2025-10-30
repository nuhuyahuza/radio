# 🔧 Troubleshoot Booking Error - Step by Step

## Step 1: Check Migration Status

**Visit this URL in your browser:**
```
http://localhost/radio/check-migration.php
```

This will show you **exactly** what's missing in your database.

### What You'll See:

#### ✅ If Migration Complete:
```
✓✓✓ ALL MIGRATIONS COMPLETED! System is ready to use. ✓✓✓
```
→ Go to Step 2

#### ❌ If Migration Needed:
You'll see RED text showing what's missing and the **exact SQL to run**.

**Do this:**
1. Copy the SQL shown on the page
2. Open phpMyAdmin: `http://localhost/phpmyadmin`
3. Select `zaa_radio` database
4. Click "SQL" tab
5. Paste the SQL
6. Click "Go"
7. Refresh the check-migration.php page
8. Should now show green ✓✓✓

---

## Step 2: Check Browser Console for Error

1. **Open your booking page**: `http://localhost/radio/book`
2. **Press F12** (opens Developer Tools)
3. **Click "Console" tab**
4. **Click "Network" tab** also
5. **Try to make a booking**
6. **Look for errors**

### Common Errors You Might See:

#### Error: "Column 'ad_type' doesn't exist"
**Fix:** Run the migration (see Step 1)

#### Error: "Table 'booking_sessions' doesn't exist"
**Fix:** Run the migration (see Step 1)

#### Error: "Column 'slot_id' cannot be null"
**Fix:** Run this SQL first:
```sql
ALTER TABLE bookings MODIFY COLUMN slot_id INT DEFAULT NULL;
```

#### Error: 500 Internal Server Error
**Check:** `C:\xampp\php\logs\php_error_log` for details

---

## Step 3: Check PHP Error Log

**Location:** `C:\xampp\php\logs\php_error_log`

**Open with:** Notepad or any text editor

**Look for lines with:** "Campaign booking error"

**Example error:**
```
Campaign booking error: SQLSTATE[42S22]: Column not found: 1054 Unknown column 'ad_type' in 'field list'
```

This tells you exactly what's wrong!

---

## Step 4: Test the Fix

After running migration:

1. **Refresh** the check-migration page
2. Should show all green ✓
3. **Go to booking page**: `/book`
4. **Click any date**
5. **Select "LPM"** (like your payload)
6. **Fill in:**
   - Start: Oct 28, 2025
   - End: Nov 20, 2025
   - Time: 8:30 PM to 9:30 PM
   - Recurrence: Every day
7. **Click "Next"**
8. **Should show 24 sessions** (matching your payload!)
9. **Fill advertiser info**
10. **Click "Confirm"**
11. **Should see:** ✅ "Your campaign has been booked with 24 sessions!"

---

## Quick Debug Checklist

Run through this list:

- [ ] Visited `check-migration.php` 
- [ ] All checks show green ✓
- [ ] Browser console shows no errors
- [ ] Network tab shows 200 OK (not 500)
- [ ] Response has `"success": true`
- [ ] Booking page refreshed (Ctrl+F5)
- [ ] Database has `booking_sessions` table
- [ ] Database `bookings.slot_id` is NULL allowed
- [ ] Database `bookings.ad_type` column exists

---

## Still Failing? Get Detailed Error

**Add this to your JavaScript console:**

```javascript
fetch('/booking/confirm', {
    method: 'POST',
    headers: {'Content-Type': 'application/json'},
    body: JSON.stringify({
        csrf_token: document.getElementById('csrf_token').value,
        ad_type: 'lpm',
        selected_slots: [{date: '2025-10-28', start_time: '20:30:00', end_time: '21:30:00'}],
        advertiser_name: 'Test User',
        advertiser_email: 'test@test.com',
        advertiser_phone: '1234567890',
        company_name: 'Test Co',
        message: 'Test',
        start_date: '2025-10-28',
        end_date: '2025-10-28',
        recurrence: 'daily',
        weekdays: []
    })
})
.then(r => r.json())
.then(d => console.log('Response:', d))
.catch(e => console.error('Error:', e));
```

This will show you the **exact error message** from the server.

---

## Expected Success Response

When it works, you should see:

```json
{
  "success": true,
  "parent_booking_id": 123,
  "session_count": 24,
  "redirect": "/"
}
```

---

## Need More Help?

**Include in your message:**
1. Screenshot of `check-migration.php` page
2. Error from browser console (F12 → Console tab)
3. Error from Network tab (F12 → Network tab → click failed request → Response)
4. Last 10 lines from `C:\xampp\php\logs\php_error_log`

This will help diagnose the exact issue!

---

**Quick Access Links:**
- 🔍 Check Migration: `http://localhost/radio/check-migration.php`
- 📊 phpMyAdmin: `http://localhost/phpmyadmin`
- 📝 Book Page: `http://localhost/radio/book`
- 📁 Error Log: `C:\xampp\php\logs\php_error_log`


