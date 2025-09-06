# EduVolt Pro - AI Copilot Development Instructions

## 🎯 Project Overview & Context

You are developing **EduVolt Pro v1.0**, an advanced school management system using Laravel 11 and Filament v3. This is a comprehensive educational platform with role-based access for administrators, faculty, students, and parents.

**Critical Version Constraints:**
- **v1.0 Features**: Cash payments, SMTP email, push notifications, English only
- **v2.0 Features** (DO NOT IMPLEMENT): SMS/WhatsApp, payment gateways, multilingual support, transport management

## 🏛️ Multi-Panel Architecture Overview

EduVolt Pro uses a sophisticated **5-panel Filament architecture** for role-based access:

1. **Admin Panel** (`/admin`) - Super admins, multi-school management
2. **School Panel** (`/school`) - School-specific administration  
3. **Faculty Panel** (`/faculty`) - Teachers, staff, librarians
4. **Student Panel** (`/student`) - Student dashboard and services
5. **Parent Panel** (`/parent`) - Parent monitoring and communication

**Panel Isolation**: Each panel has independent authentication, middleware, resources, and navigation. Users can only access panels appropriate to their role.

## 🏗️ Architecture & Technology Stack

### **Primary Technologies (MUST USE)**
- **Backend**: PHP 8.2+, Laravel 11
- **Admin Interface**: Filament v3 (Multi-panel approach)
- **Frontend**: Tailwind CSS, Blade templates
- **Database**: MySQL 8.0 with utf8mb4 charset
- **Authentication**: Spatie Laravel Permission for role-based access
- **Communication**: SMTP email + Browser push notifications
- **Testing**: Pest PHP
- **Documentation**: Custom docs system

### **File Structure Conventions**
```
app/
├── Models/ (Eloquent models with relationships)
├── Http/Controllers/
│   └── Auth/ (Authentication controllers)
├── Filament/
│   ├── Admin/Resources/ (Admin panel resources)
│   ├── Faculty/Resources/ (Faculty panel resources)
│   ├── Student/Resources/ (Student panel resources)
│   └── Parent/Resources/ (Parent panel resources)
├── Services/ (Business logic services)
└── Policies/ (Authorization policies)

resources/views/
├── layouts/ (Main layouts)
├── pages/ (Static pages)
├── components/ (Reusable components)
└── docs/ (Documentation views)

routes/
├── web.php (Web routes)
└── docs.php (Documentation routes)
```

## 🔐 Security & Authentication Architecture

### **Role-Based Access Control (Spatie Laravel Permission)**
```php
// 8 Core User Roles with specific school context isolation:
'super_admin'   => 'Multi-school system management (no school_id)'
'school_admin'  => 'Individual school administration'  
'principal'     => 'Academic oversight within school'
'teacher'       => 'Classroom management and instruction'
'accountant'    => 'Financial operations within school'
'librarian'     => 'Library management within school'
'student'       => 'Personal academic portal'
'parent'        => 'Multi-child monitoring'
```

### **Critical Security Patterns**
```php
// Panel Access Middleware - ALWAYS use custom middleware for each panel:
StudentMiddleware::class     // ->authMiddleware() for student panel
FacultyPanelAccess::class    // Role-based faculty access
SchoolPanelAccess::class     // School context validation

// School Context Isolation - CRITICAL for multi-school support:
User::where('school_id', auth()->user()->school_id)
// Super admins have school_id = null for cross-school access
```

## 🎨 Filament Development Patterns

### **Resource Organization Strategy**
```php
// Follow this exact structure for ALL resources:
app/Filament/{Panel}/Resources/{Module}/
├── {Module}Resource.php           // Main resource class
├── Pages/
│   ├── List{Module}s.php         // Listing page
│   ├── Create{Module}.php        // Creation page  
│   ├── Edit{Module}.php          // Edit page
│   └── View{Module}.php          // View page (optional)
├── Schemas/
│   └── {Module}Form.php          // Form schema (reusable)
├── Tables/
│   └── {Module}sTable.php        // Table configuration
└── RelationManagers/
    └── {Related}RelationManager.php

// Example: app/Filament/Admin/Resources/Schools/SchoolResource.php
```

### **Panel-Specific Development Rules**
```php
// Admin Panel - Multi-school management focus
->navigationGroups(['Multi-School Management', 'System Configuration'])
->discoverResources(in: app_path('Filament/Admin/Resources'))

// School Panel - Single school context, school-specific admin
->navigationGroups(['School Management', 'User Management', 'Academic Management'])
->authMiddleware(['school.panel.access']) // Custom middleware

// Faculty Panel - Teacher workflow optimization  
->navigationGroups(['My Classes', 'Students', 'Academic']) 
// ALWAYS filter by teacher's assigned classes/subjects

// Student/Parent Panels - Read-only focus with personal data
// NO create/edit capabilities, only view personal information
```

## 🗄️ Database Design Principles

### **Migration Standards**
```php
// ALWAYS follow these patterns:
- Use proper foreign key constraints with cascadeOnDelete()
- Add indexes for frequently queried columns
- Use enum types for status fields
- Include timestamps() on all tables
- Use json() for flexible data structures
- Add unique constraints where needed
- Include proper table comments
```

### **Model Relationships**
```php
// ALWAYS implement:
- Proper relationship methods (hasMany, belongsTo, etc.)
- Fillable attributes array
- Hidden attributes for sensitive data
- Casts for dates and JSON fields
- Accessors and mutators where needed
- Scopes for common queries
- Model observers for business logic
```

### **Required Database Relationships**
- School hasMany (AcademicYears, Classes, Students, Teachers, Staff)
- User polymorphic relationship with Student/Teacher/Staff
- Class belongsTo School, AcademicYear; hasMany Students
- Student belongsTo User, School, Class
- Attendance belongsTo Student, Class; pivot relationships

## 📝 Code Quality Standards

### **Laravel Best Practices**
```php
// ALWAYS follow:
- PSR-12 coding standards
- Descriptive variable and method names
- Single Responsibility Principle
- Repository pattern for complex queries
- Service classes for business logic
- Form Request classes for validation
- Resource classes for responses
- Consistent naming conventions
```

### **File Naming Conventions**
```php
// Models: PascalCase (User, SchoolClass, AcademicYear)
// Controllers: PascalCase + Controller (StudentController)
// Migrations: snake_case with descriptive names
// Tests: PascalCase + Test (StudentManagementTest)
// Seeders: PascalCase + Seeder (AcademicDataSeeder)
```

### **Method Naming Standards**
```php
// Use descriptive method names:
- markAttendance() not mark()
- calculateFeeTotal() not calculate()
- generateReportCard() not generate()
- sendNotificationToParents() not notify()
```

### **Test Development Strategy**
```php
// Run tests frequently but NEVER create test files via artisan:
// Instead of: php artisan make:test StudentManagementTest
// Do: Create manually in tests/Feature/{Panel}/

// Test structure mirrors panel structure:
tests/
├── Feature/
│   ├── Admin/           // Admin panel tests
│   ├── School/          // School panel tests  
│   ├── Faculty/         // Faculty panel tests
│   ├── Student/         // Student panel tests
│   └── Parent/          // Parent panel tests
├── Unit/
│   ├── Models/          // Model relationship tests
│   └── Services/        // Business logic tests
└── TestCase.php         // Base test class

// ALWAYS test role-based access in each panel test
```

## 📋 Critical Business Rules

### **School Context Isolation**
```php
// CRITICAL: All school-scoped data MUST be filtered by school_id
// Except super_admin users who have school_id = null

// Example queries that follow the pattern:
Student::where('school_id', auth()->user()->school_id)->get();
Teacher::where('school_id', auth()->user()->school_id)->get();

// Super admin exception:
if (auth()->user()->hasRole('super_admin')) {
    // Can access all schools
    Student::all();
} else {
    // School-scoped access
    Student::where('school_id', auth()->user()->school_id)->get();
}
```

## � Key Project Files & Conventions

### **Configuration Files (Reference Only)**
```php
config/permission.php          // Spatie permission configuration  
config/filament.php           // Filament global settings
database/seeders/RolePermissionSeeder.php  // 88 permissions, 8 roles
TESTING-GUIDE.md              // Comprehensive testing instructions
DEV.md                        // High-level development roadmap
```

### **Middleware Files (Critical for Panel Access)**
```php
app/Http/Middleware/
├── StudentMiddleware.php          // Student panel access control
├── EnsureFacultyPanelAccess.php  // Faculty role validation  
├── EnsureSchoolPanelAccess.php   // School admin validation
├── EnsureAdminPanelAccess.php    // Admin/super admin validation
└── EnsureParentPanelAccess.php   // Parent role validation
```

### **Panel Provider Configuration**
```php
// Each panel has specific color scheme and middleware:
AdminPanelProvider    => Color::Blue    + admin.panel.access
SchoolPanelProvider   => Color::Orange  + school.panel.access  
FacultyPanelProvider  => Color::Green   + faculty.panel.access
StudentPanelProvider  => Color::Purple  + StudentMiddleware
ParentPanelProvider   => Color::Orange  + parent.panel.access
```

## 🌱 Seeder Development Strategy

### **Seeder Requirements After Each Phase**
```php
// ALWAYS create seeders for:
- Test data for new features
- Demo data for presentations
- Development environment setup
- User acceptance testing
- Performance testing data
```

### **Seeder Naming Convention**
```php
// Use descriptive names:
- PhaseXCompletionSeeder (where X is phase number)
- AcademicDataSeeder
- UserRoleSeeder
- SampleStudentsSeeder
- TestingDataSeeder
```

## 🚀 Performance Optimization

### **Database Performance**
```php
// ALWAYS implement:
- Proper database indexes
- Eager loading for relationships
- Query caching for expensive operations
- Database connection pooling
- Optimized database queries
```

### **Application Performance**
```php
// ALWAYS consider:
- Redis caching for sessions
- File caching for static data
- Image optimization for uploads
- Lazy loading for large datasets
- Queue jobs for heavy operations
```

### **Faculty Access Control (Critical Pattern)**
```php
// Teachers can ONLY access students from their assigned classes
// Example in Faculty Panel StudentResource:

public static function getEloquentQuery(): Builder
{
    $query = parent::getEloquentQuery();
    
    if (!auth()->user()->hasRole(['principal', 'school_admin'])) {
        // Teacher can only see students from assigned classes
        $teacherClassIds = TeacherClass::where('teacher_id', auth()->id())
            ->pluck('class_id');
        $query->whereIn('class_id', $teacherClassIds);
    }
    
    return $query;
}

// This pattern applies to ALL faculty resources: attendance, assignments, etc.
```

### **Payment System (v1.0 Cash-Only)**
```php
// NEVER implement online payment gateways in v1.0
// Show this message for online payment attempts:
"Online payments will be available in v2.0. Please visit the school office for cash payment."

// Fee collection workflow:
1. Generate cash receipt with QR code
2. Track payment in cash_payments table  
3. Update student fee status
4. Send receipt to parent via email
```

## 📧 Communication System (v1.0 Features)

### **Allowed Communication Methods**
```php
// v1.0 Communication Rules:
- SMTP email system (required)
- Browser push notifications (required)
- In-app notifications (required)
- NO SMS integration
- NO WhatsApp integration
- NO voice calling features
```

## 🎨 Frontend Development Guidelines

### **Static Pages Requirements**
```html
<!-- ALWAYS implement:
- Mobile-first responsive design
- Tailwind CSS framework
- SEO optimization
- Accessibility compliance (WCAG 2.1 AA)
- Fast loading performance
- Clean, modern design -->
```

### **Required Static Pages**
- Landing page with hero section
- About page with system overview
- Contact page with form
- Terms of service
- Privacy policy
- Features breakdown
- Pricing information (if applicable)

## 🐛 Error Handling & Logging

### **Error Handling Standards**
```php
// ALWAYS implement:
- Try-catch blocks for external service calls
- Custom exception classes for business logic
- Proper error messages for users
- Detailed logging for debugging
- Graceful degradation for failures
```

### **Logging Requirements**
```php
// ALWAYS log:
- Authentication attempts
- Payment transactions
- Data modifications
- HTTP requests/responses
- System errors
- Security incidents
```

## � Development Workflow & Commands

### **DO NOT Run These Commands (User Manages)**
```bash
# NEVER run these - user handles server management:
php artisan serve
php artisan tinker  
systemctl restart nginx
sudo service apache2 restart

# NEVER run filesystem commands - ask user instead:
php artisan storage:link
chmod 777 storage/
```

### **File Creation Patterns (Manual Creation Required)**
```php
// NEVER use artisan make commands - create files manually:
// Instead of: php artisan make:filament-resource StudentResource
// Do: Create file manually in app/Filament/{Panel}/Resources/

// Follow exact naming conventions:
StudentResource.php           // PascalCase
CreateStudent.php            // PascalCase + Action
student_management_test.php  // snake_case for tests  
2024_01_01_create_students_table.php // migration naming
```

### **Git Commands (ONLY Allowed Commands)**
```bash
# These are the ONLY commands you can run:
git add .
git commit -m "feat: implement student attendance marking"
git push origin feature-branch
git status
git log --oneline
```

## ⚠️ Critical DO NOTs

### **v1.0 Restrictions**
- ❌ DO NOT implement SMS/WhatsApp notifications
- ❌ DO NOT integrate payment gateways
- ❌ DO NOT add multilingual support
- ❌ DO NOT implement transport management
- ❌ DO NOT add social media login
- ❌ DO NOT implement video conferencing

### **Development Restrictions**
- ❌ DO NOT use custom views where Filament can handle it
- ❌ DO NOT implement manual data import/export (use Filament built-in)
- ❌ DO NOT defer testing to the end (test alongside development)
- ❌ DO NOT skip documentation updates
- ❌ DO NOT ignore security best practices
- ❌ DO NOT bypass role-based access control

## 📋 Quality Gates

### **Before Moving to Next Phase**
```php
// MUST complete:
1. All planned features working correctly
2. Test suite passing with good coverage
3. Documentation updated and accurate
4. Seeders created and tested
5. Performance benchmarks met
6. Security audit passed
7. Code review completed
8. User acceptance criteria met
```

### **Performance Benchmarks**
- Page load time: < 3 seconds
- Database response: < 200ms
- Page response: < 500ms
- Memory usage: < 512MB per request
- Test execution: < 2 minutes for full suite

## 🎯 Success Metrics

### **Code Quality Metrics**
- Test coverage: > 80%
- Cyclomatic complexity: < 10
- Code duplication: < 5%
- PSR-12 compliance: 100%
- Security vulnerabilities: 0

### **User Experience Metrics**
- Mobile responsiveness: 100%
- Accessibility compliance: WCAG 2.1 AA
- Browser compatibility: Chrome, Firefox, Safari, Edge
- Load time: < 3 seconds on 3G connection

Remember: You are building a production-ready school management system that will be used by real schools. Prioritize security, performance, and user experience in every decision you make.

Note: we are in windows system, so do not run linux commands here, and Do not start or restart the server(php artisan serve) this will be managed by the user it self. 

if any file got currupted while you make changes do not fix this, inform user about this issue. and do continue with your works. I will take care of that file. 

do not try to run thus command `php artisan tinker` actually do not test anything what ever development is done ask user to verify and test. if any problems are there user will tell you or fix that by it self. 

run the test often to make sure all are passing and not any failures if failing then check why?, if its the issue with what you made chanegs or need to update that test case with new development. 

do not run commands for testing untill I explicitly told you. Do focus only on development activity. rest I will manage. 

Do not remove any file unless I explicitly told you to do so. 

you should not run the cmd commands to create migration, model or resource etc. just create those files in respective folders with proper naming conventions and proper code manually. 

we are using filament v3 so do not use any code or function or anything from filament v3. analyze the filament docs if you are not sure about anything.  
https://filamentphp.com/docs/4.x/introduction/overview

the dev.md file is containes only higher level requirements, a list of features only, we have to identify and analyze each in depth and create proper system. we should go beyond the expectations. 

Do not give me any prompt to run any commands, you are only allowed to run git commands if you want to commit only, no other even git commands.

# Filament Instructions 

## Filament Specific Instructions

When working with Filament, adhere to the following guidelines to ensure consistency and maintainability across the project:

1. **Resource Structure**: Each Filament resource should be organized within its respective panel directory (Admin, Faculty, Student, Parent) under `app/Filament/`. Ensure that resources are grouped logically based on their functionality and user roles.

2. **Form and Table Definitions**: Use Filament's form and table components to define the schema for each resource. Ensure that forms include appropriate validation rules, and tables have sortable and searchable columns where applicable.

3. **Navigation and Access Control**: Configure navigation items for each panel in the `getNavigationItems()` method of the respective panel service provider. Implement role-based access control using Spatie Laravel Permission to restrict access to resources based on user roles.

4. **Custom Actions and Bulk Actions**: Leverage Filament's action and bulk action features to provide users with the ability to perform operations on individual records or multiple records simultaneously. Ensure that these actions are clearly labeled and include confirmation prompts for destructive actions.

5. **Filters and Scopes**: Implement filters and scopes in Filament tables to allow users to easily narrow down records based on specific criteria. Use predefined scopes in Eloquent models to encapsulate common query logic.

6. **Dashboard Widgets**: Create custom dashboard widgets for each panel to provide users with relevant insights and quick access to important information. Ensure that widgets are optimized for performance and do not overload the dashboard.

7. **Theming and Customization**: Apply consistent theming across all Filament panels using Tailwind CSS. Customize the appearance of forms, tables, and other components to align with the overall design of the application.

