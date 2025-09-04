<?php

namespace Tests\Admin;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

abstract class AdminTestCase extends TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Create admin user for testing
        $this->createAdminUser();
    }

    /**
     * Create an admin user for testing.
     */
    protected function createAdminUser(): void
    {
        // This will be implemented when we create User model and factories
        // For now, it's a placeholder
    }

    /**
     * Get admin login credentials.
     */
    protected function getAdminCredentials(): array
    {
        return [
            'email' => 'admin@eduvaultpro.com',
            'password' => 'Admin@123',
        ];
    }

    /**
     * Login as admin user.
     */
    protected function actingAsAdmin(): self
    {
        // This will be implemented when we create authentication
        // For now, it's a placeholder
        return $this;
    }
}
