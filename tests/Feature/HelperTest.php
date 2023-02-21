<?php

namespace PodPoint\ConfigCat\Tests\Feature;

use PodPoint\ConfigCat\Facades\ConfigCat;
use PodPoint\ConfigCat\Tests\TestCase;

class HelperTest extends TestCase
{
    public function test_global_helper_can_be_used_to_check_if_a_feature_flag_is_enabled_or_disabled()
    {
        ConfigCat::fake([
            'some_enabled_feature' => true,
            'some_disabled_feature' => false,
        ]);

        $this->assertTrue(configcat('some_enabled_feature'));
        $this->assertFalse(configcat('some_disabled_feature'));
    }

    public function test_global_helper_returns_false_when_a_feature_flag_does_not_exist()
    {
        ConfigCat::fake(['some_feature' => true]);

        $this->assertFalse(configcat('some_unknown_feature'));
    }

    public function test_global_helper_can_retrieve_a_text_setting()
    {
        ConfigCat::fake(['some_feature_as_a_string' => 'foo']);

        $this->assertEquals('foo', configcat('some_feature_as_a_string'));
    }

    public function test_global_helper_can_retrieve_a_number_setting()
    {
        ConfigCat::fake(['some_feature_as_a_string' => 123]);

        $this->assertEquals(123, configcat('some_feature_as_a_string'));
    }

    public function test_global_helper_relies_on_the_facade()
    {
        ConfigCat::shouldReceive('get')->once()->with('some_feature');

        configcat('some_feature');
    }

    public function test_global_helper_can_be_used_with_a_given_user()
    {
        $user = new \Illuminate\Foundation\Auth\User();
        $user->id = 123;
        $user->email = 'foo@bar.com';

        ConfigCat::shouldReceive('get')->once()->with('some_feature', $user);

        configcat('some_feature', $user);
    }
}
