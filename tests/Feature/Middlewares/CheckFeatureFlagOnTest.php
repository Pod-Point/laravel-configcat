<?php

namespace PodPoint\ConfigCat\Tests\Feature\Middleware;

use Illuminate\Support\Facades\Route;
use PodPoint\ConfigCat\Facades\ConfigCat;
use PodPoint\ConfigCat\Tests\TestCase;

class CheckFeatureFlagOnTest extends TestCase
{
    public function test_it_can_hide_routes_when_a_feature_is_disabled()
    {
        ConfigCat::fake(['some_feature' => false]);

        Route::get('/foo', function () {
            return response('Bar!');
        })->middleware('configcat.on:some_feature');

        $this->get('/foo')->assertStatus(404);
    }

    public function test_it_can_show_routes_when_a_feature_is_enabled()
    {
        ConfigCat::fake(['some_feature' => true]);

        Route::post('/foo', function () {
            return response('Bar!');
        })->middleware('configcat.on:some_feature');

        $this->post('/foo')->assertSuccessful();
    }

    public function test_text_settings_are_treated_like_disabled_features_by_it()
    {
        ConfigCat::fake(['some_feature' => 'foo']);

        Route::post('/foo', function () {
            return response('Bar!');
        })->middleware('configcat.on:some_feature');

        $this->post('/foo')->assertStatus(404);
    }

    public function test_number_settings_are_treated_like_disabled_features_by_it()
    {
        ConfigCat::fake(['some_feature' => 1234]);

        Route::post('/foo', function () {
            return response('Bar!');
        })->middleware('configcat.on:some_feature');

        $this->post('/foo')->assertStatus(404);
    }

    public function test_features_that_dont_exist_are_treated_like_disabled_features_by_it()
    {
        Route::get('/foo', function () {
            return response('Bar!');
        })->middleware('configcat.on:foo');

        $this->get('/foo')->assertStatus(404);
    }
}
