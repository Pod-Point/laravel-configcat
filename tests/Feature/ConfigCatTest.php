<?php

namespace PodPoint\ConfigCat\Tests\Feature;

use Illuminate\Support\Facades\File;
use PodPoint\ConfigCat\Facades\Features;
use PodPoint\ConfigCat\Tests\TestCase;

class ConfigCatTest extends TestCase
{
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

    public function test_global_helper_can_retrieve_a_feature_flag_when_it_is_an_integer()
    {
        Features::fake(['some_feature_as_a_string' => 123]);

        $this->assertEquals(123, feature('some_feature_as_a_string'));
    }

    public function test_the_facade_can_override_feature_flags()
    {
        config(['configcat.overrides.enabled' => true]);

        Features::override([
            'enabled_feature' => true,
            'disabled_feature' => false,
        ]);

        $this->assertTrue(feature('enabled_feature'));
        $this->assertFalse(feature('disabled_feature'));

        $this->assertTrue(File::exists(storage_path('app/features/configcat.json')));
        $this->assertEquals(
            '{"flags":{"enabled_feature":true,"disabled_feature":false}}',
            File::get(storage_path('app/features/configcat.json'))
        );
    }
}
