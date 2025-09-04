<?php

namespace Tests\Teacher;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class TeacherTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create teacher user for testing
        $this->createTeacherUser();
    }

    /**
     * Create a teacher user for testing.
     */
    protected function createTeacherUser(): void
    {
        // This will be implemented when we create User model and factories
        // For now, it's a placeholder
    }

    /**
     * Get teacher login credentials.
     */
    protected function getTeacherCredentials(): array
    {
        return [
            'email' => 'teacher@eduvaultpro.com',
            'password' => 'Teacher@123',
        ];
    }

    /**
     * Login as teacher user.
     */
    protected function actingAsTeacher(): self
    {
        // This will be implemented when we create authentication
        // For now, it's a placeholder
        return $this;
    }

    /**
     * Create test course for teacher.
     */
    protected function createCourse(array $attributes = []): array
    {
        // This will be implemented when we create Course model and factories
        // For now, it's a placeholder
        return [
            'id' => 1,
            'title' => 'Test Course',
            'description' => 'Test Course Description',
            ...$attributes
        ];
    }
}
