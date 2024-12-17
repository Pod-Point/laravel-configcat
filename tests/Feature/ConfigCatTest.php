<?php

use Carbon\Carbon;
use ConfigCat\Cache\ConfigEntry;
use ConfigCat\ClientInterface;
use Illuminate\Contracts\Cache\Repository;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use PodPoint\ConfigCat\Facades\ConfigCat;

beforeEach(function () {
    config()->set('configcat.default', 'some_default');
});

test('it can be configured to use a default value', function () {
    expect(ConfigCat::get('unknown_feature'))->toEqual('some_default');
});

test('it can use laravel cache', function () {
    $entry = ConfigEntry::fromConfigJson(json_encode([
        'f' => [
            'some_feature' => [
                'v' => ['s' => 'some_cached_value'],
                'i' => '430bded3',
                't' => 1,
            ],
        ],
    ]), '430bded3', Carbon::now()->timestamp * 1000);

    /** @var \Mockery\MockInterface $mockedCacheStore */
    $mockedCacheStore = Mockery::mock(Repository::class);
    $mockedCacheStore
        ->shouldReceive('get')
        ->once()
        ->andReturn($entry->serialize());

    $this->mock('cache', function (MockInterface $mock) use ($mockedCacheStore) {
        $mock->shouldReceive('store')
            ->once()
            ->andReturn($mockedCacheStore);
    });

    expect(ConfigCat::get('some_feature'))->toEqual('some_cached_value');
});

test('it can use laravel logger', function () {
    /** @var \Mockery\MockInterface $mock */
    $mock = Mockery::mock(\Psr\Log\LoggerInterface::class);
    $mock->shouldReceive('error')
        ->with(Mockery::on(function ($message) {
            return Str::contains($message, "Evaluating getValue('some_feature')");
        }), Mockery::type('array'));

    Log::shouldReceive('channel')->once()->andReturn($mock);

    ConfigCat::get('some_feature');
});

test('the facade can override feature flags', function () {
    config(['configcat.overrides.enabled' => true]);

    ConfigCat::override([
        'enabled_feature' => true,
        'disabled_feature' => false,
    ]);

    expect(configcat('enabled_feature'))->toBeTrue();
    expect(configcat('disabled_feature'))->toBeFalse();

    expect(File::exists(storage_path('app/features/configcat.json')))->toBeTrue();
    expect(File::get(storage_path('app/features/configcat.json')))->toEqual('{"flags":{"enabled_feature":true,"disabled_feature":false}}');
});

test('config cat client is called when resolving feature flags', function () {
    $this->mock(ClientInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('getValue')->once();
    });

    ConfigCat::get('some_feature');
});

test('a default value can be passed when resolving feature flags', function () {
    $this->mock(ClientInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('getValue')
            ->once()
            ->with('foo', 'bar', null);
    });

    ConfigCat::get('foo', 'bar');
});

test('null as a default value will use the default value configured for the package', function () {
    $this->mock(ClientInterface::class, function (MockInterface $mock) {
        $mock->shouldReceive('getValue')
            ->once()
            ->with('foo', 'some_default', null);
    });

    ConfigCat::get('foo', null);
});

test('the user handler can be used when resolving feature flags', function () {
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
});

test('the user handler will use the logged in user by default', function () {
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
});
