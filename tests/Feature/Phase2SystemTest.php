<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\School;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class Phase2SystemTest extends TestCase
{
    use RefreshDatabase;

    public function test_roles_and_permissions_exist(): void
    {
        $this->seed();

        // Test that all expected roles exist
        $expectedRoles = ['super_admin', 'admin', 'principal', 'teacher', 'accountant', 'librarian', 'student', 'parent'];
        foreach ($expectedRoles as $roleName) {
            $this->assertDatabaseHas('roles', ['name' => $roleName]);
        }

        // Test that permissions exist
        $this->assertDatabaseHas('permissions', ['name' => 'view_users']);
        $this->assertDatabaseHas('permissions', ['name' => 'manage_students']);
        $this->assertDatabaseHas('permissions', ['name' => 'view_attendance']);

        $this->assertTrue(true);
    }

    public function test_users_have_roles(): void
    {
        $this->seed();

        // Test that users exist and have correct roles
        $superAdmin = User::where('email', 'admin@eduvaultpro.com')->first();
        $this->assertNotNull($superAdmin);
        $this->assertTrue($superAdmin->hasRole('super_admin'));

        $teacher = User::where('email', 'teacher@eduvaultpro.com')->first();
        $this->assertNotNull($teacher);
        $this->assertTrue($teacher->hasRole('teacher'));
    }

    public function test_school_data_exists(): void
    {
        $this->seed();

        // Test school exists
        $school = School::first();
        $this->assertNotNull($school);
        $this->assertEquals('EduVault Demo School', $school->name);

        // Test academic year exists
        $academicYear = AcademicYear::first();
        $this->assertNotNull($academicYear);
        $this->assertTrue($academicYear->is_current);

        // Test classes exist
        $classes = SchoolClass::count();
        $this->assertGreaterThan(0, $classes);

        // Test subjects exist
        $subjects = Subject::count();
        $this->assertGreaterThan(0, $subjects);
    }

    public function test_model_relationships(): void
    {
        $this->seed();

        $school = School::first();
        $academicYear = $school->academicYears()->first();
        $class = $school->classes()->first();
        $subject = $school->subjects()->first();

        // Test relationships work
        $this->assertNotNull($academicYear);
        $this->assertNotNull($class);
        $this->assertNotNull($subject);

        // Test relationship data
        $this->assertEquals($school->id, $academicYear->school_id);
        $this->assertEquals($school->id, $class->school_id);
        $this->assertEquals($school->id, $subject->school_id);
    }

    public function test_user_model_has_roles_trait(): void
    {
        $this->seed();

        $user = User::first();
        
        // Test that HasRoles trait methods are available
        $this->assertTrue(method_exists($user, 'assignRole'));
        $this->assertTrue(method_exists($user, 'hasRole'));
        $this->assertTrue(method_exists($user, 'getRoleNames'));
    }
}
