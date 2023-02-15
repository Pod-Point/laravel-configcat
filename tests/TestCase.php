<?php

namespace PodPoint\ConfigCat\Tests;

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
     *
     * @return array
     */
    protected function getPackageAliases($app)
    {
        return [
            'Features' => \PodPoint\ConfigCat\Facades\Features::class,
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
        $app['config']->set('configcat.key', 'testing');
        $app['config']->set('configcat.overrides', [
            'enabled' => false,
            'file' => storage_path('app/features/configcat.json'),
        ]);
    }
}
