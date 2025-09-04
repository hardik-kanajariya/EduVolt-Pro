<?php

namespace Tests\Traits;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

trait DatabaseTestingTrait
{
    /**
     * Migrate and seed the database for testing.
     */
    protected function migrateAndSeed(): void
    {
        Artisan::call('migrate:fresh');
        Artisan::call('db:seed');
    }

    /**
     * Truncate all tables except migrations.
     */
    protected function truncateAllTables(): void
    {
        $tables = DB::select('SHOW TABLES');
        $tableColumn = 'Tables_in_' . config('database.connections.mysql.database');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tables as $table) {
            $tableName = $table->$tableColumn;
            if ($tableName !== 'migrations') {
                DB::table($tableName)->truncate();
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Assert that a database table has a specific number of records.
     */
    protected function assertTableHasCount(string $table, int $count): void
    {
        $actual = DB::table($table)->count();
        $this->assertEquals(
            $count,
            $actual,
            "Table [{$table}] expected to have {$count} records, but has {$actual}"
        );
    }

    /**
     * Assert that a database table is empty.
     */
    protected function assertTableIsEmpty(string $table): void
    {
        $this->assertTableHasCount($table, 0);
    }

    /**
     * Assert that a record exists in the database.
     */
    protected function assertDatabaseHasRecord(string $table, array $data): void
    {
        $this->assertDatabaseHas($table, $data);
    }

    /**
     * Assert that a record does not exist in the database.
     */
    protected function assertDatabaseMissingRecord(string $table, array $data): void
    {
        $this->assertDatabaseMissing($table, $data);
    }

    /**
     * Get the last inserted record from a table.
     */
    protected function getLastRecord(string $table): ?object
    {
        return DB::table($table)->latest('id')->first();
    }

    /**
     * Create a test record in the database.
     */
    protected function createTestRecord(string $table, array $data): int
    {
        return DB::table($table)->insertGetId(array_merge([
            'created_at' => now(),
            'updated_at' => now(),
        ], $data));
    }
}
