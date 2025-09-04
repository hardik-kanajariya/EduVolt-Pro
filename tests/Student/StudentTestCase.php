<?php

namespace Tests\Student;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class StudentTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create student user for testing
        $this->createStudentUser();
    }

    /**
     * Create a student user for testing.
     */
    protected function createStudentUser(): void
    {
        // This will be implemented when we create User model and factories
        // For now, it's a placeholder
    }

    /**
     * Get student login credentials.
     */
    protected function getStudentCredentials(): array
    {
        return [
            'email' => 'student@eduvaultpro.com',
            'password' => 'Student@123',
        ];
    }

    /**
     * Login as student user.
     */
    protected function actingAsStudent(): self
    {
        // This will be implemented when we create authentication
        // For now, it's a placeholder
        return $this;
    }

    /**
     * Create test course enrollment.
     */
    protected function enrollInCourse(int $courseId): void
    {
        // This will be implemented when we create enrollment functionality
        // For now, it's a placeholder
    }
}
