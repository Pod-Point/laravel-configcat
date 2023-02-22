<?php

namespace PodPoint\ConfigCat\Tests;

use Closure;
use Mockery;
use Orchestra\Testbench\TestCase as Orchestra;
use PodPoint\ConfigCat\ConfigCatServiceProvider;

abstract class TestCase extends Orchestra
{
    /**
     * Get package providers.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageProviders($app)
    {
        return [
            ConfigCatServiceProvider::class,
        ];
    }

    /**
     * Override application aliases.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'ConfigCat' => \PodPoint\ConfigCat\Facades\ConfigCat::class,
        ];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('cache.default', 'file');

        $app['config']->set('configcat.key', 'testing');

        $app['config']->set('configcat.user', function ($user) {
            return new \ConfigCat\User($user->id, $user->email);
        });

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
     * @param  string  $abstract
     * @param  \Closure|null  $mock
     * @return \Mockery\MockInterface
     */
    protected function mock($abstract, Closure $mock = null)
    {
        return $this->instance($abstract, Mockery::mock(...array_filter(func_get_args())));
    }
}
