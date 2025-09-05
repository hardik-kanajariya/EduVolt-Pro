# Filament Panel Configuration

This document outlines the multi-panel Filament configuration for role-based access in EduVault Pro.

## Overview

EduVault Pro uses Filament v3 with multiple panels to provide role-specific interfaces for different user types in the school management system.

## Panel Architecture

### Panel Structure

The system includes four main panels:

1. **Admin Panel** (`/admin`) - System administrators, principals
2. **Faculty Panel** (`/faculty`) - Teachers, staff, librarians
3. **Student Panel** (`/student`) - Student dashboard and services
4. **Parent Panel** (`/parent`) - Parent portal and monitoring

## Panel Configurations

### 1. Admin Panel (`/admin`)

**Access Roles:**
- `super_admin` - Full system access
- `admin` - School administration
- `principal` - Academic oversight

**Features:**
- Complete system management
- User role management
- School configuration
- Financial management
- Academic administration
- Reporting and analytics

**File Location:** `app/Providers/Filament/AdminPanelProvider.php`

**Configuration:**
```php
public function panel(Panel $panel): Panel
{
 return $panel
 ->default()
 ->id('admin')
 ->path('/admin')
 ->login()
 ->colors([
 'primary' => Color::Blue,
 ])
 ->discoverResources(in: app_path('Filament/Admin/Resources'), for: 'App\\Filament\\Admin\\Resources')
 ->discoverPages(in: app_path('Filament/Admin/Pages'), for: 'App\\Filament\\Admin\\Pages')
 ->pages([
 Pages\Dashboard::class,
 ])
 ->discoverWidgets(in: app_path('Filament/Admin/Widgets'), for: 'App\\Filament\\Admin\\Widgets')
 ->widgets([
 Widgets\AccountWidget::class,
 Widgets\FilamentInfoWidget::class,
 ])
 ->middleware([
 EncryptCookies::class,
 AddQueuedCookiesToResponse::class,
 StartSession::class,
 AuthenticateSession::class,
 ShareErrorsFromSession::class,
 VerifyCsrfToken::class,
 SubstituteBindings::class,
 DisableBladeIconComponents::class,
 DispatchServingFilamentEvent::class,
 ])
 ->authMiddleware([
 Authenticate::class,
 ]);
}
```

### 2. Faculty Panel (`/faculty`)

**Access Roles:**
- `teacher` - Teaching staff
- `accountant` - Financial staff
- `librarian` - Library management

**Features:**
- Class management (assigned classes only)
- Attendance marking
- Assignment creation and grading
- Exam management
- Student progress tracking
- Communication tools

**File Location:** `app/Providers/Filament/FacultyPanelProvider.php`

**Configuration:**
```php
public function panel(Panel $panel): Panel
{
 return $panel
 ->id('faculty')
 ->path('/faculty')
 ->login()
 ->colors([
 'primary' => Color::Green,
 ])
 ->discoverResources(in: app_path('Filament/Faculty/Resources'), for: 'App\\Filament\\Faculty\\Resources')
 ->discoverPages(in: app_path('Filament/Faculty/Pages'), for: 'App\\Filament\\Faculty\\Pages')
 ->pages([
 Pages\Dashboard::class,
 ])
 ->discoverWidgets(in: app_path('Filament/Faculty/Widgets'), for: 'App\\Filament\\Faculty\\Widgets')
 ->middleware([
 EncryptCookies::class,
 AddQueuedCookiesToResponse::class,
 StartSession::class,
 AuthenticateSession::class,
 ShareErrorsFromSession::class,
 VerifyCsrfToken::class,
 SubstituteBindings::class,
 DisableBladeIconComponents::class,
 DispatchServingFilamentEvent::class,
 ])
 ->authMiddleware([
 Authenticate::class,
 ]);
}
```

### 3. Student Panel (`/student`)

**Access Roles:**
- `student` - Student access

**Features:**
- Personal dashboard
- Attendance viewing
- Assignment submission
- Exam schedules and results
- Fee status
- Library book status
- Grade reports

**File Location:** `app/Providers/Filament/StudentPanelProvider.php`

**Configuration:**
```php
public function panel(Panel $panel): Panel
{
 return $panel
 ->id('student')
 ->path('/student')
 ->login()
 ->colors([
 'primary' => Color::Purple,
 ])
 ->discoverResources(in: app_path('Filament/Student/Resources'), for: 'App\\Filament\\Student\\Resources')
 ->discoverPages(in: app_path('Filament/Student/Pages'), for: 'App\\Filament\\Student\\Pages')
 ->pages([
 Pages\Dashboard::class,
 ])
 ->discoverWidgets(in: app_path('Filament/Student/Widgets'), for: 'App\\Filament\\Student\\Widgets')
 ->middleware([
 EncryptCookies::class,
 AddQueuedCookiesToResponse::class,
 StartSession::class,
 AuthenticateSession::class,
 ShareErrorsFromSession::class,
 VerifyCsrfToken::class,
 SubstituteBindings::class,
 DisableBladeIconComponents::class,
 DispatchServingFilamentEvent::class,
 ])
 ->authMiddleware([
 Authenticate::class,
 ]);
}
```

### 4. Parent Panel (`/parent`)

**Access Roles:**
- `parent` - Parent access

**Features:**
- Multi-child dashboard
- Child attendance monitoring
- Academic progress tracking
- Fee payment status
- Communication with school
- Event notifications

**File Location:** `app/Providers/Filament/ParentPanelProvider.php`

**Configuration:**
```php
public function panel(Panel $panel): Panel
{
 return $panel
 ->id('parent')
 ->path('/parent')
 ->login()
 ->colors([
 'primary' => Color::Orange,
 ])
 ->discoverResources(in: app_path('Filament/Parent/Resources'), for: 'App\\Filament\\Parent\\Resources')
 ->discoverPages(in: app_path('Filament/Parent/Pages'), for: 'App\\Filament\\Parent\\Pages')
 ->pages([
 Pages\Dashboard::class,
 ])
 ->discoverWidgets(in: app_path('Filament/Parent/Widgets'), for: 'App\\Filament\\Parent\\Widgets')
 ->middleware([
 EncryptCookies::class,
 AddQueuedCookiesToResponse::class,
 StartSession::class,
 AuthenticateSession::class,
 ShareErrorsFromSession::class,
 VerifyCsrfToken::class,
 SubstituteBindings::class,
 DisableBladeIconComponents::class,
 DispatchServingFilamentEvent::class,
 ])
 ->authMiddleware([
 Authenticate::class,
 ]);
}
```

## Authentication & Authorization

### Role-Based Access Control

Each panel enforces role-based access through:

1. **Panel-Level Access Control**
 - Users can only access panels appropriate to their role
 - Automatic redirection based on user role after login

2. **Resource-Level Permissions**
 - Individual resources check user permissions
 - Gate policies enforce CRUD operation restrictions

3. **Data Scoping**
 - Teachers see only their assigned classes
 - Students see only their own data
 - Parents see only their children's data

### Authentication Flow

```php
// Panel Provider Authorization
protected function shouldRegisterNavigation(): bool
{
 return auth()->user()->hasAnyRole(['admin', 'super_admin']);
}

// Resource Authorization
public static function canViewAny(): bool
{
 return auth()->user()->can('view_students');
}

// Data Scoping Example
public static function getEloquentQuery(): Builder
{
 $user = auth()->user();
 
 if ($user->hasRole('teacher')) {
 return parent::getEloquentQuery()
 ->whereHas('class.teachers', function ($query) use ($user) {
 $query->where('user_id', $user->id);
 });
 }
 
 return parent::getEloquentQuery();
}
```

## Navigation Structure

### Admin Panel Navigation
```
Dashboard
 System Management
 Users
 Roles & Permissions
 System Settings
 Academic Management
 Schools
 Academic Years
 Classes
 Subjects
 User Management
 Students
 Teachers
 Staff
 Attendance
 Examinations
 Fee Management
 Library
 Reports
 Communication
```

### Faculty Panel Navigation
```
Dashboard
 My Classes
 Students
 Attendance
 Assignments
 Examinations
 Timetable
 Reports
```

### Student Panel Navigation
```
Dashboard
 My Profile
 Attendance
 Assignments
 Examinations
 Grades
 Fees
 Library
 Notifications
```

### Parent Panel Navigation
```
Dashboard
 Children
 Attendance
 Academic Progress
 Fees
 Communication
 Events
 Reports
```

## Customization

### Theme Configuration

Each panel can have its own color scheme:

```php
->colors([
 'primary' => Color::Blue, // Admin
 'primary' => Color::Green, // Faculty
 'primary' => Color::Purple, // Student
 'primary' => Color::Orange, // Parent
])
```

### Widget Configuration

Role-specific widgets for each panel:

- **Admin Widgets:** System stats, user analytics, financial overview
- **Faculty Widgets:** Class performance, attendance summary, upcoming tasks
- **Student Widgets:** Personal stats, upcoming assignments, recent grades
- **Parent Widgets:** Children overview, fee reminders, school announcements

## Security Considerations

### Panel Isolation
- Each panel operates independently
- User sessions are panel-specific
- Cross-panel access requires proper authentication

### Data Protection
- Role-based data filtering
- Permission checking at multiple levels
- Audit logging for sensitive operations

### Session Management
- Secure session handling
- Automatic logout on inactivity
- Role validation on each request

## Deployment Considerations

### Panel Registration
All panels must be registered in `config/app.php`:

```php
'providers' => [
 // Other providers...
 App\Providers\Filament\AdminPanelProvider::class,
 App\Providers\Filament\FacultyPanelProvider::class,
 App\Providers\Filament\StudentPanelProvider::class,
 App\Providers\Filament\ParentPanelProvider::class,
],
```

### URL Structure
- Admin Panel: `https://domain.com/admin`
- Faculty Panel: `https://domain.com/faculty`
- Student Panel: `https://domain.com/student`
- Parent Panel: `https://domain.com/parent`

### Performance Optimization
- Panel-specific asset compilation
- Role-based resource loading
- Cached permission checks

## Maintenance

### Adding New Resources
1. Create resource in appropriate panel directory
2. Follow naming conventions
3. Implement proper authorization
4. Add to navigation if needed

### Panel Updates
1. Update panel provider configuration
2. Test role-based access
3. Verify data scoping
4. Update documentation

### Troubleshooting
- Check user roles and permissions
- Verify panel provider registration
- Test middleware configuration
- Review authentication guards
