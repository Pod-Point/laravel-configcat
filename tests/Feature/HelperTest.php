<?php

use PodPoint\ConfigCat\Facades\ConfigCat;

test('global helper can be used to check if a feature flag is enabled or disabled', function () {
    ConfigCat::fake([
        'some_enabled_feature' => true,
        'some_disabled_feature' => false,
    ]);

    expect(configcat('some_enabled_feature'))->toBeTrue();
    expect(configcat('some_disabled_feature'))->toBeFalse();
});

test('global helper returns false when a feature flag does not exist by default', function () {
    ConfigCat::fake(['some_feature' => true]);

    expect(configcat('some_unknown_feature'))->toBeFalse();
});

test('global helper can return a default value when a feature flag does not exist', function () {
    ConfigCat::fake(['some_feature' => true]);

    expect(configcat('unknown_feature', false))->toBeFalse();
    expect(configcat('unknown_feature', true))->toBeTrue();
    expect(configcat('unknown_feature', 'foo'))->toEqual('foo');
    expect(configcat('unknown_feature', 1234))->toEqual(1234);
    expect(configcat('unknown_feature', 12.34))->toEqual(12.34);
});

test('global helper can retrieve a text setting', function () {
    ConfigCat::fake(['some_feature_as_a_string' => 'foo']);

    expect(configcat('some_feature_as_a_string'))->toEqual('foo');
});

test('global helper can retrieve a number setting', function () {
    ConfigCat::fake([
        'a_whole_number' => 1234,
        'a_decimal_number' => 12.34,
    ]);

    expect(configcat('a_whole_number'))->toEqual(1234);
    expect(configcat('a_decimal_number'))->toEqual(12.34);
});

test('global helper relies on the facade', function () {
    ConfigCat::shouldReceive('get')->once()->with('some_feature');

    configcat('some_feature');
});

test('global helper can be used with a default value', function () {
    ConfigCat::shouldReceive('get')->once()->with('some_feature', true);

    configcat('some_feature', true);
});

test('global helper can be used with a given user', function () {
    $user = new \Illuminate\Foundation\Auth\User();
    $user->id = 123;
    $user->email = 'foo@bar.com';

    ConfigCat::shouldReceive('get')->once()->with('some_feature', false, $user);

    configcat('some_feature', false, $user);
});
