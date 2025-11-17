# Event Gallery & Registration System Documentation

## Overview

This document outlines the complete implementation of the Event Gallery feature and improved Event Registration system based on time constraints and availability.

## Features Implemented

### 1. Time-Based Event Registration
- **Registration Window**: Registration is only open BEFORE the event starts
- **Registration Closes**: Automatically when `start_at` time is reached
- **Validation**: Uses model methods for consistent checking across the application

### 2. Event Gallery
- **Public Access**: Gallery is only viewable AFTER the event has started
- **Image Management**: Admins can upload, edit, and manage event images
- **Responsive Design**: 
  - Bootstrap for public home pages
  - Tailwind for admin dashboard
- **Features**:
  - Image upload with metadata (title, size)
  - Image reordering
  - Thumbnail preview
  - Image deletion
  - Modal view for full-size images

### 3. Event Status Management
- Multiple event statuses: `draft`, `published`, `cancelled`, `archived`
- Real-time event state detection (upcoming, ongoing, ended)
- Capacity tracking and management

## Database Structure

### New Migration
**File**: `database/migrations/2025_11_15_000001_add_registration_columns_to_account_events.php`

```php
Schema::table('account_events', function (Blueprint $table) {
    $table->dateTime('registered_at')->nullable()->after('event_id');
    $table->enum('status', ['registered', 'attended', 'cancelled', 'pending'])->default('registered')->after('registered_at');
});
```

### Table Structure

#### `events` table
```
- id
- chapter_id
- created_by
- title
- slug
- description
- start_at (KEY: Registration window closes at this time)
- end_at
- timezone
- location
- is_online
- livestream_url
- banner
- status (draft|published|cancelled|archived)
- capacity
- registration_required
- form_schema
- timestamps
```

#### `event_galleries` table
```
- id
- event_id (foreign key)
- chapter_id
- title
- file_path
- thumbnail_path
- mime_type
- size
- order_column (for manual ordering)
- timestamps
- soft_deletes
```

#### `account_events` table
```
- id
- account_id (foreign key)
- event_id (foreign key)
- registered_at (timestamp of registration)
- status (registered|attended|cancelled|pending)
- unique [account_id, event_id]
- timestamps
```

## Models

### Events Model
**Location**: `app/Models/Events.php`

**Methods**:
```php
// Check if registration is currently open
isRegistrationOpen(): bool

// Check if event has started
hasStarted(): bool

// Check if event has ended
hasEnded(): bool

// Get remaining capacity
getRemainingCapacity(): ?int

// Check if event is at capacity
isAtCapacity(): bool

// Status text helper
getEventStatusText(): string

// Registration status text
getRegistrationStatusText(): string
```

**Relationships**:
```php
$event->chapter()         // The chapter hosting the event
$event->creator()         // The user who created the event
$event->forms()          // EventForm registrations
$event->galleries()      // EventGallery images
$event->accounts()       // Registered accounts
```

### EventGallery Model
**Location**: `app/Models/EventGallery.php`

**Attributes**:
- `event_id`: Links to the event
- `chapter_id`: For chapter-level organization
- `title`: Image title/caption
- `file_path`: Storage path to the image
- `thumbnail_path`: Path to thumbnail
- `mime_type`: Image MIME type
- `size`: File size in bytes
- `order_column`: Display order

### AccountEvent Model
**Location**: `app/Models/AccountEvent.php`

**New Attributes**:
- `registered_at`: When the account registered
- `status`: Registration status (registered|attended|cancelled|pending)

**Scope**:
```php
canViewGallery()  // Get registrations where event has started
```

## Views & Components

### Home Frontend

#### Event Index (`resources/views/livewire/home/events/index.blade.php`)
- Displays all published events
- Filter by chapter
- Shows "View Gallery" button only if event has started
- Uses Bootstrap styling
- Responsive grid layout

#### Event Gallery (`resources/views/livewire/home/events/gallery.blade.php`)
- NEW component
- Public event gallery viewer
- Only accessible after event starts
- Features:
  - Grid layout with hover effects
  - Image modal viewer
  - Created at timestamps
  - Empty state messaging
- Uses Bootstrap styling

#### Event Registration (`resources/views/livewire/home/event/register.blade.php`)
- Improved validation using model methods
- Shows capacity and registration status
- Only accessible during registration window
- Includes gallery access info for ongoing events

### Admin Dashboard

#### Gallery Management (`resources/views/livewire/admin/dashboard/event/gallery-management.blade.php`)
- NEW comprehensive admin component
- Features:
  - Event selector (shows image count)
  - Drag-and-drop file upload
  - Image title editing
  - Image reordering
  - Image deletion with confirmation
  - Responsive grid layout
- Uses Tailwind styling (dashboard)
- File storage in `storage/app/public/event-galleries`

## Routes

### Public Routes
```php
// Events index
GET  /events                          -> events.index

// Event gallery (only accessible after event starts)
GET  /events/{event}/gallery          -> events.gallery

// Event registration (only during registration window)
GET  /events/{event_id}/register      -> events.register
```

### Admin Routes
```php
// Event gallery management (admin only)
GET  /admin/dashboard/events/gallery  -> admin.dashboard.events.gallery
```

## Registration Logic

### Registration Flow

```
User visits /events/{event_id}/register
    ↓
Mount checks:
    1. Event exists?
    2. Registration required?
    3. Event is published?
    4. Event hasn't started? (time check on start_at)
    5. Event not at capacity?
    ↓
All checks pass:
    Form renders with custom fields
    ↓
User submits form
    ↓
Submit validates:
    - Form schema validation
    - File uploads
    - Email, phone, etc.
    ↓
Create EventForm record
Create/update AccountEvent record
Send notifications
    ↓
Redirect to home with success message
```

### Time-Based Validation

**Start Time Check** (`start_at`):
```php
// Event registration closes when start_at is reached
if ($this->event->hasStarted()) {
    abort(403, 'Registration for this event has closed. The event has already started.');
}
```

**End Time Check** (`end_at`):
```php
// Optional: used for determining if event is finished
if ($this->event->hasEnded()) {
    // Event is complete
}
```

## Gallery Access Control

### When Gallery is Available
```php
// Event must have started
if (!$this->event->hasStarted()) {
    abort(403, 'The event gallery will be available once the event starts.');
}
```

### Gallery Status Display
- **Before Start**: "Gallery Coming Soon" (no gallery button)
- **After Start**: Gallery link appears and is accessible
- **After End**: Gallery remains accessible

## File Storage

### Storage Configuration
- **Disk**: `public` (Laravel's default public disk)
- **Paths**:
  - Event galleries: `storage/app/public/event-galleries/`
  - Event registrations: `storage/app/public/event-registrations/`

### Access URLs
```
Public URL: /storage/event-galleries/filename.jpg
Storage path: storage/app/public/event-galleries/filename.jpg
```

## Styling

### Home Pages (Bootstrap)
- Modern gradient buttons
- Card-based layouts
- Responsive grid systems
- Glass-morphism effects
- Smooth animations

### Admin Dashboard (Tailwind)
- Clean card interfaces
- Tailwind utility classes
- Responsive grid layouts
- Focus on functionality

## Validation Rules

### Event Registration Form
- Fields defined by `form_schema` JSON
- Types supported:
  - `text` (default)
  - `email` (with email validation)
  - `number` (numeric only)
  - `date` (date format)
  - `file` (with 10MB limit)
  - `phone` (string, max 20)
  - `textarea`
  - `select` (with options)
  - `radio` (single choice)
  - `checkbox` (multiple choice)

### Image Upload
- Max size: 10MB
- Allowed types: image/*
- MIME type validation
- File size tracking in DB

## API Response Examples

### Check Event Status
```php
$event = Events::find(1);

// Registration status
$event->isRegistrationOpen()      // bool
$event->hasStarted()              // bool
$event->hasEnded()                // bool
$event->isAtCapacity()            // bool
$event->getRemainingCapacity()    // int|null
$event->getRegistrationStatusText() // string
```

### Access Gallery Images
```php
$event = Events::find(1);

// Get gallery images
$images = $event->galleries()
    ->orderBy('order_column')
    ->get();

// Check if can view
if ($event->hasStarted()) {
    // Show gallery
}
```

## Event Helper Trait

**Location**: `app/Traits/EventHelper.php`

Provides utility methods for events:
```php
getEventStatusText()              // "Upcoming", "Ongoing", "Ended"
getEventStatusBadgeClass()        // CSS class for status badge
getTimeUntilStart()               // Human readable time
getTimeUntilEnd()                 // Human readable time
getRegistrationStatusText()       // "Registration open", "Closed", etc.
getRegistrationStatusBadgeClass() // CSS class for status badge
```

## Error Handling

### Registration Errors
```
400: Bad Request - Form validation failed
403: Forbidden - Event not eligible for registration
    - Event not published
    - Event has started (registration window closed)
    - Event is full (at capacity)
404: Not Found - Event doesn't exist
```

### Gallery Errors
```
403: Forbidden - Event hasn't started yet
404: Not Found - Event or image doesn't exist
```

## Testing Checklist

- [ ] User can register before event starts
- [ ] User cannot register after event starts
- [ ] User cannot register if event is full
- [ ] Gallery is not visible before event starts
- [ ] Gallery becomes visible when event starts
- [ ] Admin can upload images to event gallery
- [ ] Admin can edit image titles
- [ ] Admin can delete images
- [ ] Images maintain order
- [ ] File size is tracked correctly
- [ ] Registration form validates correctly
- [ ] Capacity calculations are correct
- [ ] Event status shows correctly (Upcoming, Ongoing, Ended)

## Performance Considerations

### Database Indexes
- `events.start_at` - for time-based queries
- `events.status` - for filtering published events
- `event_galleries.event_id` - for gallery queries
- `event_galleries.order_column` - for ordering
- `account_events.event_id` - for capacity checks

### Caching
- Consider caching gallery images if high traffic
- Cache event capacity checks for large events
- Use view caching for event pages

## Security

### File Upload Security
- MIME type validation
- File size limits
- Storage outside web root (handled by Laravel)
- File name hashing

### Access Control
- Gallery only after event starts (time-based)
- Registration only before event starts (time-based)
- Admin operations require `admin` middleware
- Soft deletes for data recovery

## Future Enhancements

1. **Image Optimization**
   - Automatic thumbnail generation
   - WebP conversion
   - Lazy loading

2. **Social Sharing**
   - Share individual gallery images
   - Social media integration

3. **Advanced Filtering**
   - Filter events by date range
   - Location-based search
   - Category filtering

4. **Notifications**
   - Gallery ready notifications
   - New image notifications
   - Event status change notifications

5. **Analytics**
   - Gallery view tracking
   - Registration completion rates
   - Popular images/events

## Troubleshooting

### Gallery Not Showing
```
1. Check event start_at time is in the past
2. Verify event status is 'published'
3. Check images are uploaded and visible in admin
4. Verify Storage::url() is working
```

### Registration Window Issues
```
1. Check server time is correct
2. Verify event start_at time in UTC/correct timezone
3. Check registration_required is true
4. Verify event status is 'published'
```

### File Upload Issues
```
1. Check storage/app/public directory exists
2. Verify file permissions (755 for directories)
3. Run: php artisan storage:link
4. Check MAX_FILE_SIZE is not exceeded
```

## Deployment

### Pre-Deployment Checklist
```bash
# Run migrations
php artisan migrate

# Create storage symlink
php artisan storage:link

# Clear caches
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Compile assets if using Vite
npm run build
```

### Production Considerations
- Use CDN for image delivery
- Enable image compression
- Set up automated backups
- Monitor disk space for uploads
- Configure appropriate file permissions
