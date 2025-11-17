# Email Notifications Setup Guide

## Log-Based Email Catching (Development - Laravel 12)

Your application is now configured to log all emails to files for development/testing purposes.

### Configuration

- **Mailer**: Log-based (configured in `.env` as `MAIL_MAILER=log`)
- **Email Storage**: `storage/logs/laravel.log`
- **Config**: `config/mail.php` has the log mailer configured with 'single' channel

### How It Works

When notifications are sent, emails are logged to `storage/logs/laravel.log` instead of being sent to actual email servers. Each email appears as a formatted HTML log entry with full content.

### Email Log Location

```
storage/logs/laravel.log
```

View emails:
```bash
# View all emails
tail -500 storage/logs/laravel.log

# Search for specific notifications
grep -i "welcome\|enrollment\|completed" storage/logs/laravel.log
```

---

## Notification Classes Implemented

### 1. **UserRegisteredToAcademy**
**Triggered when**: A student registers for an academy
**Notified**: Academy team lead
**File**: `app/Notifications/UserRegisteredToAcademy.php`
**Channels**: Mail + Database

**Usage**:
```php
use App\Notifications\UserRegisteredToAcademy;

$teamLead = $academy->believersAcademyTeam()->team->leader->user;
$teamLead->notify(new UserRegisteredToAcademy($student, $academy));
```

### 2. **ClassCompletedByStudent**
**Triggered when**: Team lead marks a class as complete
**Notified**: Student
**File**: `app/Notifications/ClassCompletedByStudent.php`
**Channels**: Mail + Database

**Usage**:
```php
use App\Notifications\ClassCompletedByStudent;

$student->notify(new ClassCompletedByStudent($student, $class, 'completed'));
```

### 3. **StudentEnrolledNotification**
**Triggered when**: Student enrolls in academy
**Notified**: Student
**File**: `app/Notifications/StudentEnrolledNotification.php`
**Channels**: Mail + Database

**Usage**:
```php
use App\Notifications\StudentEnrolledNotification;

$student->notify(new StudentEnrolledNotification($academy));
```

### 4. **PrayerRequestSubmitted**
**Triggered when**: Prayer request is submitted
**Notified**: Prayer request team
**File**: `app/Notifications/PrayerRequestSubmitted.php`
**Channels**: Mail + Database

**Usage**:
```php
use App\Notifications\PrayerRequestSubmitted;

$teamLead->notify(new PrayerRequestSubmitted($prayerRequest));
```

### 5. **EventRegistered**
**Triggered when**: User registers for event
**Notified**: Event organizer/team lead
**File**: `app/Notifications/EventRegistered.php`
**Channels**: Mail + Database

**Usage**:
```php
use App\Notifications\EventRegistered;

$eventOrganizer->notify(new EventRegistered($event, $registrant));
```

---

## Team Lead Notification Logic

### How Team Leads Are Notified

The system uses an event-based approach:

1. **StudentRegisteredToAcademy Event** (`app/Events/StudentRegisteredToAcademy.php`)
   - Fired when a student registers for academy
   - Triggers `NotifyTeamLeadOfRegistration` listener

2. **NotifyTeamLeadOfRegistration Listener** (`app/Listeners/NotifyTeamLeadOfRegistration.php`)
   - Finds the academy's team
   - Gets the team lead (user with `role_in_team = 'team_lead'`)
   - Sends notification

### Usage in Your Code

When a student registers, dispatch the event:

```php
use App\Events\StudentRegisteredToAcademy;

$studentClass = StudentClasses::create([
    'user_id' => $student->id,
    'academy_id' => $academy->id,
    'status' => 'started',
    'class_completed' => json_encode([]),
]);

StudentRegisteredToAcademy::dispatch($student, $academy, $studentClass);
```

---

## Database Notifications

All notifications are also stored in the `notifications` table for in-app notification center.

**Database Notification Structure**:
```php
[
    'type' => 'academy_registration', // notification type
    'student_id' => 1,
    'academy_name' => 'Bible Study',
    'message' => 'New student John has registered for Bible Study',
    // ... other fields
]
```

### Retrieve Notifications for User

```php
$user->notifications; // All notifications
$user->unreadNotifications; // Unread only
$user->notifications()->whereType('academy_registration')->get(); // Filter by type
```

### Mark as Read

```php
$user->notifications->markAsRead();
$notification->markAsRead();
```

---

## Testing Email Notifications

### Check Email Logs

```bash
# View last 500 lines of email logs
tail -500 storage/logs/laravel.log

# Search for specific email types
grep -i "welcome" storage/logs/laravel.log
grep -i "registration" storage/logs/laravel.log
grep -i "completed" storage/logs/laravel.log

# View full email (pretty print HTML)
tail -1000 storage/logs/laravel.log | less
```

### Using Tinker

```bash
php artisan tinker

# Send test notification
>>> $user = App\Models\User::first();
>>> $academy = App\Models\BeliversAcademy::first();
>>> $user->notify(new App\Notifications\StudentEnrolledNotification($academy));
>>> exit

# Check the log
$ tail -200 storage/logs/laravel.log
```

---

## Production Emails

For production, change `MAIL_MAILER` in `.env`:

```env
MAIL_MAILER=smtp  # or: sendmail, mailgun, ses, postmark, etc.
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=465
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
```

---

## Queue Support

All notifications implement `ShouldQueue` for async processing:

```php
// Notifications are queued if QUEUE_CONNECTION=database
php artisan queue:work
```

Configure in `.env`:
```env
QUEUE_CONNECTION=database  # or: redis, sync
```

---

## Summary

✅ File-based email catching configured
✅ 5 notification classes created with mail + database channels
✅ Event-based team lead notifications
✅ Queue support for async processing
✅ Database storage for in-app notifications

Emails are now saved to `storage/app/mail/` for testing!
