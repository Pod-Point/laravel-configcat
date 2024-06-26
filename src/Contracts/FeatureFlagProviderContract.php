<?php

namespace PodPoint\ConfigCat\Contracts;

interface FeatureFlagProviderContract
{
    /**
     * @param string  $featureKey
     * @param mixed|null $default
     * @param mixed|null $user
     * @return mixed
     */
    public function get(string $featureKey, mixed $default = null, mixed $user = null): mixed;

    /**
     * @param  array  $flagsToOverride
     * @return void
     */
    public function override(array $flagsToOverride): void;
}
