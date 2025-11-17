# Home Navbar Authentication Update

Login/Logout buttons have been added to all navbars in the /home/ section.

## Updated Files

### Main Layout (Used by most pages)
1. **resources/views/components/layouts/layout.blade.php**
   - Added login/logout button to the top navbar
   - Shows "Login" button when user is not authenticated (guest)
   - Shows "Logout" button when user is authenticated
   - Desktop view (collapse navbar)

### Pages with Custom Navbars
2. **resources/views/livewire/home/landing.blade.php**
   - Updated offcanvas mobile menu
   - Added login/logout button to mobile navigation

3. **resources/views/livewire/home/transport.blade.php**
   - Updated both desktop and mobile (offcanvas) navigation
   - Added login/logout buttons to both views

4. **resources/views/livewire/home/believers/index.blade.php**
   - Updated offcanvas mobile menu
   - Added login/logout button to mobile navigation

## Functionality

### When User is Not Logged In
- Displays a "Login" link in the navbar
- Clicking it navigates to `/home/login` route (route name: `home.login`)
- Navigation bar shows: Home, Message, About, Cell, Event, Location, Believers academy, Need a Ride, **Login**

### When User is Logged In
- Displays a "Logout" button in the navbar
- Clicking it submits a POST form to the logout route
- CSRF token is automatically included for security
- Navigation bar shows: Home, Message, About, Cell, Event, Location, Believers academy, Need a Ride, **Logout**

## Styling

The login/logout button:
- Uses Bootstrap navbar classes for consistency
- Login: styled as a normal nav-link
- Logout: uses a transparent button with white text to match the nav-link appearance
- Mobile view: properly spaced within the offcanvas menu wrapper
- Desktop view: aligns with other navigation items

## Other Pages

The following pages inherit the navbar from the main layout.blade.php:
- `/home.believers.register` - Believers Academy Registration
- `/home.believers.dashboard` - Believers Academy Dashboard
- `/home.prayers.request` - Prayer Request Page
- `/home.appointment` - Appointments Page
- `/home.events.index` - Events Listing
- `/home.events.gallery` - Event Gallery
- `/home.event.register` - Event Registration
- `/home.sermons.index` - Sermons
- `/home.partnership.index` - Partnership

All these pages automatically have the login/logout button in their navbar.

## Authentication Routes Reference

- **Login Route**: `{{ route('home.login') }}` → `/home/login`
- **Register Route**: `{{ route('home.register') }}` → `/home/register`
- **Logout Route**: `{{ route('logout') }}` → POST request
- **Password Recovery**: `{{ route('home.password.request') }}` → `/home/password/request`

## Implementation Details

### Desktop Navbar (Bootstrap collapse)
```blade
@auth
    <form method="POST" action="{{ route('logout') }}" class="d-inline">
        @csrf
        <button type="submit" class="nav-link bg-transparent border-0 text-white" style="cursor: pointer;">Logout</button>
    </form>
@else
    <a class="nav-link" href="{{ route('home.login') }}" wire:navigate>Login</a>
@endauth
```

### Mobile Navbar (Offcanvas)
```blade
@auth
    <li class="nav-item">
        <form method="POST" action="{{ route('logout') }}" class="d-inline w-100">
            @csrf
            <button type="submit" class="nav-link bg-transparent border-0 text-white w-100 text-start" style="cursor: pointer;">Logout</button>
        </form>
    </li>
@else
    <li class="nav-item"><a class="nav-link" href="{{ route('home.login') }}" wire:navigate>Login</a></li>
@endauth
```

## Notes

- Uses Laravel's `@auth` and `@else` blade directives to conditionally display buttons
- Logout form uses POST method for security (prevents accidental logout via GET requests)
- CSRF token is included automatically in logout form
- All navigation uses `wire:navigate` for Livewire compatibility
