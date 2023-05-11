<?php

namespace PodPoint\ConfigCat\Tests\Feature;

use ConfigCat\Cache\CacheItem;
use ConfigCat\ClientInterface;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mockery;
use Mockery\MockInterface;
use PodPoint\ConfigCat\Facades\ConfigCat;
use PodPoint\ConfigCat\Tests\TestCase;

class ConfigCatTest extends TestCase
{
    protected function getEnvironmentSetUp($app)
    {
        parent::getEnvironmentSetUp($app);

        $app['config']->set('configcat.default', 'some_default');
    }

    public function test_it_can_be_configured_to_use_a_default_value()
    {
        $this->assertEquals('some_default', ConfigCat::get('unknown_feature'));
    }

    public function test_it_can_use_laravel_cache()
    {
        $fakeCachedItem = new CacheItem();
        $fakeCachedItem->config = [
            'f' => [
                'some_feature' => [
                    'v' => 'some_cached_value',
                    'i' => '430bded3',
                    't' => 1,
                ],
            ],
        ];

        /** @var \Mockery\MockInterface $mockedCacheStore */
        $mockedCacheStore = Mockery::mock(Repository::class);
        $mockedCacheStore
            ->shouldReceive('get')
            ->once()
            ->andReturn(serialize($fakeCachedItem));

        $this->mock('cache', function (MockInterface $mock) use ($mockedCacheStore) {
            $mock->shouldReceive('store')
                ->once()
                ->andReturn($mockedCacheStore);
        });

        $this->assertEquals('some_cached_value', ConfigCat::get('some_feature'));
    }

    public function test_it_can_use_laravel_logger()
    {
        /** @var \Mockery\MockInterface $mock */
        $mock = Mockery::mock(\Psr\Log\LoggerInterface::class);
        $mock->shouldReceive('error')
            ->with(Mockery::on(function ($message) {
                return Str::contains($message, "Evaluating getValue('some_feature')");
            }), Mockery::type('array'));

        if ($this->app->version() >= '5.6.0') {
            Log::shouldReceive('channel')->once()->andReturn($mock);
        } else {
            fclose(STDERR);
            $this->instance('log', $mock);
        }

        ConfigCat::get('some_feature');
    }

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

    public function test_a_default_value_can_be_passed_when_resolving_feature_flags()
    {
        $this->mock(ClientInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getValue')
                ->once()
                ->with('foo', 'bar', null);
        });

        ConfigCat::get('foo', 'bar');
    }

    public function test_null_as_a_default_value_will_use_the_default_value_configured_for_the_package()
    {
        $this->mock(ClientInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getValue')
                ->once()
                ->with('foo', 'some_default', null);
        });

        ConfigCat::get('foo', null);
    }

    public function test_the_user_handler_can_be_used_when_resolving_feature_flags()
    {
        $this->mock(ClientInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getValue')
                ->once()
                ->with('some_feature', false, \Mockery::on(function (\ConfigCat\User $user) {
                    return $user->getIdentifier() === '123'
                        && $user->getAttribute('Email') === 'foo@baz.com';
                }));
        });

        $user = new \Illuminate\Foundation\Auth\User();
        $user->id = 123;
        $user->email = 'foo@baz.com';

        ConfigCat::get('some_feature', false, $user);
    }

    public function test_the_user_handler_will_use_the_logged_in_user_by_default()
    {
        $this->mock(ClientInterface::class, function (MockInterface $mock) {
            $mock->shouldReceive('getValue')
                ->once()
                ->with('some_feature', false, \Mockery::on(function (\ConfigCat\User $user) {
                    return $user->getIdentifier() === '456'
                        && $user->getAttribute('Email') === 'bar@foo.com';
                }));
        });

        $user = new \Illuminate\Foundation\Auth\User();
        $user->id = 456;
        $user->email = 'bar@foo.com';

        $this->actingAs($user);

        ConfigCat::get('some_feature', false);
    }
}
