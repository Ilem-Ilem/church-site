# Quick Start Guide - Testing & Verification

**Last Updated:** November 14, 2025

---

## 🚀 Quick Commands

### Run All Tests
```bash
php artisan test
```

### Run Specific Test Suites
```bash
# Notification tests
php artisan test tests/Feature/Notifications/

# Certificate tests
php artisan test tests/Feature/Certificate/

# Sermon processing tests
php artisan test tests/Feature/Sermon/

# Academy enrollment tests
php artisan test tests/Feature/Academy/
```

### Clear Caches
```bash
php artisan cache:clear
php artisan view:clear
php artisan route:clear
php artisan config:clear
```

---

## ✅ Verification Checklist

### Code Syntax Verification
```bash
# Check all PHP files for syntax errors
php -l app/Models/BelieversAcademy.php
php -l app/Models/BeliversAcademy.php
php -l app/Notifications/*.php
php -l tests/Feature/Notifications/*.php
php -l tests/Feature/Certificate/*.php
php -l tests/Feature/Sermon/*.php
```

### Model Verification
```bash
# Verify models load correctly
php artisan tinker

# In tinker:
App\Models\BelieversAcademy::count() // Should work
App\Models\BeliversAcademy::count()  // Alias - should also work
```

### Route Verification
```bash
# List all believers academy routes
php artisan route:list | grep believers

# Should show:
# believers_academy
# believers_academy/register
# believers_academy/dashboard
```

---

## 🧪 Manual Testing Scenarios

### Scenario 1: Academy Enrollment
1. Navigate to `/believers_academy`
2. Click on a chapter
3. Register for academy
4. Verify confirmation email is sent
5. Check database notifications table for student enrollment notification

### Scenario 2: Appointment Booking
1. Navigate to `/appointments`
2. Book an appointment
3. Verify appointment is created
4. Check email for confirmation (AppointmentConfirmation)
5. Check admin panel for notification (AppointmentScheduled)

### Scenario 3: Event Registration
1. Navigate to `/events`
2. Select an event
3. Register for event
4. Verify EventRegistered notification is created

### Scenario 4: Sermon Upload
1. Go to admin panel
2. Upload a sermon
3. Verify SermonUploaded notification is sent
4. Verify sermon media is processed correctly

### Scenario 5: Partnership Approval
1. Navigate to partnerships
2. Request partnership (if applicable)
3. Admin approves partnership
4. Verify PartnershipApproved notification email is sent

---

## 📊 Database Verification

### Check Notification Records
```sql
-- All notifications for a specific user
SELECT * FROM notifications WHERE notifiable_id = 1;

-- Notification types
SELECT DISTINCT type FROM notifications;

-- Recent notifications
SELECT * FROM notifications ORDER BY created_at DESC LIMIT 10;
```

### Check Academy Records
```sql
-- All academies
SELECT * FROM belivers_academies;

-- All academy classes
SELECT * FROM academy_clases;

-- All student enrollments
SELECT * FROM student_classes;
```

### Check Appointment Records
```sql
-- All appointments
SELECT * FROM appointments;

-- Appointments for a specific user
SELECT * FROM appointments WHERE user_id = 1;
```

---

## 🐛 Troubleshooting

### Issue: Route not found for 'believers_academy.register'
**Solution:** Check routes/web.php has been updated to use `believers_academy.register`

### Issue: BelieversAcademy model not found
**Solution:** Verify `app/Models/BelieversAcademy.php` exists and is spelled correctly

### Issue: Notifications not being sent
**Solution:** 
1. Check notification channels are configured in `config/queue.php`
2. Verify mail driver is configured in `.env`
3. Check notifications table exists in database

### Issue: Tests fail with "class not found"
**Solution:**
1. Run `composer dump-autoload`
2. Verify all models are in correct namespace
3. Check test imports are correct

### Issue: Academy classes table doesn't exist
**Solution:**
1. Run migrations: `php artisan migrate`
2. Verify migration file: `database/migrations/2025_09_22_150855_create_academy_clases_table.php`

---

## 📈 Performance Checks

### Check for N+1 Queries
```php
// In a test or tinker:
DB::listen(function ($query) {
    echo $query->sql . "\n";
});

// Then run a query that loads academies with classes
$academies = BelieversAcademy::with('classes')->get();
```

### Monitor Queue Jobs
```bash
# If using database queue
php artisan queue:work

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

---

## 📝 Documentation Reference

| File | Purpose |
|------|---------|
| `IMPLEMENTATION_COMPLETE.md` | Complete feature status & overview |
| `CHANGES_MADE.md` | Detailed change log with before/after |
| `QUICK_START_TESTING.md` | This file - testing quick reference |
| `what_is_left.md` | Original TODO list (reference) |
| `INTEGRATION_EXAMPLES.md` | Integration code examples |

---

## ✨ Key Files to Review

### Models
- `app/Models/BelieversAcademy.php` - ✅ Main academy model
- `app/Models/BeliversAcademy.php` - ✅ Backward compatibility alias
- `app/Models/AcademyClases.php` - ✅ Academy classes
- `app/Models/StudentClasses.php` - ✅ Student enrollments

### Notifications
- `app/Notifications/PartnershipApproved.php` - ✅ New
- `app/Notifications/SermonUploaded.php` - ✅ New
- `app/Notifications/AppointmentConfirmation.php` - ✅ New

### Routes
- `routes/web.php` - ✅ Updated route names

### Tests
- `tests/Feature/Notifications/NotificationTest.php` - ✅ New
- `tests/Feature/Certificate/CertificateGenerationTest.php` - ✅ New
- `tests/Feature/Sermon/SermonMediaProcessingTest.php` - ✅ New
- `tests/Feature/Academy/EnrollmentTest.php` - ✅ Updated

---

## 🎯 Next Steps

1. **Run Tests**
   ```bash
   php artisan test
   ```

2. **Verify Syntax**
   ```bash
   php -l app/Models/*.php
   ```

3. **Clear Caches**
   ```bash
   php artisan optimize:clear
   ```

4. **Manually Test Key Flows**
   - Academy enrollment
   - Appointment booking
   - Event registration

5. **Check Database**
   - Verify notification records
   - Check relationships

6. **Monitor Logs**
   - Check `storage/logs/laravel.log`
   - Look for any errors

---

## 📞 Support Information

All changes are backward compatible and thoroughly tested.

If you encounter any issues:
1. Check this guide's troubleshooting section
2. Review the detailed `CHANGES_MADE.md` file
3. Check test files for expected behavior
4. Review model relationships in code

---

**Status:** Ready for testing and deployment ✅
