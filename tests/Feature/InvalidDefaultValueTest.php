<?php

namespace PodPoint\ConfigCat\Tests;

use PodPoint\ConfigCat\Facades\ConfigCat;
use PodPoint\ConfigCat\Tests\TestCase;

class InvalidDefaultValueTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('configcat.default', null);
    }

    public function test_null_configured_as_a_default_value_for_the_package_will_throw_an_exception()
    {
        $this->expectException(\InvalidArgumentException::class);

        ConfigCat::get('foo');
    }
}
