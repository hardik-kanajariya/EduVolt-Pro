<?php

namespace Database\Seeders;

use App\Models\AcademicYear;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Database\Seeder;

class ComprehensiveTestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('Creating comprehensive test data...');

        // Create main school (use existing or create new)
        $school = School::first() ?? School::factory()->active()->create([
            'name' => 'EduVault Demo School',
            'code' => 'EDS001',
            'email' => 'admin@eduvaultdemo.com',
            'phone' => '+91-9876543210',
            'address' => '123 Education Street, Learning City, Knowledge State - 123456',
            'website' => 'www.eduvaultdemo.com',
            'principal_name' => 'Dr. Jane Smith',
            'description' => 'A premier educational institution dedicated to excellence in learning and character development.',
        ]);

        $this->command->info("School created: {$school->name}");

        // Create current academic year
        $currentAcademicYear = AcademicYear::factory()->current()->create([
            'school_id' => $school->id,
            'name' => '2024-25',
            'start_date' => '2024-04-01',
            'end_date' => '2025-03-31',
        ]);

        $this->command->info("Academic year created: {$currentAcademicYear->name}");

        // Create subjects
        $subjects = [
            ['name' => 'Mathematics', 'code' => 'MATH', 'type' => 'core'],
            ['name' => 'English', 'code' => 'ENG', 'type' => 'core'],
            ['name' => 'Science', 'code' => 'SCI', 'type' => 'core'],
            ['name' => 'Social Studies', 'code' => 'SST', 'type' => 'core'],
            ['name' => 'Hindi', 'code' => 'HIN', 'type' => 'core'],
            ['name' => 'Computer Science', 'code' => 'CS', 'type' => 'elective'],
            ['name' => 'Physical Education', 'code' => 'PE', 'type' => 'optional'],
            ['name' => 'Art & Craft', 'code' => 'ART', 'type' => 'optional'],
        ];

        $createdSubjects = collect();
        foreach ($subjects as $subjectData) {
            $subject = Subject::factory()->active()->create([
                'school_id' => $school->id,
                'name' => $subjectData['name'],
                'code' => $subjectData['code'],
                'type' => $subjectData['type'],
                'credits' => $subjectData['type'] === 'core' ? 5 : 3,
                'description' => "Comprehensive {$subjectData['name']} curriculum for holistic development.",
            ]);
            $createdSubjects->push($subject);
        }

        $this->command->info('Subjects created: ' . $createdSubjects->count());

        // Create classes for grades 1-12
        $classes = [];
        for ($grade = 1; $grade <= 12; $grade++) {
            $sections = $grade <= 5 ? ['A', 'B'] : ['A', 'B', 'C'];

            foreach ($sections as $section) {
                $class = SchoolClass::factory()->active()->create([
                    'school_id' => $school->id,
                    'academic_year_id' => $currentAcademicYear->id,
                    'name' => $grade <= 10 ? "{$grade}th" : ($grade == 11 ? "11th" : "12th"),
                    'section' => $section,
                    'capacity' => 35,
                    'room_number' => "Room-{$grade}{$section}",
                ]);
                $classes[] = $class;
            }
        }

        $this->command->info('Classes created: ' . count($classes));

        // Create teachers with proper user accounts
        $teacherData = [
            ['name' => 'Prof. John Doe', 'email' => 'john.teacher@eduvault.com', 'specialization' => 'Mathematics'],
            ['name' => 'Dr. Mary Johnson', 'email' => 'mary.teacher@eduvault.com', 'specialization' => 'English'],
            ['name' => 'Mr. Robert Wilson', 'email' => 'robert.teacher@eduvault.com', 'specialization' => 'Science'],
            ['name' => 'Ms. Sarah Davis', 'email' => 'sarah.teacher@eduvault.com', 'specialization' => 'Social Studies'],
            ['name' => 'Mrs. Lisa Brown', 'email' => 'lisa.teacher@eduvault.com', 'specialization' => 'Hindi'],
            ['name' => 'Mr. David Miller', 'email' => 'david.teacher@eduvault.com', 'specialization' => 'Computer Science'],
            ['name' => 'Coach Mike Taylor', 'email' => 'mike.teacher@eduvault.com', 'specialization' => 'Physical Education'],
            ['name' => 'Ms. Emma Wilson', 'email' => 'emma.teacher@eduvault.com', 'specialization' => 'Art & Craft'],
        ];

        $teachers = collect();
        foreach ($teacherData as $data) {
            // Check if user already exists
            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                // Create user account
                $user = User::factory()->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'status' => true,
                    'phone' => fake()->phoneNumber(),
                ]);

                // Assign teacher role
                $user->assignRole('teacher');
            }

            // Check if teacher profile already exists
            $teacher = Teacher::where('user_id', $user->id)->first();

            if (!$teacher) {
                // Create teacher profile
                $teacher = Teacher::factory()->active()->create([
                    'user_id' => $user->id,
                    'school_id' => $school->id,
                    'employee_id' => 'TCH' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'specialization' => $data['specialization'],
                    'qualification' => fake()->randomElement(['M.Ed', 'B.Ed', 'M.A', 'M.Sc']),
                    'experience_years' => fake()->numberBetween(2, 15),
                    'employment_type' => 'full_time',
                    'salary' => fake()->numberBetween(35000, 65000),
                ]);
            }

            $teachers->push($teacher);
        }

        $this->command->info('Teachers created: ' . $teachers->count());

        // Assign subjects to teachers
        foreach ($teachers as $index => $teacher) {
            $relatedSubjects = $createdSubjects->filter(function ($subject) use ($teacher) {
                return stripos($subject->name, $teacher->specialization) !== false ||
                    stripos($teacher->specialization, $subject->name) !== false;
            });

            if ($relatedSubjects->isEmpty()) {
                $relatedSubjects = $createdSubjects->random(1);
            }

            $teacher->subjects()->attach($relatedSubjects->pluck('id'));
        }

        // Assign class teachers
        foreach ($classes as $index => $class) {
            if ($index < $teachers->count()) {
                $class->update(['class_teacher_id' => $teachers[$index]->id]);
            }
        }

        // Create staff members
        $staffData = [
            ['name' => 'Mr. Admin User', 'email' => 'admin.staff@eduvault.com', 'department' => 'Administration', 'position' => 'Administrator'],
            ['name' => 'Mrs. Finance Manager', 'email' => 'finance@eduvault.com', 'department' => 'Accounts', 'position' => 'Accountant'],
            ['name' => 'Ms. Library Chief', 'email' => 'librarian@eduvault.com', 'department' => 'Library', 'position' => 'Librarian'],
            ['name' => 'Mr. Lab Assistant', 'email' => 'lab@eduvault.com', 'department' => 'Laboratory', 'position' => 'Lab Assistant'],
        ];

        foreach ($staffData as $data) {
            // Check if user already exists
            $user = User::where('email', $data['email'])->first();

            if (!$user) {
                $user = User::factory()->create([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'status' => true,
                ]);
            }

            // Assign appropriate role if not already assigned
            $role = match ($data['department']) {
                'Library' => 'librarian',
                'Accounts' => 'accountant',
                'Administration' => 'admin',
                default => 'admin' // Use admin role for other staff
            };

            if (!$user->hasRole($role)) {
                $user->assignRole($role);
            }

            // Check if staff profile already exists
            $staff = Staff::where('user_id', $user->id)->first();

            if (!$staff) {
                Staff::factory()->active()->create([
                    'user_id' => $user->id,
                    'school_id' => $school->id,
                    'employee_id' => 'STF' . str_pad($user->id, 4, '0', STR_PAD_LEFT),
                    'department' => $data['department'],
                    'position' => $data['position'],
                    'employment_type' => 'full_time',
                    'responsibilities' => [
                        'Primary responsibility for ' . $data['department'],
                        'Support school operations and administration'
                    ],
                    'salary' => fake()->numberBetween(20000, 45000),
                ]);
            }
        }

        $this->command->info('Staff created: ' . count($staffData));

        // Create students for each class
        $totalStudents = 0;
        $admissionCounter = Student::max('id') ?? 0; // Get the highest existing ID

        foreach ($classes as $class) {
            $studentsInClass = fake()->numberBetween(15, 30);

            for ($i = 1; $i <= $studentsInClass; $i++) {
                $admissionCounter++; // Increment for unique admission number

                $user = User::factory()->create([
                    'name' => fake()->name(),
                    'email' => fake()->unique()->email(),
                    'status' => true,
                    'date_of_birth' => fake()->dateTimeBetween('-18 years', '-5 years'),
                    'gender' => fake()->randomElement(['male', 'female']),
                ]);

                $user->assignRole('student');

                Student::factory()->active()->create([
                    'user_id' => $user->id,
                    'school_id' => $school->id,
                    'class_id' => $class->id,
                    'admission_number' => 'STU' . date('Y') . str_pad($admissionCounter, 4, '0', STR_PAD_LEFT),
                    'roll_number' => $i,
                    'admission_date' => fake()->dateTimeBetween('-3 years', 'now'),
                    'parent_email' => fake()->email(),
                ]);
            }

            $totalStudents += $studentsInClass;
        }

        $this->command->info("Students created: {$totalStudents}");

        // Create parent users for some students
        $students = Student::with('user')->take(50)->get();
        foreach ($students as $student) {
            if (fake()->boolean(70)) { // 70% chance of having parent account
                $parentUser = User::factory()->create([
                    'name' => $student->parent_name,
                    'email' => $student->parent_email,
                    'status' => true,
                    'phone' => $student->parent_phone,
                ]);

                $parentUser->assignRole('parent');
            }
        }

        $this->command->info('Parent accounts created for some students');

        // Attach subjects to classes
        foreach ($classes as $class) {
            $grade = (int) filter_var($class->name, FILTER_SANITIZE_NUMBER_INT);

            // Core subjects for all grades
            $coreSubjects = $createdSubjects->where('type', 'core');
            $class->subjects()->attach($coreSubjects->pluck('id'));

            // Add elective subjects for higher grades
            if ($grade >= 9) {
                $electiveSubjects = $createdSubjects->where('type', 'elective');
                $class->subjects()->attach($electiveSubjects->pluck('id'));
            }

            // Add optional subjects
            $optionalSubjects = $createdSubjects->where('type', 'optional');
            $class->subjects()->attach($optionalSubjects->random(2)->pluck('id'));
        }

        $this->command->info('Subjects attached to classes');

        $this->command->info('✅ Comprehensive test data seeding completed successfully!');
        $this->command->info("📊 Summary:");
        $this->command->info("   - Schools: " . School::count());
        $this->command->info("   - Academic Years: " . AcademicYear::count());
        $this->command->info("   - Classes: " . SchoolClass::count());
        $this->command->info("   - Subjects: " . Subject::count());
        $this->command->info("   - Teachers: " . Teacher::count());
        $this->command->info("   - Staff: " . Staff::count());
        $this->command->info("   - Students: " . Student::count());
        $this->command->info("   - Users: " . User::count());
    }
}
