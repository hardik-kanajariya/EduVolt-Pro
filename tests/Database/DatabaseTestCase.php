<?php

namespace Tests\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

abstract class DatabaseTestCase extends TestCase
{
    use RefreshDatabase, DatabaseMigrations;

    /**
     * Indicates whether the database should be migrated.
     */
    protected bool $migrateFreshDatabase = true;

    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Ensure database is fresh for each test
        if ($this->migrateFreshDatabase) {
            $this->artisan('migrate:fresh');
        }

        // Seed necessary data for testing
        $this->seedEssentialData();
    }

    /**
     * Seed essential data required for testing.
     */
    protected function seedEssentialData(): void
    {
        // This will be implemented when we create seeders
        // For now, it's a placeholder
    }

    /**
     * Clean up after each test.
     */
    protected function tearDown(): void
    {
        // Clear any cached data
        $this->artisan('cache:clear');

        parent::tearDown();
    }
}
