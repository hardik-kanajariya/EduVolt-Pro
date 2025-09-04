<?php

namespace Tests\Feature\Library;

use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\BookReservation;
use App\Models\LibraryBook;
use App\Models\LibraryFine;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private School $school;
    private Student $student;
    private BookCategory $category;
    private LibraryBook $book;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
        $this->school = School::factory()->create();
        
        $this->student = Student::factory()->create([
            'school_id' => $this->school->id
        ]);

        $this->category = BookCategory::factory()->create([
            'school_id' => $this->school->id
        ]);

        $this->book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
            'total_copies' => 2,
            'available_copies' => 2,
        ]);
    }

    public function test_complete_book_issue_and_return_workflow()
    {
        // Initial state
        $this->assertEquals(2, $this->book->available_copies);
        $this->assertTrue($this->book->isAvailable());

        // Issue book
        $issue = BookIssue::create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'issued_at' => now(),
        ]);

        // Check book availability updated
        $this->book->refresh();
        $this->assertEquals(1, $this->book->available_copies);
        $this->assertTrue($this->book->isAvailable());

        // Check issue details
        $this->assertNotNull($issue->due_date);
        $this->assertNull($issue->returned_at);
        $this->assertFalse($issue->isOverdue());

        // Return book
        $issue->update([
            'returned_at' => now(),
            'returned_to' => $this->admin->id,
        ]);

        // Check book availability updated
        $this->book->refresh();
        $this->assertEquals(2, $this->book->available_copies);
        $this->assertNotNull($issue->fresh()->returned_at);
    }

    public function test_overdue_book_creates_fine()
    {
        // Issue book
        $issue = BookIssue::create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'issued_at' => Carbon::now()->subDays(20),
            'due_date' => Carbon::now()->subDays(5),
        ]);

        // Simulate return of overdue book
        $issue->update([
            'returned_at' => now(),
            'returned_to' => $this->admin->id,
        ]);

        // Check fine was created
        $fine = LibraryFine::where('book_issue_id', $issue->id)->first();
        
        $this->assertNotNull($fine);
        $this->assertEquals($this->student->id, $fine->student_id);
        $this->assertEquals('unpaid', $fine->status);
        $this->assertGreaterThan(0, $fine->amount);
    }

    public function test_book_reservation_when_unavailable()
    {
        // Issue all copies
        BookIssue::create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'issued_at' => now(),
        ]);

        $student2 = Student::factory()->create([
            'school_id' => $this->school->id
        ]);

        BookIssue::create([
            'library_book_id' => $this->book->id,
            'student_id' => $student2->id,
            'issued_by' => $this->admin->id,
            'issued_at' => now(),
        ]);

        // Book should be unavailable now
        $this->book->refresh();
        $this->assertEquals(0, $this->book->available_copies);
        $this->assertFalse($this->book->isAvailable());

        // Try to reserve the book
        $student3 = Student::factory()->create([
            'school_id' => $this->school->id
        ]);

        $reservation = BookReservation::create([
            'library_book_id' => $this->book->id,
            'student_id' => $student3->id,
            'reserved_by' => $this->admin->id,
            'reserved_at' => now(),
        ]);

        $this->assertEquals('pending', $reservation->status);
        $this->assertNotNull($reservation->expires_at);
    }

    public function test_book_renewal_workflow()
    {
        // Issue book
        $issue = BookIssue::create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'issued_at' => now(),
        ]);

        $originalDueDate = $issue->due_date;
        
        // Renew book
        $renewed = $issue->renewBook();
        
        $this->assertTrue($renewed);
        $this->assertEquals(1, $issue->fresh()->renewal_count);
        $this->assertTrue($issue->fresh()->due_date->gt($originalDueDate));
    }

    public function test_multiple_renewals_limit()
    {
        // Issue book
        $issue = BookIssue::create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'issued_at' => now(),
            'renewal_count' => 3, // At max limit
        ]);

        // Try to renew
        $renewed = $issue->renewBook();
        
        $this->assertFalse($renewed);
        $this->assertEquals(3, $issue->fresh()->renewal_count);
    }

    public function test_student_can_have_multiple_book_issues()
    {
        // Create another book
        $book2 = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
            'total_copies' => 1,
            'available_copies' => 1,
        ]);

        // Issue first book
        $issue1 = BookIssue::create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'issued_at' => now(),
        ]);

        // Issue second book
        $issue2 = BookIssue::create([
            'library_book_id' => $book2->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'issued_at' => now(),
        ]);

        // Check student has both books
        $this->assertEquals(2, $this->student->bookIssues()->whereNull('returned_at')->count());
    }

    public function test_fine_payment_workflow()
    {
        // Create overdue issue and fine
        $issue = BookIssue::create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'issued_at' => Carbon::now()->subDays(20),
            'due_date' => Carbon::now()->subDays(5),
            'returned_at' => now(),
            'returned_to' => $this->admin->id,
        ]);

        $fine = LibraryFine::create([
            'book_issue_id' => $issue->id,
            'student_id' => $this->student->id,
            'amount' => 25.00,
            'reason' => 'Overdue return',
            'status' => 'unpaid',
            'fine_date' => now(),
        ]);

        // Mark fine as paid
        $fine->markAsPaid($this->admin->id);

        $this->assertEquals('paid', $fine->fresh()->status);
        $this->assertEquals($this->admin->id, $fine->fresh()->paid_by);
        $this->assertNotNull($fine->fresh()->paid_at);
    }

    public function test_library_statistics()
    {
        // Create some test data
        $student2 = Student::factory()->create(['school_id' => $this->school->id]);
        
        // Issue some books
        BookIssue::create([
            'library_book_id' => $this->book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'issued_at' => now(),
        ]);

        BookIssue::create([
            'library_book_id' => $this->book->id,
            'student_id' => $student2->id,
            'issued_by' => $this->admin->id,
            'issued_at' => now(),
            'due_date' => Carbon::yesterday(), // Overdue
        ]);

        // Create fine
        $fine = LibraryFine::factory()->create([
            'book_issue_id' => 2,
            'student_id' => $student2->id,
            'amount' => 15.00,
            'status' => 'unpaid',
        ]);

        // Check statistics
        $totalBooks = LibraryBook::count();
        $issuedBooks = BookIssue::whereNull('returned_at')->count();
        $overdueBooks = BookIssue::whereNull('returned_at')
            ->where('due_date', '<', now())
            ->count();
        $totalFines = LibraryFine::where('status', 'unpaid')->sum('amount');

        $this->assertEquals(1, $totalBooks);
        $this->assertEquals(2, $issuedBooks);
        $this->assertEquals(1, $overdueBooks);
        $this->assertEquals(15.00, $totalFines);
    }
}
