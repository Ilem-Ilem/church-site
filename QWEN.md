# Church Site - Project Overview

## Project Description

The Church Site is a comprehensive multi-conclave church management system built with Laravel 12, featuring Livewire Volt for reactive components. The system manages various aspects of church operations including member management, events, sermons, academy programs, transportation requests, partnerships, and more.

## Technology Stack

- **Backend Framework**: Laravel 12
- **Frontend Framework**: Livewire Volt (single-file components)
- **Styling**: Bootstrap 5 + Tailwind CSS + TALL Stack UI
- **Database**: MySQL (with Eloquent ORM)
- **Queue System**: Laravel Queues
- **File Management**: Spatie Media Library
- **Permissions**: Spatie Laravel Permission
- **PDF Generation**: FPDF/FPDI
- **Build Tools**: Vite, NPM

## Key Features Actually Implemented

### Core Functionality
- User authentication and authorization with role-based access
- Multi-conclave management system (chapters)
- Profile management for users
- Dashboard with analytics and reporting

### Church-Specific Modules
- **Believers Academy**: Enrollment, class management, certificate generation
- **Events Management**: Event creation, registration, galleries
- **Sermons**: Upload, categorization, series management
- **Prayer Requests**: Submission and team assignment
- **Cell Groups**: Small group management
- **Transportation**: Pickup request system with admin approval workflow
- **Partnerships**: Partnership management
- **Appointments**: Booking system for counseling and meetings
- **Finance**: Giving tracking and financial reporting
- **Testimonies**: Submission and management
- **Medical Health Cards**: Subscription-based health benefits

### Administrative Features
- Super Admin dashboard for system-wide management
- Chapter-based admin panels with custom middleware
- User management within chapters
- Role and permission management (using Spatie package)
- Audit logging (migration exists)
- System configuration

## Project Structure

```
church-site/
├── app/                    # Application source code
│   ├── Http/              # Controllers, Middleware
│   │   ├── Controllers/   # HTTP controllers
│   │   └── Middleware/    # Custom middleware (AdminChapters, SuperAdmin)
│   ├── Livewire/          # Livewire components
│   │   ├── Actions/       # Reusable actions
│   │   └── Admin/         # Admin-specific components
│   ├── Models/            # Eloquent models (40+ models)
│   ├── Jobs/              # Queue jobs
│   └── Traits/            # Custom traits
├── resources/             # Views, CSS, JS
│   ├── views/             # Blade templates (including Volt components)
│   │   ├── livewire/      # Volt component templates
│   │   │   ├── admin/     # Admin components
│   │   │   │   ├── superadmin/ # Super admin components
│   │   │   │   └── dashboard/  # Chapter admin dashboard components
│   │   │   ├── home/      # Public-facing components
│   │   │   ├── auth/      # Authentication components
│   │   │   └── settings/  # User settings components
│   │   └── components/    # Reusable UI components
│   ├── css/               # Stylesheets
│   └── js/                # JavaScript
├── routes/                # Route definitions
├── database/              # Migrations, seeds, factories
│   └── migrations/        # 50+ migrations for complete schema
├── public/                # Public assets
├── config/                # Configuration files
└── ...
```

## Key Directories and Files

- `routes/web.php` - Main web routes including Volt components
- `routes/admin_route.php` - Chapter admin routes (50+ routes)
- `routes/super_admin_route.php` - Super admin routes
- `app/Models/` - 40+ Eloquent models with relationships
- `database/migrations/` - 50+ migrations for complete schema
- `app/Http/Middleware/AdminChapters.php` - Chapter-based access control
- `app/Http/Middleware/SuperAdmin.php` - Super admin access control
- `app/Models/User.php` - User model with Spatie roles/permissions
- `app/Http/Controllers/CertificateController.php` - Certificate generation

## Actual Implementation Status

### Working Features
- Complete authentication system with login, registration, password reset
- Multi-conclave (chapter) management with proper data isolation
- Super admin dashboard with conclave management
- Chapter admin dashboards with role-based access
- Believers Academy with class management and certificate generation
- Transportation request system (public submission + admin approval)
- Event management system with registration
- Sermon management system
- Prayer request system
- Testimony submission and management
- Member management within chapters
- Team management within chapters
- Finance tracking
- Cell group management
- Partnership management
- Appointment booking system

### Key Technical Implementation Details
- **Multi-tenancy**: Each chapter has isolated data using chapter_id foreign keys
- **Role-based Access**: Using Spatie Laravel Permission package with roles like super-admin, admin, team-lead
- **Custom Middleware**: AdminChapters middleware enforces chapter-based access control
- **Livewire Volt**: Single-file components combining PHP logic and Blade template
- **PDF Generation**: Certificate generation using FPDF/FPDI
- **File Uploads**: Using Spatie Media Library for file management
- **Activity Logging**: Migration exists for audit trail functionality

## Building and Running

### Prerequisites
- PHP 8.2+
- Composer
- Node.js and NPM
- MySQL or compatible database

### Setup Instructions

1. **Install Dependencies**:
   ```bash
   composer install
   npm install
   ```

2. **Environment Configuration**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
   
   Configure your database settings in `.env`

3. **Database Setup**:
   ```bash
   php artisan migrate --seed
   ```

4. **Build Assets**:
   ```bash
   npm run build
   # Or for development with hot reloading:
   npm run dev
   ```

5. **Start Server**:
   ```bash
   php artisan serve
   ```

6. **Queue Worker** (for background jobs):
   ```bash
   php artisan queue:work
   ```

### Development Commands

- **Run Tests**:
  ```bash
  php artisan test
  ```

- **Development Mode** (with concurrent server, queue, and asset building):
  ```bash
  composer run dev
  ```

- **Clear Caches**:
  ```bash
  php artisan optimize:clear
  ```

- **View Routes**:
  ```bash
  php artisan route:list
  ```

## Development Conventions

### Naming Conventions
- Models follow PascalCase (e.g., `User`, `BelieversAcademy`)
- Controllers follow PascalCase with `Controller` suffix
- Volt components use dot notation in routes (e.g., `'home.landing'` maps to `resources/views/livewire/home/landing.blade.php`)

### Volt Components
- Single-file components combining PHP logic and Blade template
- Use `Volt::route()` for defining routes
- State management with public properties
- Actions defined as public methods

### Database Schema
- Migrations follow Laravel conventions
- Foreign key relationships properly defined
- Chapter-based data isolation using chapter_id
- Timestamps included on most tables
- Soft deletes used where appropriate

### Authentication & Authorization
- Laravel's built-in authentication
- Spatie Laravel Permission for role-based access
- Custom middleware for super admin and chapter admin access
- Chapter-based data isolation

## Current Status

The system has substantial functionality implemented:
- 40+ Eloquent models representing the complete domain
- 50+ database migrations for the full schema
- Complete authentication and authorization system
- Super admin functionality for managing multiple conclaves
- Chapter admin dashboards with comprehensive management features
- Public-facing features like event registration, transportation requests, and testimonies
- Certificate generation for the Believers Academy
- Transportation request system with approval workflow

The project appears to be actively developed with recent migrations and features being added. The multi-conclave architecture is well-implemented with proper data isolation between chapters.

## Key Architectural Elements

1. **Multi-Conclave Architecture**: Each chapter operates independently with shared system access
2. **Role-Based Permissions**: Super-admin, admin, team-lead, and member roles
3. **Data Isolation**: Chapter ID ensures data separation between conclaves
4. **Component-Based UI**: Livewire Volt for reactive, single-file components
5. **Modular Design**: Separate sections for different church functions