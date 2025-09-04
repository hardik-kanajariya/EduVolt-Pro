<?php

namespace Tests\Feature\Library;

use App\Models\BookCategory;
use App\Models\BookIssue;
use App\Models\LibraryBook;
use App\Models\LibraryFine;
use App\Models\School;
use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryFineTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private School $school;
    private Student $student;
    private BookIssue $bookIssue;

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

        $book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $category->id,
        ]);

        $this->bookIssue = BookIssue::factory()->create([
            'library_book_id' => $book->id,
            'student_id' => $this->student->id,
            'issued_by' => $this->admin->id,
            'due_date' => Carbon::now()->subDays(5),
        ]);
    }

    public function test_can_create_library_fine()
    {
        $fineData = [
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'amount' => 10.00,
            'reason' => 'Overdue return',
            'status' => 'unpaid',
            'fine_date' => now(),
        ];

        $fine = LibraryFine::create($fineData);

        $this->assertInstanceOf(LibraryFine::class, $fine);
        $this->assertDatabaseHas('library_fines', $fineData);
    }

    public function test_can_mark_fine_as_paid()
    {
        $fine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'unpaid',
        ]);

        $fine->markAsPaid($this->admin->id);

        $this->assertEquals('paid', $fine->fresh()->status);
        $this->assertEquals($this->admin->id, $fine->fresh()->paid_by);
        $this->assertNotNull($fine->fresh()->paid_at);
    }

    public function test_can_waive_fine()
    {
        $fine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'unpaid',
        ]);

        $fine->waiveFine($this->admin->id, 'Student hardship');

        $this->assertEquals('waived', $fine->fresh()->status);
        $this->assertEquals($this->admin->id, $fine->fresh()->waived_by);
        $this->assertEquals('Student hardship', $fine->fresh()->waiver_reason);
        $this->assertNotNull($fine->fresh()->waived_at);
    }

    public function test_unpaid_scope()
    {
        $unpaidFine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'unpaid',
        ]);

        $paidFine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'paid',
        ]);

        $unpaidFines = LibraryFine::unpaid()->get();

        $this->assertTrue($unpaidFines->contains($unpaidFine));
        $this->assertFalse($unpaidFines->contains($paidFine));
    }

    public function test_paid_scope()
    {
        $unpaidFine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'unpaid',
        ]);

        $paidFine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'paid',
        ]);

        $paidFines = LibraryFine::paid()->get();

        $this->assertFalse($paidFines->contains($unpaidFine));
        $this->assertTrue($paidFines->contains($paidFine));
    }

    public function test_waived_scope()
    {
        $unpaidFine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'unpaid',
        ]);

        $waivedFine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'waived',
        ]);

        $waivedFines = LibraryFine::waived()->get();

        $this->assertFalse($waivedFines->contains($unpaidFine));
        $this->assertTrue($waivedFines->contains($waivedFine));
    }

    public function test_belongs_to_book_issue()
    {
        $fine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
        ]);

        $this->assertInstanceOf(BookIssue::class, $fine->bookIssue);
        $this->assertEquals($this->bookIssue->id, $fine->bookIssue->id);
    }

    public function test_belongs_to_student()
    {
        $fine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
        ]);

        $this->assertInstanceOf(Student::class, $fine->student);
        $this->assertEquals($this->student->id, $fine->student->id);
    }

    public function test_is_paid_accessor()
    {
        $unpaidFine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'unpaid',
        ]);

        $paidFine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'paid',
        ]);

        $this->assertFalse($unpaidFine->is_paid);
        $this->assertTrue($paidFine->is_paid);
    }

    public function test_is_waived_accessor()
    {
        $unpaidFine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'unpaid',
        ]);

        $waivedFine = LibraryFine::factory()->create([
            'book_issue_id' => $this->bookIssue->id,
            'student_id' => $this->student->id,
            'status' => 'waived',
        ]);

        $this->assertFalse($unpaidFine->is_waived);
        $this->assertTrue($waivedFine->is_waived);
    }
}
