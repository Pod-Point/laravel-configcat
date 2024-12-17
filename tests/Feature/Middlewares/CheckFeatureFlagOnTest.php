<?php

use Illuminate\Support\Facades\Route;
use PodPoint\ConfigCat\Facades\ConfigCat;

test('it can hide routes when a feature is disabled', function () {
    ConfigCat::fake(['some_feature' => false]);

    Route::get('/foo', function () {
        return response('Bar!');
    })->middleware('configcat.on:some_feature');

    $this->get('/foo')->assertStatus(404);
});

test('it can show routes when a feature is enabled', function () {
    ConfigCat::fake(['some_feature' => true]);

    Route::post('/foo', function () {
        return response('Bar!');
    })->middleware('configcat.on:some_feature');

    $this->post('/foo')->assertSuccessful();
});

test('text settings are treated like disabled features by it', function () {
    ConfigCat::fake(['some_feature' => 'foo']);

    Route::post('/foo', function () {
        return response('Bar!');
    })->middleware('configcat.on:some_feature');

    $this->post('/foo')->assertStatus(404);
});

test('number settings are treated like disabled features by it', function () {
    ConfigCat::fake(['some_feature' => 1234]);

    Route::post('/foo', function () {
        return response('Bar!');
    })->middleware('configcat.on:some_feature');

    $this->post('/foo')->assertStatus(404);
});

test('features that dont exist are treated like disabled features by it', function () {
    Route::get('/foo', function () {
        return response('Bar!');
    })->middleware('configcat.on:foo');

    $this->get('/foo')->assertStatus(404);
});
