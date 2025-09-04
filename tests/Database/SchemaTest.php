<?php

namespace Tests\Database;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class SchemaTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that all required tables exist.
     */
    public function test_required_tables_exist(): void
    {
        $requiredTables = [
            'users',
            'password_reset_tokens',
            'sessions',
            'failed_jobs',
            'job_batches',
            'cache',
            'cache_locks',
        ];

        foreach ($requiredTables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Table '{$table}' does not exist in the database"
            );
        }
    }

    /**
     * Test that users table has required columns.
     */
    public function test_users_table_has_required_columns(): void
    {
        $requiredColumns = [
            'id',
            'name',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'created_at',
            'updated_at',
        ];

        foreach ($requiredColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('users', $column),
                "Column '{$column}' does not exist in users table"
            );
        }
    }

    /**
     * Test that users table has proper indexes.
     */
    public function test_users_table_has_proper_indexes(): void
    {
        $indexes = Schema::getConnection()
            ->getDoctrineSchemaManager()
            ->listTableIndexes('users');

        $indexNames = array_keys($indexes);

        // Check for email unique index
        $this->assertTrue(
            in_array('users_email_unique', $indexNames),
            "Users table should have unique index on email column"
        );
    }

    /**
     * Test that password reset tokens table has required structure.
     */
    public function test_password_reset_tokens_table_structure(): void
    {
        $requiredColumns = [
            'email',
            'token',
            'created_at',
        ];

        foreach ($requiredColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('password_reset_tokens', $column),
                "Column '{$column}' does not exist in password_reset_tokens table"
            );
        }
    }

    /**
     * Test that sessions table has required structure.
     */
    public function test_sessions_table_structure(): void
    {
        $requiredColumns = [
            'id',
            'user_id',
            'ip_address',
            'user_agent',
            'payload',
            'last_activity',
        ];

        foreach ($requiredColumns as $column) {
            $this->assertTrue(
                Schema::hasColumn('sessions', $column),
                "Column '{$column}' does not exist in sessions table"
            );
        }
    }
}
