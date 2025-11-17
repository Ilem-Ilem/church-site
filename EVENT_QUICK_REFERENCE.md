# Event Gallery & Registration - Quick Reference

## Quick Start

### For Admins: Upload Gallery Images

1. Go to `/admin/dashboard/events/gallery`
2. Select an event from the list
3. Upload images with titles
4. Images become visible to public once event starts

### For Users: View Gallery

1. Go to `/events` (Events Index)
2. Click "View Gallery" button (appears after event starts)
3. Browse and click images to view full-size
4. Gallery only visible after event has started

### For Users: Register for Event

1. Go to `/events` (Events Index)
2. Click "View Details" on an event
3. Click "Register Now" button
4. Fill out the registration form
5. Submit before event starts

---

## Time Rules

### Registration Window
```
OPEN:   Now until event start_at time
CLOSED: After event start_at time
STATUS: Automatic - no manual action needed
```

### Gallery Access
```
HIDDEN: Before event start_at time
VISIBLE: After event start_at time
STATUS: Automatic - no manual action needed
```

---

## Key Methods

### Check Event Status
```php
$event = Events::find(1);

$event->hasStarted()              // true if event has begun
$event->hasEnded()                // true if event has finished
$event->isRegistrationOpen()      // true if can register now
$event->isAtCapacity()            // true if no more spots
$event->getRemainingCapacity()    // number of spots left
```

### Get Status Text
```php
$event->getEventStatusText()      // "Upcoming", "Ongoing", "Ended"
$event->getRegistrationStatusText() // "Registration open", "Closed", etc.
```

---

## Database Tables

### Events Table (Key Fields)
- `start_at` - Registration closes at this time
- `end_at` - When event finishes
- `status` - Must be "published" for registration/gallery
- `registration_required` - If true, registration form shows
- `capacity` - Max registrations allowed

### Event Galleries Table
- `event_id` - Links to event
- `file_path` - Where image is stored
- `title` - Optional image caption
- `order_column` - Display order (1, 2, 3...)

### Account Events Table (Registrations)
- `account_id` - The person who registered
- `event_id` - The event
- `registered_at` - When they registered
- `status` - registered|attended|cancelled|pending

---

## Files & Locations

### Code
| File | Purpose |
|------|---------|
| `app/Models/Events.php` | Event model with methods |
| `app/Models/EventGallery.php` | Gallery image model |
| `app/Models/AccountEvent.php` | Registration model |
| `app/Traits/EventHelper.php` | Status helper methods |

### Views
| File | Purpose |
|------|---------|
| `resources/views/livewire/home/events/index.blade.php` | Event listing (public) |
| `resources/views/livewire/home/events/gallery.blade.php` | Gallery viewer (public) |
| `resources/views/livewire/home/event/register.blade.php` | Registration form (public) |
| `resources/views/livewire/admin/dashboard/event/gallery-management.blade.php` | Gallery uploader (admin) |

### Storage
```
storage/app/public/event-galleries/     ← Gallery images stored here
storage/app/public/event-registrations/ ← Registration files stored here
```

---

## Routes

### Public Routes
```
GET  /events                 → View all events
GET  /events/{event}/gallery → View event gallery (if started)
GET  /events/{id}/register   → Register for event (if open)
```

### Admin Routes
```
GET  /admin/dashboard/events/gallery → Manage gallery
```

---

## Common Tasks

### Upload Gallery Image
```
1. Admin goes to /admin/dashboard/events/gallery
2. Select event
3. Click "Choose Images"
4. Select image file and optional title
5. Click "Upload Image"
```

### View Event Gallery
```
1. User goes to /events
2. Finds event (must have started)
3. Clicks "View Gallery" button
4. Browses and clicks images
```

### Check Registration Status
```php
// In a component or controller
$event = Events::find(1);

if ($event->isRegistrationOpen()) {
    // Show registration link
} else {
    echo $event->getRegistrationStatusText();
}
```

### Get All Gallery Images
```php
$event = Events::find(1);
$images = $event->galleries()
    ->orderBy('order_column')
    ->get();
```

---

## Validation & Errors

### Registration Errors
```
ERROR: "This event does not require registration"
FIX:   Admin must enable registration_required for event

ERROR: "This event is not currently published"
FIX:   Admin must publish event (status = 'published')

ERROR: "Registration for this event has closed"
FIX:   Event has started, registration window closed

ERROR: "This event has reached maximum capacity"
FIX:   Event is full, no more registrations allowed
```

### Gallery Errors
```
ERROR: "The event gallery will be available once the event starts"
FIX:   Wait for event to start_at time
```

---

## Settings Checklist

For Event to Work Properly:

- [ ] **Create Event**
  - Title
  - Description
  - start_at (date/time)
  - end_at (optional, for ending event)
  - location
  - status = "published"

- [ ] **Enable Registration** (if needed)
  - Set registration_required = true
  - Create form fields (form_schema)
  - Set capacity (optional)

- [ ] **Event During**
  - Admin uploads gallery images
  - Images show in /events/{id}/gallery

- [ ] **After Event**
  - Gallery remains accessible
  - Registration window is closed

---

## Image Upload Limits

- **Max Size**: 10MB per image
- **Allowed Types**: JPG, PNG, GIF, WebP
- **Storage**: Unlimited (monitor disk space)
- **Automatic**: Timestamp added to each upload

---

## Capacity Management

### Check Remaining Spots
```php
$event = Events::find(1);
$remaining = $event->getRemainingCapacity(); // number or null if unlimited

if ($event->isAtCapacity()) {
    echo "Event is full";
}
```

### Set Capacity
When creating/editing event:
```
capacity = 50  → Limit to 50 registrations
capacity = null → Unlimited registrations
```

---

## Timezone Handling

- Stored in `timezone` field
- Default: Server timezone
- Times are in UTC in database
- Displayed in user's timezone via Carbon

---

## Testing

Quick test:
1. Create event with start_at = 10 minutes from now
2. Try to register → Should work
3. Manually update start_at = 5 minutes ago in DB
4. Try to register → Should be blocked
5. Try to view gallery → Should be available

---

## Troubleshooting

### Images not showing in gallery
```
✓ Event has started (start_at is in past)?
✓ Event status is "published"?
✓ Images uploaded in admin?
✓ run: php artisan storage:link
```

### Can't register
```
✓ Event status is "published"?
✓ Event hasn't started yet (start_at is in future)?
✓ registration_required is true?
✓ Event not full (capacity check)?
```

### Files not uploading
```
✓ Check storage permissions: chmod 755 storage/
✓ Run: php artisan storage:link
✓ Image under 10MB?
✓ Supported image format?
```

---

## Performance Tips

1. **For Large Events** (500+ registrations)
   - Add database indexes on start_at, status
   - Cache event availability checks
   - Use pagination for gallery

2. **For Many Images** (100+ per event)
   - Generate thumbnails automatically
   - Implement image optimization
   - Use CDN for serving images

3. **General**
   - Clear caches after updates
   - Monitor disk space for uploads
   - Set up automated backups

---

## Support

For detailed documentation, see: `EVENT_GALLERY_REGISTRATION.md`

For code changes, check: Git history or CHANGES_MADE.md
