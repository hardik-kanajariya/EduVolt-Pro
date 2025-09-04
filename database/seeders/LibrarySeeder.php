<?php

namespace Database\Seeders;

use App\Models\BookCategory;
use App\Models\LibraryBook;
use App\Models\BookIssue;
use App\Models\BookReservation;
use App\Models\LibraryFine;
use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LibrarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get the first school (assuming it exists)
        $school = School::first();
        if (!$school) {
            $this->command->warn('No school found. Please seed schools first.');
            return;
        }

        // Create book categories only if they don't exist
        $categories = [
            [
                'name' => 'Fiction',
                'code' => 'FICTION',
                'description' => 'Novels, short stories, and fictional literature',
                'color' => '#3B82F6',
                'sort_order' => 1,
            ],
            [
                'name' => 'Non-Fiction',
                'code' => 'NON_FICTION',
                'description' => 'Biographies, history, science, and factual books',
                'color' => '#059669',
                'sort_order' => 2,
            ],
            [
                'name' => 'Academic',
                'code' => 'ACADEMIC',
                'description' => 'Textbooks and educational materials',
                'color' => '#DC2626',
                'sort_order' => 3,
            ],
            [
                'name' => 'Reference',
                'code' => 'REFERENCE',
                'description' => 'Dictionaries, encyclopedias, and reference materials',
                'color' => '#7C3AED',
                'sort_order' => 4,
            ],
            [
                'name' => 'Children',
                'code' => 'CHILDREN',
                'description' => 'Picture books and children\'s literature',
                'color' => '#F59E0B',
                'sort_order' => 5,
            ],
        ];

        foreach ($categories as $categoryData) {
            BookCategory::firstOrCreate(
                [
                    'school_id' => $school->id,
                    'code' => $categoryData['code']
                ],
                array_merge($categoryData, ['school_id' => $school->id, 'is_active' => true])
            );
        }

        // Create sample books only if they don't exist
        $booksData = [
            [
                'title' => 'To Kill a Mockingbird',
                'author' => 'Harper Lee',
                'isbn' => '9780061120084',
                'publisher' => 'HarperCollins',
                'publication_year' => 1960,
                'category_code' => 'FICTION',
                'total_copies' => 5,
            ],
            [
                'title' => 'The Great Gatsby',
                'author' => 'F. Scott Fitzgerald',
                'isbn' => '9780743273565',
                'publisher' => 'Scribner',
                'publication_year' => 1925,
                'category_code' => 'FICTION',
                'total_copies' => 3,
            ],
            [
                'title' => 'A Brief History of Time',
                'author' => 'Stephen Hawking',
                'isbn' => '9780553380163',
                'publisher' => 'Bantam',
                'publication_year' => 1988,
                'category_code' => 'NON_FICTION',
                'total_copies' => 4,
            ],
            [
                'title' => 'Mathematics Class 10',
                'author' => 'NCERT',
                'isbn' => '9788174506788',
                'publisher' => 'NCERT',
                'publication_year' => 2021,
                'category_code' => 'ACADEMIC',
                'total_copies' => 10,
            ],
            [
                'title' => 'Oxford English Dictionary',
                'author' => 'Oxford University Press',
                'isbn' => '9780198611868',
                'publisher' => 'Oxford University Press',
                'publication_year' => 2020,
                'category_code' => 'REFERENCE',
                'total_copies' => 2,
            ],
            [
                'title' => 'The Very Hungry Caterpillar',
                'author' => 'Eric Carle',
                'isbn' => '9780399226908',
                'publisher' => 'Philomel Books',
                'publication_year' => 1969,
                'category_code' => 'CHILDREN',
                'total_copies' => 6,
            ],
        ];

        foreach ($booksData as $bookData) {
            $category = BookCategory::where('school_id', $school->id)
                ->where('code', $bookData['category_code'])
                ->first();

            if ($category) {
                LibraryBook::firstOrCreate(
                    [
                        'school_id' => $school->id,
                        'isbn' => $bookData['isbn']
                    ],
                    [
                        'category_id' => $category->id,
                        'title' => $bookData['title'],
                        'author' => $bookData['author'],
                        'publisher' => $bookData['publisher'],
                        'publication_year' => $bookData['publication_year'],
                        'total_copies' => $bookData['total_copies'],
                        'available_copies' => $bookData['total_copies'],
                        'issued_copies' => 0,
                        'reserved_copies' => 0,
                        'language' => 'English',
                        'condition' => 'excellent',
                        'is_active' => true,
                    ]
                );
            }
        }

        // Create sample book issues only if students exist
        $students = Student::take(3)->get();
        if ($students->count() > 0) {
            $books = LibraryBook::where('school_id', $school->id)->take(3)->get();

            foreach ($books as $index => $book) {
                if (isset($students[$index])) {
                    BookIssue::firstOrCreate(
                        [
                            'book_id' => $book->id,
                            'student_id' => $students[$index]->id,
                            'status' => 'issued'
                        ],
                        [
                            'school_id' => $school->id,
                            'issued_by' => 1, // Admin user
                            'issue_date' => now()->subDays(rand(1, 10)),
                            'due_date' => now()->addDays(rand(5, 20)),
                            'condition_at_issue' => 'excellent',
                            'status' => 'issued',
                        ]
                    );
                }
            }

            // Update book copy counts
            foreach ($books as $book) {
                $book->updateCopyCounts();
            }
        }

        $this->command->info('Library data seeded successfully!');
        $this->command->info('Created ' . BookCategory::count() . ' book categories');
        $this->command->info('Created ' . LibraryBook::count() . ' library books');
        $this->command->info('Created ' . BookIssue::count() . ' book issues');
    }
}
