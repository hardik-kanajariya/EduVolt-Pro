<?php

namespace Tests\Feature\Library;

use App\Models\BookCategory;
use App\Models\LibraryBook;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LibraryBookTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private School $school;
    private BookCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create();
        $this->school = School::factory()->create();
        $this->category = BookCategory::factory()->create([
            'school_id' => $this->school->id
        ]);
    }

    public function test_can_create_library_book()
    {
        $bookData = [
            'title' => 'Test Book',
            'author' => 'Test Author',
            'isbn' => '978-0123456789',
            'publisher' => 'Test Publisher',
            'publication_year' => 2023,
            'total_copies' => 5,
            'available_copies' => 5,
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
        ];

        $book = LibraryBook::create($bookData);

        $this->assertInstanceOf(LibraryBook::class, $book);
        $this->assertDatabaseHas('library_books', $bookData);
        $this->assertNotNull($book->barcode);
    }

    public function test_barcode_is_automatically_generated()
    {
        $book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
        ]);

        $this->assertNotNull($book->barcode);
        $this->assertMatchesRegularExpression('/^LB\d{10}$/', $book->barcode);
    }

    public function test_can_check_book_availability()
    {
        $book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 3,
        ]);

        $this->assertTrue($book->isAvailable());

        $book->update(['available_copies' => 0]);
        $this->assertFalse($book->fresh()->isAvailable());
    }

    public function test_can_borrow_book()
    {
        $book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);

        $initialAvailable = $book->available_copies;
        $result = $book->borrowBook();

        $this->assertTrue($result);
        $this->assertEquals($initialAvailable - 1, $book->fresh()->available_copies);
    }

    public function test_cannot_borrow_unavailable_book()
    {
        $book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 0,
        ]);

        $result = $book->borrowBook();

        $this->assertFalse($result);
        $this->assertEquals(0, $book->fresh()->available_copies);
    }

    public function test_can_return_book()
    {
        $book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 3,
        ]);

        $initialAvailable = $book->available_copies;
        $result = $book->returnBook();

        $this->assertTrue($result);
        $this->assertEquals($initialAvailable + 1, $book->fresh()->available_copies);
    }

    public function test_cannot_return_more_books_than_total()
    {
        $book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
            'total_copies' => 5,
            'available_copies' => 5,
        ]);

        $result = $book->returnBook();

        $this->assertFalse($result);
        $this->assertEquals(5, $book->fresh()->available_copies);
    }

    public function test_belongs_to_school()
    {
        $book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
        ]);

        $this->assertInstanceOf(School::class, $book->school);
        $this->assertEquals($this->school->id, $book->school->id);
    }

    public function test_belongs_to_category()
    {
        $book = LibraryBook::factory()->create([
            'school_id' => $this->school->id,
            'book_category_id' => $this->category->id,
        ]);

        $this->assertInstanceOf(BookCategory::class, $book->bookCategory);
        $this->assertEquals($this->category->id, $book->bookCategory->id);
    }
}
