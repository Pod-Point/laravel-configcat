<?php

namespace PodPoint\ConfigCat\Rules;

use PodPoint\ConfigCat\ConfigCat;
use Illuminate\Validation\Concerns\ValidatesAttributes;

class RequiredIfFeature
{
    use ValidatesAttributes;

    public function validate($attribute, $value, $parameters): bool
    {
        if (! is_string($parameters[0] ?? null)) {
            throw new \InvalidArgumentException(
                'First parameter for `required_if_feature` validation rule must be the name of the feature'
            );
        }

        if (ConfigCat::get($parameters[0]) !== false) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }
}
