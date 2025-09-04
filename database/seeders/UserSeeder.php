<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin User
        $superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'admin@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
        ]);

        // Create additional test users for different roles
        $admin = User::create([
            'name' => 'School Administrator',
            'email' => 'schooladmin@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin123'),
        ]);

        $principal = User::create([
            'name' => 'Principal',
            'email' => 'principal@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('principal123'),
        ]);

        $teacher = User::create([
            'name' => 'John Teacher',
            'email' => 'teacher@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('teacher123'),
        ]);

        $student = User::create([
            'name' => 'Jane Student',
            'email' => 'student@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('student123'),
        ]);

        $parent = User::create([
            'name' => 'Parent Smith',
            'email' => 'parent@eduvaultpro.com',
            'email_verified_at' => now(),
            'password' => Hash::make('parent123'),
        ]);

        $this->command->info('Users created successfully!');
        $this->command->info('Super Admin: admin@eduvaultpro.com / admin123');
        $this->command->info('School Admin: schooladmin@eduvaultpro.com / admin123');
        $this->command->info('Principal: principal@eduvaultpro.com / principal123');
        $this->command->info('Teacher: teacher@eduvaultpro.com / teacher123');
        $this->command->info('Student: student@eduvaultpro.com / student123');
        $this->command->info('Parent: parent@eduvaultpro.com / parent123');
    }
}
