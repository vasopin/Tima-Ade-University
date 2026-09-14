<?php

namespace Tests\Concerns;

use Illuminate\Foundation\Testing\RefreshDatabase;

trait ForceRefreshDatabase
{
    use RefreshDatabase;

    /**
     * Force the database refresh command in the test environment so PHPUnit does
     * not trigger interactive confirmations through the console output mock.
     */
    protected function migrateFreshUsing()
    {
        $seeder = $this->seeder();

        return array_merge(
            [
                '--drop-views' => $this->shouldDropViews(),
                '--drop-types' => $this->shouldDropTypes(),
                '--force' => true,
            ],
            $seeder ? ['--seeder' => $seeder] : ['--seed' => $this->shouldSeed()]
        );
    }
}
