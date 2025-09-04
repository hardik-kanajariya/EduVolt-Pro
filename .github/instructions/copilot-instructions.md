# EduVault Pro - AI Copilot Development Instructions

## 🎯 Project Overview & Context

You are developing **EduVault Pro v1.0**, an advanced school management system using Laravel 11 and Filament v4. This is a comprehensive educational platform with role-based access for administrators, faculty, students, and parents.

**Critical Version Constraints:**
- **v1.0 Features**: Cash payments, SMTP email, push notifications, English only
- **v2.0 Features** (DO NOT IMPLEMENT): SMS/WhatsApp, payment gateways, multilingual support, transport management

## 🏗️ Architecture & Technology Stack

### **Primary Technologies (MUST USE)**
- **Backend**: PHP 8.2+, Laravel 11
- **Admin Interface**: Filament v4 (Multi-panel approach)
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

## 🔐 Security & Authentication Rules

### **User Roles & Permissions**
- **super_admin**: Full system access
- **admin**: School administration
- **principal**: Academic oversight  
- **teacher**: Teaching staff
- **accountant**: Financial management
- **librarian**: Library management
- **student**: Student access
- **parent**: Parent portal access

### **Security Implementation Requirements**
1. **ALWAYS** use Spatie Laravel Permission for role management
2. **ALWAYS** validate user input with Laravel Form Requests
3. **ALWAYS** implement authorization policies for each model
4. **NEVER** expose sensitive data in responses
5. **ALWAYS** use CSRF protection for forms
6. **ALWAYS** encrypt sensitive database fields

## 🎨 Filament Development Guidelines

### **Multi-Panel Strategy**
- **Admin Panel** (`/admin`): Super admin, admin, principal access
- **Faculty Panel** (`/faculty`): Teachers, staff, librarian access  
- **Student Panel** (`/student`): Student dashboard and features
- **Parent Panel** (`/parent`): Parent portal and monitoring

### **Filament Resource Standards**
```php
// ALWAYS include these features in resources:
- Proper form validation rules
- Role-based access control
- Search functionality
- Filters for common fields
- Bulk actions where appropriate
- Export functionality (built-in)
- Proper table columns with sortable fields
- Custom actions for specific workflows
```

### **Filament Panel Configuration**
```php
// Each panel MUST have:
- Custom authentication guard
- Role-based middleware
- Panel-specific navigation
- Dashboard widgets
- Custom color scheme
- Proper asset management
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

## 🧪 Testing Methodology

### **Test-Driven Development (TDD)**
```php
// ALWAYS write tests alongside development:
1. Write failing test first
2. Implement minimum code to pass
3. Refactor and improve
4. Repeat for each feature

// Test Categories (ALL REQUIRED):
- Unit tests for models and services
- Feature tests for user workflows  
- Feature tests for all functionality
- Browser tests for critical paths
- Integration tests for external services
```

### **Testing Standards**
```php
// EVERY feature MUST have:
- Model relationship tests
- CRUD operation tests
- Authorization tests
- Validation tests
- Business logic tests
- Feature tests
- Performance tests for critical operations
```

### **Test Structure**
```php
tests/
├── Unit/
│   ├── Models/ (Model tests)
│   └── Services/ (Service tests)
├── Feature/
│   ├── Admin/ (Admin panel tests)
│   ├── Faculty/ (Faculty panel tests)  
│   ├── Student/ (Student panel tests)
│   └── Parent/ (Parent panel tests)
├── Feature/ (Feature tests)
└── Browser/ (Dusk browser tests)
```

## 📚 Documentation Requirements

### **Side-by-Side Documentation**
```markdown
// ALWAYS document while developing:
1. Code comments for complex logic
2. User documentation with examples
3. User guides for each feature
4. Technical documentation for developers
5. Installation and setup guides
6. Troubleshooting guides
```

### **Documentation Structure**
```
resources/views/docs/
├──────── installation/ (Setup guides)
├──────── user-guides/ (Role-specific guides)
├──────── user-guides/ (User documentation)
├──────── database/ (Schema documentation)
├──────── deployment/ (Production setup)
├──────── testing/ (Testing procedures)
└──────── troubleshooting/ (Common issues)
```

### **Documentation Standards**
- Use clear, concise language
- Include code examples
- Provide step-by-step instructions
- Include validation steps
- Document error scenarios

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

##  Payment System (v1.0 Constraints)

### **Cash-Only Implementation**
```php
// v1.0 Payment Rules:
- ONLY cash payment collection
- Generate cash receipts with QR codes
- Track payment history
- Calculate late fees automatically
- NO online payment gateways
- NO credit card processing
- Show "Visit School Office" for online payments
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

## 🔄 Development Workflow

### **Phase Completion Checklist**
```markdown
For EACH phase completion:
☐ All features implemented and tested
☐ Tests written and passing (min 80% coverage)
☐ Documentation updated
☐ Seeders created for testing
☐ Code reviewed and refactored
☐ Performance optimized
☐ Security validated
☐ User acceptance testing completed
```

### **Git Workflow**
```bash
# Branch naming convention:
- feature/phase-1-foundation
- feature/attendance-system
- bugfix/login-issue
- hotfix/security-patch

# Commit message format:
feat: implement student attendance marking
fix: resolve login redirect issue
docs: update user documentation
test: add attendance marking tests
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

we are using filament v4 so do not use any code or function or anything from filament v3. analyze the filament docs if you are not sure about anything.  
https://filamentphp.com/docs/4.x/introduction/overview

the dev.md file is containes only higher level requirements, a list of features only, we have to identify and analyze each in depth and create proper system. we should go beyond the expectations. 