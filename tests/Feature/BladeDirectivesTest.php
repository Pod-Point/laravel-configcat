<?php

namespace PodPoint\ConfigCat\Tests\Feature;

use Illuminate\Support\Facades\Route;
use PodPoint\ConfigCat\Facades\ConfigCat;
use PodPoint\ConfigCat\Tests\TestCase;

class BladeDirectivesTest extends TestCase
{
    public function test_it_will_render_something_only_when_the_corresponding_feature_flag_is_enabled()
    {
        ConfigCat::fake([
            'enabled_feature' => true,
            'disabled_feature' => false,
        ]);

        Route::get('/foo', function () {
            return view('feature');
        });

        $this->get('/foo')->assertDontSee('I am hidden');
    }

    public function test_it_will_consider_an_unknown_feature_flag_to_be_disabled()
    {
        ConfigCat::fake([
            'enabled_feature' => true,
            'disabled_feature' => false,
        ]);

        Route::get('/foo', function () {
            return view('feature');
        });

        $this->get('/foo')->assertSee('You can see me');
    }

    public function test_it_will_consider_a_feature_flag_as_a_number_setting_to_be_disabled()
    {
        ConfigCat::fake([
            'enabled_feature' => 1234,
            'disabled_feature' => false,
        ]);

        Route::get('/foo', function () {
            return view('feature');
        });

        $this->get('/foo')->assertDontSee('I should be visible');
        $this->get('/foo')->assertSee('I should not be visible');
    }

    public function test_it_will_consider_a_feature_flag_as_a_text_setting_to_be_disabled()
    {
        ConfigCat::fake([
            'enabled_feature' => 'foobar',
            'disabled_feature' => false,
        ]);

        Route::get('/foo', function () {
            return view('feature');
        });

        $this->get('/foo')->assertDontSee('I should be visible');
        $this->get('/foo')->assertSee('I should not be visible');
    }

    public function test_it_supports_the_unlessconfigcat_directive()
    {
        ConfigCat::fake([
            'enabled_feature' => true,
            'disabled_feature' => false,
        ]);

        Route::get('/foo', function () {
            return view('feature');
        });

        $this->get('/foo')->assertSee('I am not hidden');
    }

    public function test_it_supports_the_else_directive()
    {
        ConfigCat::fake([
            'enabled_feature' => false,
            'disabled_feature' => false,
        ]);

        Route::get('/foo', function () {
            return view('feature');
        });

        $this->get('/foo')->assertDontSee('I should be visible');
        $this->get('/foo')->assertSee('I should not be visible');
    }

    public function test_it_supports_the_elseconfigcat_directive()
    {
        ConfigCat::fake([
            'enabled_feature' => true,
            'disabled_feature' => false,
        ]);

        Route::get('/foo', function () {
            return view('feature');
        });

        $this->get('/foo')->assertDontSee('You cannot see me');
        $this->get('/foo')->assertSee('You can see me');
    }
}
