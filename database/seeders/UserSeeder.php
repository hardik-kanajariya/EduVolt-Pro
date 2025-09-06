<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\School;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first school for demo purposes
        $demoSchool = School::first();

        // Create Super Admin User (no school association - can access all schools)
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'school_id' => null, // Super admin not tied to specific school
        ]);

        // Create School Administrator (tied to demo school)
        $schoolAdmin = User::create([
            'name' => 'School Administrator',
            'email' => 'schooladmin@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'school_id' => $demoSchool->id,
        ]);

        // Create additional test users for different roles (all tied to demo school)
        $admin = User::create([
            'name' => 'General Administrator',
            'email' => 'generaladmin@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
            'school_id' => $demoSchool->id,
        ]);

        $principal = User::create([
            'name' => 'Principal',
            'email' => 'principal@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('principal123'),
            'school_id' => $demoSchool->id,
        ]);

        $teacher = User::create([
            'name' => 'John Teacher',
            'email' => 'teacher@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('teacher123'),
            'school_id' => $demoSchool->id,
        ]);

        $student = User::create([
            'name' => 'Jane Student',
            'email' => 'student@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('student123'),
            'school_id' => $demoSchool->id,
        ]);

        $parent = User::create([
            'name' => 'Parent Smith',
            'email' => 'parent@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('parent123'),
            'school_id' => $demoSchool->id,
        ]);

        // Assign roles to users
        $superAdmin->assignRole('super_admin');
        $schoolAdmin->assignRole('school_admin');
        $admin->assignRole('admin');
        $principal->assignRole('principal');
        $teacher->assignRole('teacher');
        $student->assignRole('student');
        $parent->assignRole('parent');

        $this->command->info('Users created successfully with school associations!');
        $this->command->info('Super Admin: admin@eduvaultpro.com / admin123 (All Schools Access)');
        $this->command->info('School Admin: schooladmin@eduvaultpro.com / admin123 (School: ' . $demoSchool->name . ')');
        $this->command->info('General Admin: generaladmin@eduvaultpro.com / admin123 (School: ' . $demoSchool->name . ')');
        $this->command->info('Principal: principal@eduvaultpro.com / principal123 (School: ' . $demoSchool->name . ')');
        $this->command->info('Teacher: teacher@eduvaultpro.com / teacher123 (School: ' . $demoSchool->name . ')');
        $this->command->info('Student: student@eduvaultpro.com / student123 (School: ' . $demoSchool->name . ')');
        $this->command->info('Parent: parent@eduvaultpro.com / parent123 (School: ' . $demoSchool->name . ')');
    }
}
