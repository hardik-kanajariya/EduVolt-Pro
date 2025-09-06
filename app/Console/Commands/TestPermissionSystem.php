<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\School;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class TestPermissionSystem extends Command
{
    protected $signature = 'test:permissions {--show-routes}';

    protected $description = 'Test the role-based permission system';

    public function handle()
    {
        $this->info('🔐 Testing EduVault Pro Permission System');
        $this->line('');

        // Test 1: Panel Route Registration
        $this->testPanelRoutes();

        // Test 2: User Role Assignments
        $this->testUserRoles();

        // Test 3: School Context
        $this->testSchoolContext();

        // Test 4: Permission Assignments
        $this->testPermissions();

        $this->line('');
        $this->info('✅ Permission system test completed!');

        if ($this->option('show-routes')) {
            $this->showPanelRoutes();
        }
    }

    private function testPanelRoutes()
    {
        $this->info('📍 Testing Panel Routes...');

        $expectedPanels = [
            'admin' => '/admin',
            'school' => '/school',
            'faculty' => '/faculty',
            'student' => '/student',
            'parent' => '/parent'
        ];

        $routes = Route::getRoutes();

        foreach ($expectedPanels as $panel => $path) {
            $found = false;
            foreach ($routes as $route) {
                if (str_contains($route->uri(), trim($path, '/'))) {
                    $found = true;
                    break;
                }
            }

            if ($found) {
                $this->line("  ✅ {$panel} panel routes registered");
            } else {
                $this->error("  ❌ {$panel} panel routes NOT found");
            }
        }
    }

    private function testUserRoles()
    {
        $this->info('👥 Testing User Roles...');

        $testUsers = [
            ['email' => 'super@admin.com', 'expected_role' => 'super_admin'],
            ['email' => 'school@admin.com', 'expected_role' => 'school_admin'],
            ['email' => 'principal@school.com', 'expected_role' => 'principal'],
            ['email' => 'teacher@school.com', 'expected_role' => 'teacher'],
            ['email' => 'student@school.com', 'expected_role' => 'student'],
            ['email' => 'parent@school.com', 'expected_role' => 'parent'],
            ['email' => 'accountant@school.com', 'expected_role' => 'accountant'],
        ];

        foreach ($testUsers as $userData) {
            $user = User::where('email', $userData['email'])->first();

            if ($user) {
                if ($user->hasRole($userData['expected_role'])) {
                    $this->line("  ✅ {$user->email} has role: {$userData['expected_role']}");
                } else {
                    $this->error("  ❌ {$user->email} missing role: {$userData['expected_role']}");
                }
            } else {
                $this->error("  ❌ User not found: {$userData['email']}");
            }
        }
    }

    private function testSchoolContext()
    {
        $this->info('🏫 Testing School Context...');

        $school = School::first();
        if (!$school) {
            $this->error('  ❌ No schools found in database');
            return;
        }

        $schoolUsers = User::where('school_id', $school->id)->count();
        $superAdmins = User::whereNull('school_id')->whereHas('roles', function ($q) {
            $q->where('name', 'super_admin');
        })->count();

        $this->line("  ✅ Found school: {$school->name}");
        $this->line("  ✅ School users: {$schoolUsers}");
        $this->line("  ✅ Super admins (no school): {$superAdmins}");
    }

    private function testPermissions()
    {
        $this->info('🔑 Testing Permissions...');

        $criticalPermissions = [
            'user_create',
            'user_read',
            'user_update',
            'user_delete',
            'role_create',
            'role_read',
            'role_update',
            'role_delete',
            'permission_create',
            'permission_read',
            'permission_update',
            'permission_delete',
            'school_create',
            'school_read',
            'school_update',
            'school_delete'
        ];

        foreach ($criticalPermissions as $permission) {
            $exists = \Spatie\Permission\Models\Permission::where('name', $permission)->exists();

            if ($exists) {
                $this->line("  ✅ Permission exists: {$permission}");
            } else {
                $this->error("  ❌ Permission missing: {$permission}");
            }
        }

        // Test super admin permissions
        $superAdmin = User::whereHas('roles', function ($q) {
            $q->where('name', 'super_admin');
        })->first();

        if ($superAdmin) {
            $permissionCount = $superAdmin->getAllPermissions()->count();
            $this->line("  ✅ Super admin has {$permissionCount} permissions");
        }
    }

    private function showPanelRoutes()
    {
        $this->line('');
        $this->info('📋 Panel Access URLs:');
        $this->line('');

        $panels = [
            'Super Admin Panel' => '/admin (super_admin only)',
            'School Admin Panel' => '/school (school_admin, principal, teacher, accountant, librarian)',
            'Faculty Panel' => '/faculty (teacher, principal, school_admin)',
            'Student Panel' => '/student (student only)',
            'Parent Panel' => '/parent (parent only)',
        ];

        foreach ($panels as $name => $access) {
            $this->line("  🔗 {$name}: {$access}");
        }

        $this->line('');
        $this->info('📝 Test User Accounts:');
        $this->line('  📧 super@admin.com / password - Super Admin');
        $this->line('  📧 school@admin.com / password - School Admin');
        $this->line('  📧 principal@school.com / password - Principal');
        $this->line('  📧 teacher@school.com / password - Teacher');
        $this->line('  📧 student@school.com / password - Student');
        $this->line('  📧 parent@school.com / password - Parent');
        $this->line('  📧 accountant@school.com / password - Accountant');
    }
}
