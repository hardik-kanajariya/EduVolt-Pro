<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\AcademicYear;
use App\Models\SchoolClass;
use App\Models\Subject;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Sample School
        $school = School::create([
            'name' => 'EduVault Demo School',
            'code' => 'EVDS001',
            'address' => '123 Education Street, Learning City, State 12345',
            'phone' => '+91-9876-543-210',
            'email' => 'info@eduvaultdemo.edu',
            'website' => 'https://www.eduvaultdemo.edu',
            'established_date' => '2020-01-01',
            'status' => 'active',
            'settings' => [
                'timezone' => 'Asia/Kolkata',
                'currency' => 'INR',
                'academic_year_start' => '04-01',
                'academic_year_end' => '03-31',
            ]
        ]);

        // Create Academic Year
        $academicYear = AcademicYear::create([
            'school_id' => $school->id,
            'name' => '2024-2025',
            'start_date' => '2024-04-01',
            'end_date' => '2025-03-31',
            'is_current' => true,
            'status' => 'active',
        ]);

        // Create Classes
        $classes = [
            ['name' => 'Nursery', 'section' => 'A'],
            ['name' => 'LKG', 'section' => 'A'],
            ['name' => 'UKG', 'section' => 'A'],
            ['name' => 'Grade 1', 'section' => 'A'],
            ['name' => 'Grade 1', 'section' => 'B'],
            ['name' => 'Grade 2', 'section' => 'A'],
            ['name' => 'Grade 3', 'section' => 'A'],
            ['name' => 'Grade 4', 'section' => 'A'],
            ['name' => 'Grade 5', 'section' => 'A'],
            ['name' => 'Grade 6', 'section' => 'A'],
            ['name' => 'Grade 7', 'section' => 'A'],
            ['name' => 'Grade 8', 'section' => 'A'],
            ['name' => 'Grade 9', 'section' => 'A'],
            ['name' => 'Grade 10', 'section' => 'A'],
        ];

        foreach ($classes as $classData) {
            SchoolClass::create([
                'school_id' => $school->id,
                'academic_year_id' => $academicYear->id,
                'name' => $classData['name'],
                'section' => $classData['section'],
                'capacity' => 30,
                'status' => 'active',
            ]);
        }

        // Create Subjects
        $subjects = [
            ['name' => 'English', 'code' => 'ENG', 'type' => 'core'],
            ['name' => 'Mathematics', 'code' => 'MATH', 'type' => 'core'],
            ['name' => 'Science', 'code' => 'SCI', 'type' => 'core'],
            ['name' => 'Social Studies', 'code' => 'SST', 'type' => 'core'],
            ['name' => 'Hindi', 'code' => 'HIN', 'type' => 'core'],
            ['name' => 'Computer Science', 'code' => 'CS', 'type' => 'elective'],
            ['name' => 'Physical Education', 'code' => 'PE', 'type' => 'extra_curricular'],
            ['name' => 'Art & Craft', 'code' => 'ART', 'type' => 'extra_curricular'],
            ['name' => 'Music', 'code' => 'MUS', 'type' => 'extra_curricular'],
        ];

        foreach ($subjects as $subjectData) {
            Subject::create([
                'school_id' => $school->id,
                'name' => $subjectData['name'],
                'code' => $subjectData['code'],
                'type' => $subjectData['type'],
                'credits' => 1,
                'status' => 'active',
            ]);
        }

        $this->command->info('School data created successfully!');
        $this->command->info('School: ' . $school->name);
        $this->command->info('Academic Year: ' . $academicYear->name);
        $this->command->info('Classes: ' . count($classes));
        $this->command->info('Subjects: ' . count($subjects));
    }
}
