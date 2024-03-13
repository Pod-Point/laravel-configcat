<?php

namespace PodPoint\ConfigCat\Rules;

use Illuminate\Validation\Concerns\ValidatesAttributes;
use PodPoint\ConfigCat\Facades\ConfigCat;

class RequiredIfFeature
{
    use ValidatesAttributes;

    /**
     * @param string $attribute
     * @param mixed  $value
     * @param array $parameters
     * @return bool
     */
    public function validate(string $attribute, mixed $value, array $parameters): bool
    {
        if (! is_string($parameters[0] ?? null)) {
            throw new \InvalidArgumentException(
                'First parameter for `required_if_configcat` validation rule must be the key of a feature flag'
            );
        }

        if (! in_array($parameters[1] ?? null, ['true', 'false'])) {
            throw new \InvalidArgumentException(
                'Second parameter for `required_if_configcat` validation rule must be either true or false'
            );
        }

        if ($parameters[1] === 'true' && ConfigCat::get($parameters[0]) === true) {
            return $this->validateRequired($attribute, $value);
        }

        if ($parameters[1] === 'false' && ConfigCat::get($parameters[0]) === false) {
            return $this->validateRequired($attribute, $value);
        }

        return true;
    }
}
