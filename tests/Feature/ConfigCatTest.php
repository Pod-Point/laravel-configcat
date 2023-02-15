<?php

namespace PodPoint\ConfigCat\Tests\Feature;

use PodPoint\ConfigCat\Facades\Features;
use PodPoint\ConfigCat\Tests\TestCase;

class ConfigCatTest extends TestCase
{
    public function test_it_does_something()
    {
        $this->assertTrue(true);
    }

    public function test_global_helper_can_be_used_to_check_if_a_feature_flag_is_enabled_or_disabled()
    {
        Features::fake([
            'some_enabled_feature' => true,
            'some_disabled_feature' => false,
        ]);

        $this->assertTrue(feature('some_enabled_feature'));
        $this->assertFalse(feature('some_disabled_feature'));
    }

    public function test_global_helper_returns_false_when_a_feature_flag_does_not_exist()
    {
        Features::fake(['some_feature' => true]);

        $this->assertFalse(feature('some_unknown_feature'));
    }

    public function test_global_helper_can_retrieve_a_feature_flag_when_it_is_a_string()
    {
        Features::fake(['some_feature_as_a_string' => 'foo']);

        $this->assertEquals('foo', feature('some_feature_as_a_string'));
    }
}
