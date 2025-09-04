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

            // Student Management Permissions
            'view_students',
            'create_students',
            'edit_students',
            'delete_students',
            'promote_students',
            'transfer_students',

            // Teacher Management Permissions
            'view_teachers',
            'create_teachers',
            'edit_teachers',
            'delete_teachers',
            'assign_subjects',

            // Class Management Permissions
            'view_classes',
            'create_classes',
            'edit_classes',
            'delete_classes',
            'manage_class_sections',

            // Attendance Management Permissions
            'view_attendance',
            'mark_attendance',
            'edit_attendance',
            'view_attendance_reports',

            // Fee Management Permissions
            'view_fees',
            'collect_fees',
            'manage_fee_structure',
            'view_fee_reports',
            'issue_receipts',

            // Exam Management Permissions
            'view_exams',
            'create_exams',
            'edit_exams',
            'delete_exams',
            'manage_marks',
            'publish_results',

            // Library Management Permissions
            'view_library',
            'manage_books',
            'issue_books',
            'return_books',
            'manage_library_inventory',

            // Academic Management Permissions
            'view_academic_progress',
            'manage_assignments',
            'manage_timetable',
            'generate_reports',

            // Communication Permissions
            'send_notifications',
            'manage_announcements',
            'send_emails',
            'manage_events',

            // System Administration Permissions
            'manage_school_settings',
            'view_system_logs',
            'manage_academic_years',
            'backup_restore',
            'manage_roles_permissions',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create Roles and assign permissions

        // Super Admin - Has all permissions
        $superAdmin = Role::create(['name' => 'super_admin']);
        $superAdmin->givePermissionTo(Permission::all());

        // Admin - School administration permissions
        $admin = Role::create(['name' => 'admin']);
        $admin->givePermissionTo([
            'view_users',
            'create_users',
            'edit_users',
            'view_students',
            'create_students',
            'edit_students',
            'promote_students',
            'transfer_students',
            'view_teachers',
            'create_teachers',
            'edit_teachers',
            'assign_subjects',
            'view_classes',
            'create_classes',
            'edit_classes',
            'manage_class_sections',
            'view_attendance',
            'view_attendance_reports',
            'view_fees',
            'collect_fees',
            'manage_fee_structure',
            'view_fee_reports',
            'issue_receipts',
            'view_exams',
            'create_exams',
            'edit_exams',
            'manage_marks',
            'publish_results',
            'view_library',
            'manage_books',
            'issue_books',
            'return_books',
            'manage_library_inventory',
            'view_academic_progress',
            'manage_timetable',
            'generate_reports',
            'send_notifications',
            'manage_announcements',
            'send_emails',
            'manage_events',
            'manage_school_settings',
            'manage_academic_years',
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
        $this->command->info('Created 8 roles: super_admin, admin, principal, teacher, accountant, librarian, student, parent');
    }
}
