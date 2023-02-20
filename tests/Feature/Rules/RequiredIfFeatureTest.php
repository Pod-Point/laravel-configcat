<?php

namespace PodPoint\ConfigCat\Tests\Feature\Rules;

use Illuminate\Support\Facades\Validator;
use PodPoint\ConfigCat\Facades\ConfigCat;
use PodPoint\ConfigCat\Tests\TestCase;

class RequiredIfFeatureTest extends TestCase
{
    public function test_a_field_can_be_required_when_a_feature_flag_is_enabled()
    {
        ConfigCat::fake(['some_feature' => true]);

        $validator = Validator::make([
            'foo' => 'bar',
        ], [
            'some_field' => 'required_if_configcat:some_feature,true',
        ]);

        $this->assertTrue($validator->errors()->has('some_field'));
    }

    public function test_a_field_can_be_optional_when_a_feature_flag_is_disabled()
    {
        ConfigCat::fake(['some_feature' => false]);

        $validator = Validator::make([
            'foo' => 'bar',
        ], [
            'some_field' => 'required_if_configcat:some_feature,true',
        ]);

        $this->assertFalse($validator->errors()->has('some_field'));
    }

    public function test_a_field_can_be_optional_when_a_feature_flag_is_enabled()
    {
        ConfigCat::fake(['some_feature' => true]);

        $validator = Validator::make([
            'foo' => 'bar',
        ], [
            'some_field' => 'required_if_configcat:some_feature,false',
        ]);

        $this->assertFalse($validator->errors()->has('some_field'));
    }

    public function test_a_field_can_be_required_when_a_feature_flag_is_disabled()
    {
        ConfigCat::fake(['some_feature' => false]);

        $validator = Validator::make([
            'foo' => 'bar',
        ], [
            'some_field' => 'required_if_configcat:some_feature,false',
        ]);

        $this->assertTrue($validator->errors()->has('some_field'));
    }

    public function test_a_field_is_optional_when_a_feature_flag_is_defined_as_a_string()
    {
        ConfigCat::fake(['some_feature' => 'foo']);

        $validator = Validator::make([
            'foo' => 'bar',
        ], [
            'some_field' => 'required_if_configcat:some_feature,true',
        ]);

        $this->assertFalse($validator->errors()->has('some_field'));
    }

    public function test_a_field_is_optional_when_a_feature_flag_is_defined_as_a_number()
    {
        ConfigCat::fake(['some_feature' => 123]);

        $validator = Validator::make([
            'foo' => 'bar',
        ], [
            'some_field' => 'required_if_configcat:some_feature,true',
        ]);

        $this->assertFalse($validator->errors()->has('some_field'));
    }
}
