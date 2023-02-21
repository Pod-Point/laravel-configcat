<?php

namespace PodPoint\ConfigCat\Tests\Feature\Facades;

use ConfigCat\ClientInterface;
use Illuminate\Support\Facades\File;
use Mockery\MockInterface;
use PodPoint\ConfigCat\Facades\ConfigCat;
use PodPoint\ConfigCat\Tests\TestCase;

class ConfigCatTest extends TestCase
{
    public function test_the_facade_can_override_feature_flags()
    {
        config(['configcat.overrides.enabled' => true]);

        ConfigCat::override([
            'enabled_feature' => true,
            'disabled_feature' => false,
        ]);

        $this->assertTrue(configcat('enabled_feature'));
        $this->assertFalse(configcat('disabled_feature'));

        $this->assertTrue(File::exists(storage_path('app/features/configcat.json')));
        $this->assertEquals(
            '{"flags":{"enabled_feature":true,"disabled_feature":false}}',
            File::get(storage_path('app/features/configcat.json'))
        );
    }

    public function test_config_cat_client_is_called_when_resolving_feature_flags()
    {
        $this->mock(ClientInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getValue')->once();
        });

        ConfigCat::get('some_feature');
    }

    public function test_false_is_the_default_value_automatically_set_when_resolving_feature_flags()
    {
        $this->mock(ClientInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getValue')
                ->once()
                ->with('some_feature', false, null);
        });

        ConfigCat::get('some_feature');
    }

    public function test_a_default_value_can_be_passed_when_resolving_feature_flags()
    {
        $this->mock(ClientInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getValue')
                ->once()
                ->with('some_feature', true, null);
        });

        ConfigCat::get('some_feature', true);
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

        ConfigCat::get('some_feature', false, $user);
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

        ConfigCat::get('some_feature');
    }
}
