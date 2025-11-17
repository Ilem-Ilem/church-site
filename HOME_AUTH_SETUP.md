# Home Authentication System Setup

A complete authentication system for regular users (separate from admin/superadmin auth) has been created with Bootstrap styling.

## Created Files

### Views (Livewire Components)
1. **resources/views/livewire/home/auth/login.blade.php**
   - User login page
   - Email and password validation
   - Remember me option
   - Link to password recovery
   - Bootstrap styled interface
   - Redirects to home after successful login

2. **resources/views/livewire/home/auth/forgot-password.blade.php**
   - Password recovery request page
   - Users request a password reset (no OTP)
   - System creates a pending request for admin approval
   - Sends confirmation email to user
   - Bootstrap styled interface

### Models
4. **app/Models/PasswordResetRequest.php**
   - Tracks password reset requests
   - Fields: user_id, email, token, status (pending/approved/rejected), approved_at, approved_by, reason
   - Relations: belongs to User, approved by User

### Migrations
5. **database/migrations/2024_11_14_create_password_reset_requests_table.php**
   - Creates password_reset_requests table
   - Tracks all password reset requests with approval status

### Notifications
6. **app/Notifications/PasswordResetRequestNotification.php**
   - Sent to user when they request password reset
   - Confirms request was received
   - States that admin will review and send reset link if approved

7. **app/Notifications/PasswordResetApprovedNotification.php**
   - Sent to user when admin approves their password reset request
   - Contains link to reset password
   - Link expires in 60 minutes

## Routes Added (in routes/web.php)

```php
Route::middleware('guest')->group(function () {
    Volt::route('/home/login', 'home.auth.login')->name('home.login');
    Volt::route('/home/password/request', 'home.auth.forgot-password')->name('home.password.request');
});
```

### Routes Available:
- **GET /home/login** - Login page (route: `home.login`)
- **GET /home/password/request** - Password reset request page (route: `home.password.request`)

## Features

### Login Page
- Email and password authentication
- Rate limiting (5 attempts per IP)
- Remember me checkbox
- Link to password recovery
- Session management
- Redirects to home page after successful login

### Password Recovery
- User requests password reset by entering email
- System creates a PasswordResetRequest record with status: "pending"
- User receives confirmation email
- **Admin/Team Lead Action Required**: Must review and approve/reject the request
- Once approved, user receives email with password reset link
- Link is valid for 60 minutes

## Admin Approval Flow (To Be Implemented)

You'll need to create an admin panel page to:
1. View pending password reset requests
2. Approve or reject requests with optional reason
3. Send PasswordResetApprovedNotification with reset link when approved

## Bootstrap Styling

All pages are styled with Bootstrap 5 and custom CSS featuring:
- Gradient background (blue to dark blue)
- Centered card layout
- Responsive design (mobile-friendly)
- Form validation styling
- Loading spinners
- Smooth transitions and hover effects

## Chapter Selection

Users must select their chapter during registration. The selected chapter becomes their `chapter_id` in the users table, which is required for:
- Believers Academy registration
- Other chapter-specific features

## Next Steps

1. Run migration: `php artisan migrate`
2. Create admin page to approve/reject password reset requests
3. Test the registration and login flows
4. Update any styling to match your design preferences

## Integration Notes

- Uses existing User model with `chapter_id` field
- Uses Livewire Volt components
- Uses custom `home-auth` layout (no navbar/footer)
- Completely separate from existing admin/superadmin authentication
- Redirects authenticated users to home page
