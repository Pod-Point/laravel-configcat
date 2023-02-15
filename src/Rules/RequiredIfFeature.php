<?php

namespace PodPoint\ConfigCat\Rules;

use Illuminate\Validation\Concerns\ValidatesAttributes;
use PodPoint\ConfigCat\Facades\Features;

class RequiredIfFeature
{
    use ValidatesAttributes;

    /**
     * Makes a field required if a feature flag is found and is set to a truthy value
     * such as true (bool), an integer or a string.
     *
     * @param string $attribute
     * @param mixed $value
     * @param array $parameters
     * @return boolean
     */
    public function validate($attribute, $value, $parameters): bool
    {
        if (! is_string($parameters[0] ?? null)) {
            throw new \InvalidArgumentException(
                'First parameter for `required_if_feature` validation rule must be the name of the feature'
            );
        }

        if (Features::get($parameters[0])) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }
}
