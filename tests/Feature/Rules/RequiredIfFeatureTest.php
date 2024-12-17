<?php

use Illuminate\Support\Facades\Validator;
use PodPoint\ConfigCat\Facades\ConfigCat;

test('a field can be required when a feature flag is enabled', function () {
    ConfigCat::fake(['some_feature' => true]);

    $validator = Validator::make([
        'foo' => 'bar',
    ], [
        'some_field' => 'required_if_configcat:some_feature,true',
    ]);

    expect($validator->errors()->has('some_field'))->toBeTrue();
});

test('a field can be optional when a feature flag is disabled', function () {
    ConfigCat::fake(['some_feature' => false]);

    $validator = Validator::make([
        'foo' => 'bar',
    ], [
        'some_field' => 'required_if_configcat:some_feature,true',
    ]);

    expect($validator->errors()->has('some_field'))->toBeFalse();
});

test('a field can be optional when a feature flag is enabled', function () {
    ConfigCat::fake(['some_feature' => true]);

    $validator = Validator::make([
        'foo' => 'bar',
    ], [
        'some_field' => 'required_if_configcat:some_feature,false',
    ]);

    expect($validator->errors()->has('some_field'))->toBeFalse();
});

test('a field can be required when a feature flag is disabled', function () {
    ConfigCat::fake(['some_feature' => false]);

    $validator = Validator::make([
        'foo' => 'bar',
    ], [
        'some_field' => 'required_if_configcat:some_feature,false',
    ]);

    expect($validator->errors()->has('some_field'))->toBeTrue();
});

test('a field is optional when a feature flag is defined as a string', function () {
    ConfigCat::fake(['some_feature' => 'foo']);

    $validator = Validator::make([
        'foo' => 'bar',
    ], [
        'some_field' => 'required_if_configcat:some_feature,true',
    ]);

    expect($validator->errors()->has('some_field'))->toBeFalse();
});

test('a field is optional when a feature flag is defined as a number', function () {
    ConfigCat::fake(['some_feature' => 1234]);

    $validator = Validator::make([
        'foo' => 'bar',
    ], [
        'some_field' => 'required_if_configcat:some_feature,true',
    ]);

    expect($validator->errors()->has('some_field'))->toBeFalse();
});
