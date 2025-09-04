<?php

namespace Tests\Feature\Library;

use App\Models\BookIssue;
use App\Models\BookCategory;
use App\Models\LibraryBook;
use App\Models\LibraryFine;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookIssueTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private School $school;
    private Student $student;
    private LibraryBook $book;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
        $this->school = School::factory()->create();
        
        $this->student = Student::factory()->create([
            'school_id' => $this->school->id
        ]);

        $category = BookCategory::factory()->create([
            'school_id' => $this->school->id
        ]);

        $this->book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);
    }

    public function test_can_issue_book()
    {
        $issueData = [
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'issued_at' => now(),
        ];

        $issue = BookIssue::create($issueData);

        $this->assertInstanceOf(BookIssue::class, $issue);
        $this->assertDatabaseHas('book_issues', $issueData);
        $this->assertNotNull($issue->due_date);
    }

    public function test_due_date_is_automatically_set()
    {
        $issue = BookIssue::factory()->create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
        ]);

        $expectedDueDate = $issue->issued_at->copy()->addDays(14);
        
        $this->assertEquals(
            $expectedDueDate->format('Y-m-d'),
            $issue->due_date->format('Y-m-d')
        );
    }

    public function test_can_return_book()
    {
        $issue = BookIssue::factory()->create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
        ]);

        $returnDate = now();
        $issue->update([
            'returned_at' => $returnDate,
            'returned_to' => $this->admin->id,
        ]);

        $this->assertNotNull($issue->fresh()->returned_at);
        $this->assertEquals($this->admin->id, $issue->fresh()->returned_to);
    }

    public function test_can_check_if_overdue()
    {
        // Create overdue issue
        $overdueIssue = BookIssue::factory()->create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'due_date' => Carbon::yesterday(),
        ]);

        // Create current issue
        $currentIssue = BookIssue::factory()->create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'due_date' => Carbon::tomorrow(),
        ]);

        $this->assertTrue($overdueIssue->isOverdue());
        $this->assertFalse($currentIssue->isOverdue());
    }

    public function test_can_calculate_overdue_days()
    {
        $issue = BookIssue::factory()->create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'due_date' => Carbon::now()->subDays(5),
        ]);

        $this->assertEquals(5, $issue->getOverdueDays());
    }

    public function test_can_renew_book()
    {
        $issue = BookIssue::factory()->create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'renewal_count' => 0,
        ]);

        $originalDueDate = $issue->due_date;
        $result = $issue->renewBook();

        $this->assertTrue($result);
        $this->assertEquals(1, $issue->fresh()->renewal_count);
        $this->assertTrue($issue->fresh()->due_date->gt($originalDueDate));
    }

    public function test_cannot_renew_book_more_than_limit()
    {
        $issue = BookIssue::factory()->create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'renewal_count' => 3, // Max renewals
        ]);

        $result = $issue->renewBook();

        $this->assertFalse($result);
        $this->assertEquals(3, $issue->fresh()->renewal_count);
    }

    public function test_fine_is_created_for_overdue_return()
    {
        $issue = BookIssue::factory()->create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'due_date' => Carbon::now()->subDays(5),
        ]);

        // Simulate returning overdue book
        $issue->update([
            'returned_at' => now(),
            'returned_to' => $this->admin->id,
        ]);

        // Check if fine was created
        $this->assertDatabaseHas('library_fines', [
            'book_issue_id' => $issue->id,
            'student_id' => $this->student->id,
            'status' => 'unpaid',
        ]);
    }

    public function test_belongs_to_library_book()
    {
        $issue = BookIssue::factory()->create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
        ]);

        $this->assertInstanceOf(LibraryBook::class, $issue->libraryBook);
        $this->assertEquals($this->book->id, $issue->libraryBook->id);
    }

    public function test_belongs_to_student()
    {
        $issue = BookIssue::factory()->create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
        ]);

        $this->assertInstanceOf(Student::class, $issue->student);
        $this->assertEquals($this->student->id, $issue->student->id);
    }
}
