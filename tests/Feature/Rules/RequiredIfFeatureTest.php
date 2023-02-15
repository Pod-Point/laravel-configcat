<?php

namespace PodPoint\ConfigCat\Tests\Feature\Rules;

use Illuminate\Support\Facades\Validator;
use PodPoint\ConfigCat\Facades\Features;
use PodPoint\ConfigCat\Tests\TestCase;

class RequiredIfFeatureTest extends TestCase
{
    public function test_a_field_can_be_required_with_the_validation_rule_when_a_feature_flag_is_enabled()
    {
        Features::fake(['some_feature' => true]);

        $validator = Validator::make([
            'foo' => 'bar',
        ], [
            'some_field' => 'required_if_feature:some_feature',
        ]);

        $this->assertTrue($validator->errors()->has('some_field'));
    }

    public function test_a_field_can_be_optional_with_the_validation_rule_when_a_feature_flag_is_disabled()
    {
        Features::fake(['some_feature' => false]);

        $validator = Validator::make([
            'foo' => 'bar',
        ], [
            'some_field' => 'required_if_feature:some_feature',
        ]);

        $this->assertFalse($validator->errors()->has('some_field'));
    }

    public function test_a_field_can_be_required_when_a_feature_flag_is_defined_as_a_string()
    {
        Features::fake(['some_feature' => 'foo']);

        $validator = Validator::make([
            'foo' => 'bar',
        ], [
            'some_field' => 'required_if_feature:some_feature',
        ]);

        $this->assertTrue($validator->errors()->has('some_field'));
    }

    public function test_a_field_can_be_required_when_a_feature_flag_is_defined_as_an_integer()
    {
        Features::fake(['some_feature' => 123]);

        $validator = Validator::make([
            'foo' => 'bar',
        ], [
            'some_field' => 'required_if_feature:some_feature',
        ]);

        $this->assertTrue($validator->errors()->has('some_field'));
    }
}
