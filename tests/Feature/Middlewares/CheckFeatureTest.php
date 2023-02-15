<?php

namespace PodPoint\ConfigCat\Tests\Feature\Middleware;

use Illuminate\Support\Facades\Route;
use PodPoint\ConfigCat\Facades\Features;
use PodPoint\ConfigCat\Tests\TestCase;

class CheckFeatureTest extends TestCase
{
    public function test_the_middleware_can_hide_features()
    {
        Features::fake(['some_feature' => false]);

        Route::get('/foo', function () {
            return response('Bar!');
        })->middleware('feature:some_feature');

        $this->get('/foo')->assertStatus(404);
    }

    public function test_the_middleware_can_show_features()
    {
        Features::fake(['some_feature' => true]);

        Route::post('/foo', function () {
            return response('Bar!');
        })->middleware('feature:some_feature');

        $this->post('/foo')->assertSuccessful();
    }

    public function test_the_middleware_can_show_features_when_the_feature_flag_exists_and_is_a_string()
    {
        Features::fake(['some_feature' => 'foo']);

        Route::post('/foo', function () {
            return response('Bar!');
        })->middleware('feature:some_feature');

        $this->post('/foo')->assertSuccessful();
    }

    public function test_the_middleware_can_show_features_when_the_feature_flag_exists_and_is_an_integer()
    {
        Features::fake(['some_feature' => 123]);

        Route::post('/foo', function () {
            return response('Bar!');
        })->middleware('feature:some_feature');

        $this->post('/foo')->assertSuccessful();
    }

    public function test_features_that_dont_exist_are_treated_like_disabled_features_by_the_middleware()
    {
        Route::get('/foo', function () {
            return response('Bar!');
        })->middleware('feature:foo');

        $this->get('/foo')->assertStatus(404);
    }
}
