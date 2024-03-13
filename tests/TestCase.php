<?php

namespace PodPoint\ConfigCat\Tests;

use Closure;
use Illuminate\Foundation\Application;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;
use PodPoint\ConfigCat\ConfigCatServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param Application $app
     * @return array
     */
    protected function getPackageProviders($app): array
    {
        return [
            ConfigCatServiceProvider::class,
        ];
    }

    /**
     * Override application aliases.
     *
     * @param Application $app
     * @return array
     */
    protected function getPackageAliases($app): array
    {
        return [
            'ConfigCat' => \PodPoint\ConfigCat\Facades\ConfigCat::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param Application $app
     * @return void
     */
    protected function getEnvironmentSetUp($app): void
    {
        $app['config']->set('cache.default', 'file');

        $app['config']->set('configcat.key', 'gL_tESTh5r6R6gAG6gRlpR/GH-A5gLI69brgKiR5eLGBs');

        $app['config']->set('configcat.user', \PodPoint\ConfigCat\Support\DefaultUserTransformer::class);

        $app['config']->set('configcat.overrides', [
            'enabled' => false,
            'file' => storage_path('app/features/configcat.json'),
        ]);

        $app['config']->set('view.paths', [
            dirname(__FILE__).DIRECTORY_SEPARATOR.'resources/views',
        ]);
    }

    /**
     * Mock an instance of an object in the container.
     *
     * @param string $abstract
     * @param Closure|null  $mock
     * @return object
     */
    protected function mock($abstract, Closure $mock = null): object
    {
        return $this->instance($abstract, Mockery::mock(...array_filter(func_get_args())));
    }
}
