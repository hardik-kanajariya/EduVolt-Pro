<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create Permissions
        $permissions = [
            // User Management Permissions
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',
            'manage_user_permissions',

            // Role & Permission Management
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'view_permissions',
            'create_permissions',
            'edit_permissions',
            'delete_permissions',
            'assign_permissions_to_roles',

            // School Management Permissions
            'view_schools',
            'create_schools',
            'edit_schools',
            'delete_schools',
            'manage_school_settings',
            'switch_schools',

            // Student Management Permissions
            'view_students',
            'create_students',
            'edit_students',
            'delete_students',
            'promote_students',
            'transfer_students',
            'bulk_import_students',
            'export_students',

            // Teacher Management Permissions
            'view_teachers',
            'create_teachers',
            'edit_teachers',
            'delete_teachers',
            'assign_subjects',
            'assign_classes',

            // Staff Management Permissions
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff',

            // Class Management Permissions
            'view_classes',
            'create_classes',
            'edit_classes',
            'delete_classes',
            'manage_class_sections',
            'assign_class_teachers',

            // Subject Management Permissions
            'view_subjects',
            'create_subjects',
            'edit_subjects',
            'delete_subjects',

            // Attendance Management Permissions
            'view_attendance',
            'mark_attendance',
            'edit_attendance',
            'view_attendance_reports',
            'bulk_attendance_operations',

            // Fee Management Permissions
            'view_fees',
            'collect_fees',
            'manage_fee_structure',
            'view_fee_reports',
            'issue_receipts',
            'manage_fee_categories',
            'fee_waivers',

            // Exam Management Permissions
            'view_exams',
            'create_exams',
            'edit_exams',
            'delete_exams',
            'manage_marks',
            'publish_results',
            'generate_mark_sheets',

            // Library Management Permissions
            'view_library',
            'manage_books',
            'issue_books',
            'return_books',
            'manage_library_inventory',
            'library_reports',

            // Academic Management Permissions
            'view_academic_progress',
            'manage_assignments',
            'manage_timetable',
            'generate_reports',
            'academic_calendar',

            // Communication Permissions
            'send_notifications',
            'manage_announcements',
            'send_emails',
            'manage_events',
            'bulk_communications',

            // System Administration Permissions
            'view_system_logs',
            'manage_academic_years',
            'backup_restore',
            'system_settings',

            // Super Admin Specific Permissions
            'manage_multiple_schools',
            'view_global_finances',
            'manage_global_finances',
            'manage_payment_gateways',
            'manage_sms_gateways',
            'manage_global_settings',
            'view_system_analytics',
            'manage_subscriptions',
            'manage_super_admins',
            'view_all_schools_data',
            'export_system_data',
            'manage_system_templates',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create Roles and assign permissions

        // Super Admin - Has all permissions (system-wide)
        $superAdmin = Role::firstOrCreate(['name' => 'super_admin']);
        $superAdmin->syncPermissions(Permission::all());

        // School Admin - School-specific administration permissions
        $schoolAdmin = Role::create(['name' => 'school_admin']);
        $schoolAdmin->givePermissionTo([
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',
            'assign_roles',
            'view_roles',
            'create_roles',
            'edit_roles',
            'delete_roles',
            'view_permissions',
            'assign_permissions_to_roles',
            'view_students',
            'create_students',
            'edit_students',
            'delete_students',
            'promote_students',
            'transfer_students',
            'bulk_import_students',
            'export_students',
            'view_teachers',
            'create_teachers',
            'edit_teachers',
            'delete_teachers',
            'assign_subjects',
            'assign_classes',
            'view_staff',
            'create_staff',
            'edit_staff',
            'delete_staff',
            'view_classes',
            'create_classes',
            'edit_classes',
            'delete_classes',
            'manage_class_sections',
            'assign_class_teachers',
            'view_subjects',
            'create_subjects',
            'edit_subjects',
            'delete_subjects',
            'view_attendance',
            'view_attendance_reports',
            'bulk_attendance_operations',
            'view_fees',
            'collect_fees',
            'manage_fee_structure',
            'view_fee_reports',
            'issue_receipts',
            'manage_fee_categories',
            'fee_waivers',
            'view_exams',
            'create_exams',
            'edit_exams',
            'delete_exams',
            'manage_marks',
            'publish_results',
            'generate_mark_sheets',
            'view_library',
            'manage_books',
            'issue_books',
            'return_books',
            'manage_library_inventory',
            'library_reports',
            'view_academic_progress',
            'manage_assignments',
            'manage_timetable',
            'generate_reports',
            'academic_calendar',
            'send_notifications',
            'manage_announcements',
            'send_emails',
            'manage_events',
            'bulk_communications',
            'manage_school_settings',
            'manage_academic_years',
        ]);

        // Admin - Legacy role (can be used for multi-school admin)
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'view_users',
            'view_students',
            'view_teachers',
            'view_classes',
            'view_attendance',
            'view_attendance_reports',
            'view_fees',
            'view_fee_reports',
            'view_exams',
            'view_library',
            'view_academic_progress',
            'generate_reports',
            'send_notifications',
            'manage_announcements',
        ]);

        // Principal - Academic oversight permissions
        $principal = Role::create(['name' => 'principal']);
        $principal->givePermissionTo([
            'view_users',
            'view_students',
            'view_teachers',
            'view_classes',
            'view_attendance',
            'view_attendance_reports',
            'view_fees',
            'view_fee_reports',
            'view_exams',
            'manage_marks',
            'publish_results',
            'view_library',
            'view_academic_progress',
            'generate_reports',
            'send_notifications',
            'manage_announcements',
            'send_emails',
            'manage_events',
        ]);

        // Teacher - Teaching staff permissions
        $teacher = Role::create(['name' => 'teacher']);
        $teacher->givePermissionTo([
            'view_students',
            'view_classes',
            'view_attendance',
            'mark_attendance',
            'edit_attendance',
            'view_exams',
            'manage_marks',
            'view_academic_progress',
            'manage_assignments',
            'send_notifications',
        ]);

        // Accountant - Financial management permissions
        $accountant = Role::create(['name' => 'accountant']);
        $accountant->givePermissionTo([
            'view_students',
            'view_fees',
            'collect_fees',
            'manage_fee_structure',
            'view_fee_reports',
            'issue_receipts',
        ]);

        // Librarian - Library management permissions
        $librarian = Role::create(['name' => 'librarian']);
        $librarian->givePermissionTo([
            'view_students',
            'view_library',
            'manage_books',
            'issue_books',
            'return_books',
            'manage_library_inventory',
        ]);

        // Student - Student access permissions
        $student = Role::create(['name' => 'student']);
        $student->givePermissionTo([
            'view_attendance',
            'view_academic_progress',
        ]);

        // Parent - Parent portal permissions
        $parent = Role::create(['name' => 'parent']);
        $parent->givePermissionTo([
            'view_attendance',
            'view_academic_progress',
            'view_fees',
        ]);

        $this->command->info('Roles and permissions created successfully!');
        $this->command->info('Created ' . count($permissions) . ' permissions');
        $this->command->info('Created 8 roles: super_admin, school_admin, admin, principal, teacher, accountant, librarian, student, parent');
        $this->command->info('Note: school_admin role has been added for school-specific administration');
    }
}
