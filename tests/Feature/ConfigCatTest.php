<?php

namespace PodPoint\ConfigCat\Tests\Feature;

use ConfigCat\ClientInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Mockery\MockInterface;
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

    public function test_global_helper_relies_on_the_facade()
    {
        Features::shouldReceive('get')->once()->with('some_feature');

        feature('some_feature');
    }

    public function test_global_helper_can_be_used_with_a_given_user()
    {
        $user = new \Illuminate\Foundation\Auth\User();
        $user->id = 123;
        $user->email = 'foo@bar.com';

        Features::shouldReceive('get')->once()->with('some_feature', $user);

        feature('some_feature', $user);
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

    public function test_the_blade_directive_will_render_something_only_when_the_corresponding_feature_flag_is_enabled()
    {
        Features::fake([
            'enabled_feature' => true,
            'disabled_feature' => false,
        ]);

        Route::get('/foo', function () {
            return view('feature');
        });

        $this->get('/foo')->assertSee('I should be visible');
        $this->get('/foo')->assertDontSee('I am hidden');
    }

    public function test_config_cat_client_is_called_when_resolving_feature_flags()
    {
        $this->mock(ClientInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getValue')->once();
        });

        Features::get('some_feature');
    }

    public function test_the_user_handler_can_be_used_when_resolving_feature_flags()
    {
        $this->mock(ClientInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getValue')
                ->once()
                ->with('some_feature', false, \Mockery::on(function (\ConfigCat\User $user) {
                    return $user->getIdentifier() === '456'
                        && $user->getAttribute('Email') === 'foo@baz.com';
                }));
        });

        $user = new \Illuminate\Foundation\Auth\User();
        $user->id = 456;
        $user->email = 'foo@baz.com';

        Features::get('some_feature', $user);
    }

    public function test_the_user_handler_will_use_the_logged_in_user_by_default()
    {
        $this->mock(ClientInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getValue')
                ->once()
                ->with('some_feature', false, \Mockery::on(function (\ConfigCat\User $user) {
                    return $user->getIdentifier() === '789'
                        && $user->getAttribute('Email') === 'foo@foo.com';
                }));
        });

        $user = new \Illuminate\Foundation\Auth\User();
        $user->id = 789;
        $user->email = 'foo@foo.com';

        $this->actingAs($user);

        Features::get('some_feature');
    }
}
