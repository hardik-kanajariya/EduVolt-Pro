<?php

namespace Tests\Feature\Library;

use App\Models\BookCategory;
use App\Models\School;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookCategoryTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private School $school;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->admin = User::factory()->create();
        $this->school = School::factory()->create();
    }

    public function test_can_create_book_category()
    {
        $categoryData = [
            'name' => 'Science Fiction',
            'description' => 'Books about science and future',
            'color' => '#3B82F6',
            'school_id' => $this->school->id,
        ];

        $category = BookCategory::create($categoryData);

        $this->assertInstanceOf(BookCategory::class, $category);
        $this->assertDatabaseHas('book_categories', $categoryData);
    }

    public function test_name_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        BookCategory::create([
            'description' => 'Test description',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_school_id_is_required()
    {
        $this->expectException(\Illuminate\Database\QueryException::class);
        
        BookCategory::create([
            'name' => 'Test Category',
            'description' => 'Test description',
        ]);
    }

    public function test_belongs_to_school()
    {
        $category = BookCategory::factory()->create([
            'school_id' => $this->school->id,
        ]);

        $this->assertInstanceOf(School::class, $category->school);
        $this->assertEquals($this->school->id, $category->school->id);
    }

    public function test_has_many_library_books()
    {
        $category = BookCategory::factory()->create([
            'school_id' => $this->school->id,
        ]);

        $this->assertTrue($category->libraryBooks()->exists() === false);
        $this->assertCount(0, $category->libraryBooks);
    }

    public function test_active_scope()
    {
        // Create active category
        $activeCategory = BookCategory::factory()->create([
            'school_id' => $this->school->id,
            'is_active' => true,
        ]);

        // Create inactive category
        $inactiveCategory = BookCategory::factory()->create([
            'school_id' => $this->school->id,
            'is_active' => false,
        ]);

        $activeCategories = BookCategory::active()->get();

        $this->assertTrue($activeCategories->contains($activeCategory));
        $this->assertFalse($activeCategories->contains($inactiveCategory));
    }

    public function test_get_books_count_accessor()
    {
        $category = BookCategory::factory()->create([
            'school_id' => $this->school->id,
        ]);

        // Initially should be 0
        $this->assertEquals(0, $category->books_count);
    }

    public function test_can_soft_delete()
    {
        $category = BookCategory::factory()->create([
            'school_id' => $this->school->id,
        ]);

        $categoryId = $category->id;
        $category->delete();

        // Should be soft deleted
        $this->assertDatabaseHas('book_categories', [
            'id' => $categoryId,
        ]);

        $this->assertNull(BookCategory::find($categoryId));
        $this->assertNotNull(BookCategory::withTrashed()->find($categoryId));
    }

    public function test_can_restore_soft_deleted()
    {
        $category = BookCategory::factory()->create([
            'school_id' => $this->school->id,
        ]);

        $categoryId = $category->id;
        $category->delete();
        
        $category = BookCategory::withTrashed()->find($categoryId);
        $category->restore();

        $this->assertNotNull(BookCategory::find($categoryId));
    }
}
