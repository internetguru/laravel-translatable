<?php

namespace Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Internetguru\LaravelTranslatable\LaravelTranslatableServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;

abstract class TestCase extends Orchestra
{
    use RefreshDatabase;

    protected function getPackageProviders($app)
    {
        return [
            LaravelTranslatableServiceProvider::class,
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Optionally, set up your environment here
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->loadLaravelMigrations(['--database' => 'testing']);

        // Load your package's migrations if needed
        // $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // Create test data
        $this->setUpTestData();
    }

    protected function setUpTestData()
    {
        // Implement your test data setup here
    }
}
