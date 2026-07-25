<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class MigrationRollbackTest extends TestCase
{
    public function test_all_migrations_can_roll_back_and_run_again(): void
    {
        $this->artisan('migrate:fresh')->assertSuccessful();
        $this->assertTrue(Schema::hasTable('listings'));
        $this->assertTrue(Schema::hasTable('offers'));

        $this->artisan('migrate:rollback')->assertSuccessful();
        $this->assertFalse(Schema::hasTable('listings'));
        $this->assertFalse(Schema::hasTable('offers'));

        $this->artisan('migrate')->assertSuccessful();
        $this->assertTrue(Schema::hasTable('listings'));
        $this->assertTrue(Schema::hasTable('offers'));
    }
}
