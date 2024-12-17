<?php

use Illuminate\Support\Facades\Route;
use PodPoint\ConfigCat\Facades\ConfigCat;

test('it will render something only when the corresponding feature flag is enabled', function () {
    ConfigCat::fake([
        'enabled_feature' => true,
        'disabled_feature' => false,
    ]);

    Route::get('/foo', function () {
        return view('feature');
    });

    $this->get('/foo')->assertDontSee('I am hidden');
});

test('it will consider an unknown feature flag to be disabled', function () {
    ConfigCat::fake([
        'enabled_feature' => true,
        'disabled_feature' => false,
    ]);

    Route::get('/foo', function () {
        return view('feature');
    });

    $this->get('/foo')->assertSee('You can see me');
});

test('it will consider a feature flag as a number setting to be disabled', function () {
    ConfigCat::fake([
        'enabled_feature' => 1234,
        'disabled_feature' => false,
    ]);

    Route::get('/foo', function () {
        return view('feature');
    });

    $this->get('/foo')->assertDontSee('I should be visible');
    $this->get('/foo')->assertSee('I should not be visible');
});

test('it will consider a feature flag as a text setting to be disabled', function () {
    ConfigCat::fake([
        'enabled_feature' => 'foobar',
        'disabled_feature' => false,
    ]);

    Route::get('/foo', function () {
        return view('feature');
    });

    $this->get('/foo')->assertDontSee('I should be visible');
    $this->get('/foo')->assertSee('I should not be visible');
});

test('it supports the unlessconfigcat directive', function () {
    ConfigCat::fake([
        'enabled_feature' => true,
        'disabled_feature' => false,
    ]);

    Route::get('/foo', function () {
        return view('feature');
    });

    $this->get('/foo')->assertSee('I am not hidden');
});

test('it supports the else directive', function () {
    ConfigCat::fake([
        'enabled_feature' => false,
        'disabled_feature' => false,
    ]);

    Route::get('/foo', function () {
        return view('feature');
    });

    $this->get('/foo')->assertDontSee('I should be visible');
    $this->get('/foo')->assertSee('I should not be visible');
});

test('it supports the elseconfigcat directive', function () {
    ConfigCat::fake([
        'enabled_feature' => true,
        'disabled_feature' => false,
    ]);

    Route::get('/foo', function () {
        return view('feature');
    });

    $this->get('/foo')->assertDontSee('You cannot see me');
    $this->get('/foo')->assertSee('You can see me');
});
