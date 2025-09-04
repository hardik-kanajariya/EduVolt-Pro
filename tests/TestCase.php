<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Set up testing environment
        $this->setupTestingEnvironment();
    }

    /**
     * Setup the testing environment.
     */
    protected function setupTestingEnvironment(): void
    {
        // Ensure we're in testing environment
        $this->app['env'] = 'testing';

        // Clear any cached configuration
        $this->artisan('config:clear');

        // Set timezone
        date_default_timezone_set('Asia/Kolkata');
    }

    /**
     * Clean up after each test.
     */
    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Create application instance.
     */
    public function createApplication()
    {
        $app = require __DIR__ . '/../bootstrap/app.php';

        return $app;
    }
}
