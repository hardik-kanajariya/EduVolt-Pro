# EduVolt Pro - AI Development Instructions

## 🎯 Project Context
Advanced school management system using **Laravel 11** + **Filament v3** with role-based multi-panel architecture for educational institutions.

**v1.0 Scope**: Cash payments, SMTP email, push notifications, English only  
**Prohibited**: SMS/WhatsApp, payment gateways, multilingual, transport (v2.0 features)

## 🏛️ Multi-Panel Architecture (Core Knowledge)

**5-Panel Filament System** with strict role isolation:
1. **Admin** (`/admin`) - Super admins, multi-school management
2. **School** (`/school`) - School-specific administration  
3. **Faculty** (`/faculty`) - Teachers, staff, librarians
4. **Student** (`/student`) - Personal academic portal
5. **Parent** (`/parent`) - Multi-child monitoring

**Critical Pattern**: Each panel has independent auth, middleware, resources, navigation. Users access only their role-appropriate panel.

## 🔐 Security Architecture (Essential)

**8 User Roles** with **school context isolation**:
```php
// school_id determines data access scope (null = super_admin)
'super_admin'   => 'Cross-school system management (school_id: null)'
'school_admin'  => 'Individual school administration'  
'principal'     => 'Academic oversight within school'
'teacher'       => 'Classroom management (assigned classes only)'
'accountant'    => 'Financial operations within school'
'librarian'     => 'Library management within school'
'student'       => 'Personal academic data'
'parent'        => 'Children monitoring'
```

**Data Scoping Pattern** (CRITICAL):
```php
// ALWAYS filter by school context except super_admin
Student::where('school_id', auth()->user()->school_id)->get();

// Faculty access control example:
if (!auth()->user()->hasRole(['principal', 'school_admin'])) {
    $teacherClassIds = TeacherClass::where('teacher_id', auth()->id())->pluck('class_id');
    $query->whereIn('class_id', $teacherClassIds);
}
```

## 🎨 Filament Resource Structure (Follow Exactly)

```
app/Filament/{Panel}/Resources/{Module}/
├── {Module}Resource.php           // Main resource
├── Pages/
│   ├── List{Module}s.php         
│   ├── Create{Module}.php        
│   ├── Edit{Module}.php          
│   └── View{Module}.php          
├── Schemas/
│   └── {Module}Form.php          // Reusable form schema
├── Tables/
│   └── {Module}sTable.php        // Table configuration
└── RelationManagers/
    └── {Related}RelationManager.php
```

**Panel-Specific Rules**:
- **Admin**: Multi-school management, navigation groups: `['Multi-School Management', 'System Configuration']`
- **School**: Single school context, middleware: `'school.panel.access'`
- **Faculty**: Teacher workflow optimization, ALWAYS filter by assigned classes/subjects
- **Student/Parent**: Read-only focus, NO create/edit capabilities

## 🔧 Development Workflow (Critical Constraints)

**NEVER Run** (User managed):
```bash
php artisan serve|tinker
systemctl restart nginx
php artisan storage:link
```

**Manual File Creation** (NO artisan make commands):
```php
// Instead of: php artisan make:filament-resource StudentResource
// Do: Create manually in app/Filament/{Panel}/Resources/
```

**Only Allowed Commands**:
```bash
git add|commit|push|status|log
# All other commands require user approval
```

## 📁 Key Reference Files

**Critical Configuration**:
- `database/seeders/RolePermissionSeeder.php` - 88 permissions, 8 roles
- `TESTING-GUIDE.md` - Comprehensive role-based testing
- `app/Http/Middleware/` - Panel access control middleware
- `config/permission.php` - Spatie permission config

**Panel Providers** (Color coding):
- AdminPanelProvider → Blue + `admin.panel.access`
- SchoolPanelProvider → Orange + `school.panel.access`  
- FacultyPanelProvider → Green + `faculty.panel.access`
- StudentPanelProvider → Purple + `StudentMiddleware`
- ParentPanelProvider → Orange + `parent.panel.access`

## 💡 Business Logic Patterns

**School Context Isolation**:
```php
// All school-scoped queries MUST follow this pattern
User::where('school_id', auth()->user()->school_id)
// Exception: super_admin has school_id = null
```

**Faculty Access Control**:
```php
// Teachers see only their assigned classes/subjects
$teacherClasses = TeacherClass::where('teacher_id', auth()->id())->pluck('class_id');
Student::whereIn('class_id', $teacherClasses)->get();
```

**v1.0 Payment Constraint**:
```php
// Cash-only system, show for online payment attempts:
"Online payments available in v2.0. Visit school office for cash payment."
```

## 🧪 Testing Strategy

**Test Structure** (mirrors panel architecture):
```
tests/Feature/{Admin,School,Faculty,Student,Parent}/
tests/Unit/{Models,Services}/
```

**Always Test**: Role-based access, school context isolation, panel restrictions

Remember: This is a production educational system prioritizing security, performance, and role-based access control.
