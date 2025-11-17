# Transportation Feature Implementation

## Overview
The transportation feature allows church members to request pickup services for attending church services. Members can submit pickup requests from the public-facing transport page, and admins can manage these requests from the admin dashboard.

## Files Created/Modified

### Backend

#### Model
- `app/Models/Transport.php` - Transport request model

#### Database
- `database/migrations/2025_11_14_065831_create_transports_table.php` - Creates transports table with columns:
  - id (primary key)
  - name (string)
  - phone (string)
  - pickup_location (text)
  - status (enum: pending, approved, rejected)
  - notes (text, nullable)
  - processed_at (timestamp, nullable)
  - timestamps (created_at, updated_at)

#### Controller
- `app/Http/Controllers/TransportController.php` - Handles:
  - `store()` - Save new pickup requests
  - `updateStatus()` - Update request status by admin
  - `destroy()` - Delete transport requests

### Frontend - Public Page

#### Livewire Volt Component
- `resources/views/livewire/home/transport.blade.php` - Public transport request page with:
  - Hero section
  - Pickup locations display (3 demo locations)
  - Request form modal
  - Form validation with error display
  - Real-time submission feedback
  - Responsive design

### Frontend - Admin Dashboard

#### Livewire Volt Components
- `resources/views/livewire/admin/dashboard/transport/index.blade.php` - Transport requests list with:
  - Search by name, phone, or location
  - Filter by status (pending, approved, rejected)
  - Quick actions (approve, reject, delete)
  - Pagination (20 items per page)
  - Status indicators with badges

- `resources/views/livewire/admin/dashboard/transport/show.blade.php` - Request details page with:
  - Requestor information display
  - Status update form
  - Notes management
  - Timeline of events
  - Current status badge

### Routes

#### Web Routes (`routes/web.php`)
- `GET /transport` - Public transport page (Volt)
- `POST /transport/pickup-request` - Store pickup request (Controller)

#### Admin Routes (`routes/admin_route.php`)
- `GET /admin/dashboard/transport` - List requests (Volt)
- `GET /admin/dashboard/transport/{transport}` - View request details (Volt)
- `PUT /admin/dashboard/transport/{transport}/status` - Update status (Controller)
- `DELETE /admin/dashboard/transport/{transport}` - Delete request (Controller)

### HTML Integration
- `dox-church-site-main/transport.html` - Updated form to submit to backend API

## Features

### Public Features
1. View pickup locations with contact information
2. Request pickup from custom location
3. Form validation with error messages
4. Success feedback on submission
5. Responsive design for mobile/tablet/desktop

### Admin Features
1. View all pickup requests in paginated list
2. Search requests by name, phone, or location
3. Filter requests by status
4. View detailed request information
5. Approve pending requests
6. Reject requests with notes
7. Delete requests
8. Track processing timeline

## Database Schema

```sql
CREATE TABLE transports (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    pickup_location LONGTEXT NOT NULL,
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    notes LONGTEXT NULLABLE,
    processed_at TIMESTAMP NULLABLE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

## Usage

### For Members
1. Navigate to `/transport` page
2. Review available pickup locations and times
3. Click "Request a Pickup" button
4. Fill in form with name, phone, and location
5. Submit form
6. Receive confirmation message

### For Admins
1. Navigate to Admin Dashboard → Transportation
2. View all requests in list
3. Use search to find specific requests
4. Filter by status
5. Click eye icon to view full details
6. Update status with notes if needed
7. Approve/reject/delete as needed

## API Endpoints

### Public
- `POST /transport/pickup-request` - Submit pickup request
  - Request body:
    ```json
    {
      "name": "John Doe",
      "phone": "0801234567",
      "pickup-location": "123 Main St"
    }
    ```
  - Response: 201 Created with success message

### Admin
- `PUT /admin/dashboard/transport/{id}/status` - Update request status
  - Request body:
    ```json
    {
      "status": "approved|rejected|pending",
      "notes": "Optional notes"
    }
    ```
  - Response: 200 OK with updated data

- `DELETE /admin/dashboard/transport/{id}` - Delete request
  - Response: 200 OK with success message

## Security
- CSRF protection on all forms
- Middleware authentication on admin routes
- Admin middleware to restrict access
- Input validation on all fields
- Proper error handling

## Future Enhancements
1. Email notifications to requestors
2. SMS notifications for approved requests
3. Admin notification of new requests
4. Export requests to CSV/Excel
5. Schedule management interface
6. Driver assignment system
7. GPS tracking integration
8. Request status updates via email/SMS
9. Calendar view of pickups
10. Availability/capacity management

## Testing
Run migrations:
```bash
php artisan migrate
```

Test form submission:
1. Navigate to `/transport`
2. Click "Request a Pickup"
3. Fill form and submit
4. Check database for new record

Test admin panel:
1. Login as admin
2. Navigate to `/admin/dashboard/transport`
3. View requests and manage statuses
