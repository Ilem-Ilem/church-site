# Email Notifications Integration Examples

## How to Use Notifications in Your Controllers

### 1. When Student Enrolls in Academy

**Scenario**: Student registers for believers academy → notify team lead

```php
<?php

namespace App\Http\Controllers;

use App\Events\StudentRegisteredToAcademy;
use App\Models\StudentClasses;
use App\Models\BeliversAcademy;
use App\Notifications\StudentEnrolledNotification;
use Illuminate\Http\Request;

class AcademyEnrollmentController extends Controller
{
    public function enroll(Request $request)
    {
        $student = auth()->user();
        $academy = BeliversAcademy::findOrFail($request->academy_id);

        // Create student class record
        $studentClass = StudentClasses::create([
            'user_id' => $student->id,
            'academy_id' => $academy->id,
            'status' => 'started',
            'class_completed' => json_encode([]),
        ]);

        // Notify student enrollment
        $student->notify(new StudentEnrolledNotification($academy));

        // Dispatch event to notify team lead
        StudentRegisteredToAcademy::dispatch($student, $academy, $studentClass);

        return response()->json(['message' => 'Enrolled successfully']);
    }
}
```

### 2. When Team Lead Marks Class Complete

**Scenario**: Team lead marks student class as complete → notify student

```php
<?php

namespace App\Http\Controllers;

use App\Models\AcademyClases;
use App\Models\StudentClasses;
use App\Notifications\ClassCompletedByStudent;
use Illuminate\Http\Request;

class ClassProgressController extends Controller
{
    public function markClassComplete(Request $request)
    {
        $class = AcademyClases::findOrFail($request->class_id);
        $studentClass = StudentClasses::findOrFail($request->student_class_id);

        // Update completion status
        $studentClass->update([
            'status' => 'completed',
            'class_completed' => json_encode(array_merge(
                json_decode($studentClass->class_completed, true) ?? [],
                [$class->id]
            )),
        ]);

        // Notify student
        $studentClass->user->notify(
            new ClassCompletedByStudent($studentClass->user, $class, 'completed')
        );

        return response()->json(['message' => 'Class marked complete']);
    }

    public function updateClassStatus(Request $request)
    {
        $studentClass = StudentClasses::findOrFail($request->student_class_id);
        $status = $request->status; // 'completed', 'attending', 'stopped'

        $studentClass->update(['status' => $status]);

        // Notify student of status change
        $studentClass->user->notify(
            new ClassCompletedByStudent($studentClass->user, $studentClass->academy, $status)
        );

        return response()->json(['message' => "Class marked as {$status}"]);
    }
}
```

### 3. When Prayer Request Is Submitted

**Scenario**: Public submits prayer request → notify prayer team

```php
<?php

namespace App\Http\Controllers;

use App\Models\PrayerRequest;
use App\Models\Team;
use App\Notifications\PrayerRequestSubmitted;
use Illuminate\Http\Request;

class PrayerRequestController extends Controller
{
    public function store(Request $request)
    {
        $prayerRequest = PrayerRequest::create([
            'title' => $request->title,
            'description' => $request->description,
            'user_id' => auth()->id(),
            'chapter_id' => auth()->user()->chapter_id,
        ]);

        // Get prayer request team
        $prayerTeam = Team::whereHas('prayerRequests')
            ->where('chapter_id', auth()->user()->chapter_id)
            ->first();

        if ($prayerTeam) {
            // Notify all team members
            $teamMembers = $prayerTeam->users()->get();
            
            foreach ($teamMembers as $member) {
                $member->notify(new PrayerRequestSubmitted($prayerRequest));
            }
        }

        return response()->json(['message' => 'Prayer request submitted']);
    }
}
```

### 4. When User Registers for Event

**Scenario**: User registers for event → notify event organizer

```php
<?php

namespace App\Http\Controllers;

use App\Models\Events;
use App\Notifications\EventRegistered;
use Illuminate\Http\Request;

class EventRegistrationController extends Controller
{
    public function register(Request $request)
    {
        $event = Events::findOrFail($request->event_id);
        $user = auth()->user();

        // Create registration
        $event->registrations()->attach($user->id, [
            'registered_at' => now(),
        ]);

        // Get event organizer/team
        $eventTeam = $event->eventTeams()->first();

        if ($eventTeam && $eventTeam->team) {
            // Notify team lead
            $teamLead = $eventTeam->team->leader()->with('user')->first();
            
            if ($teamLead && $teamLead->user) {
                $teamLead->user->notify(new EventRegistered($event, $user));
            }
        }

        return response()->json(['message' => 'Registered for event']);
    }
}
```

### 5. Appointment Scheduled Notification

**Existing implementation** (already in the codebase):

```php
<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Notifications\AppointmentScheduled;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function store(Request $request)
    {
        $appointment = Appointment::create($request->validated());

        // Notify appointment team
        $appointmentTeam = $appointment->appointmentTeam()->first();

        if ($appointmentTeam && $appointmentTeam->team) {
            $teamMembers = $appointmentTeam->team->users()->get();
            
            foreach ($teamMembers as $member) {
                $member->notify(new AppointmentScheduled($appointment));
            }
        }

        return response()->json(['message' => 'Appointment scheduled']);
    }
}
```

---

## Using Events for Decoupled Notifications

Instead of directly notifying in controllers, use Events + Listeners for better separation:

### Register Event Listener

In `app/Providers/EventServiceProvider.php`:

```php
protected $listen = [
    StudentRegisteredToAcademy::class => [
        NotifyTeamLeadOfRegistration::class,
    ],
];
```

### Dispatch Event from Controller

```php
// In your enrollment controller
StudentRegisteredToAcademy::dispatch($student, $academy, $studentClass);

// The listener handles notification automatically
```

### Create Custom Listener

```php
<?php

namespace App\Listeners;

use App\Events\StudentRegisteredToAcademy;
use App\Notifications\UserRegisteredToAcademy;

class NotifyTeamLeadOfRegistration
{
    public function handle(StudentRegisteredToAcademy $event): void
    {
        $academyTeam = $event->academy->believersAcademyTeam()
            ->with('team')
            ->first();

        if ($academyTeam && $academyTeam->team) {
            $teamLead = $academyTeam->team->leader()
                ->with('user')
                ->first();

            if ($teamLead && $teamLead->user) {
                $teamLead->user->notify(
                    new UserRegisteredToAcademy($event->student, $event->academy)
                );
            }
        }
    }
}
```

---

## Database Notifications

All notifications are also stored in the `notifications` table. Access them in views:

### Blade Template

```blade
@forelse (auth()->user()->unreadNotifications as $notification)
    <div class="alert alert-info">
        <strong>{{ $notification->data['message'] }}</strong>
        <small>{{ $notification->created_at->diffForHumans() }}</small>
        
        @if ($notification->type === 'academy_registration')
            <a href="/admin/academy/students/{{ $notification->data['student_id'] }}">
                View Student
            </a>
        @endif
        
        <button onclick="markRead('{{ $notification->id }}')">Dismiss</button>
    </div>
@empty
    <p>No new notifications</p>
@endforelse

<script>
function markRead(id) {
    fetch(`/notifications/${id}/read`, { method: 'POST' });
}
</script>
```

### Controller

```php
public function getNotifications()
{
    return auth()->user()->unreadNotifications()
        ->whereType('academy_registration')
        ->get();
}

public function markAsRead($notificationId)
{
    auth()->user()->notifications()
        ->find($notificationId)
        ->markAsRead();

    return response()->json(['status' => 'ok']);
}
```

---

## Trait for Reusable Notification Logic

Use the `NotifyAcademyTeamLead` trait in your models:

```php
<?php

namespace App\Models;

use App\Traits\NotifyAcademyTeamLead;
use Illuminate\Database\Eloquent\Model;

class StudentClasses extends Model
{
    use NotifyAcademyTeamLead;

    public function markComplete()
    {
        $this->update(['status' => 'completed']);
        
        // Use trait method
        $this->notifyTeamLeadOfClassCompletion(
            $this->user,
            $this->academy->classes()->first(),
            'completed'
        );
    }
}
```

---

## Testing Notifications

### Using Livewire/Volt

```php
use Livewire\Volt\Component;
use App\Notifications\StudentEnrolledNotification;

new class extends Component {
    public function testNotification()
    {
        $user = auth()->user();
        $academy = \App\Models\BeliversAcademy::first();
        
        $user->notify(new StudentEnrolledNotification($academy));
        
        $this->dispatch('notify', 'Test email sent - check logs');
    }
}
```

### Using Artisan Tinker

```bash
php artisan tinker
>>> $user = App\Models\User::find(1);
>>> $academy = App\Models\BeliversAcademy::first();
>>> $user->notify(new App\Notifications\StudentEnrolledNotification($academy));
>>> exit

# Check logs
$ tail -200 storage/logs/laravel.log
```

---

## Checking Email Logs

All emails are logged to `storage/logs/laravel.log`:

```bash
# View recent emails
tail -500 storage/logs/laravel.log

# Search by notification type
grep -i "welcome" storage/logs/laravel.log
grep -i "enrollment" storage/logs/laravel.log
grep -i "completed" storage/logs/laravel.log

# Save email to file for viewing in email client
tail -200 storage/logs/laravel.log > email.txt
```

---

## Summary

✅ 5 notifications implemented
✅ Team lead notifications via events
✅ Database + mail channels
✅ Log-based email catching for development
✅ Easy integration in controllers
✅ Event-based architecture for decoupling
✅ Database notification retrieval
