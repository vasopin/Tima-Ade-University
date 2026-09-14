<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Ensure the test database is refreshed non-interactively.
     * Laravel's migrate:fresh command prompts in interactive terminals; PHPUnit
     * uses a mocked console output, so the prompt triggers an unhandled
     * "askQuestion" call unless we explicitly force the command.
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
