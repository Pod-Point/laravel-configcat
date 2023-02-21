<?php

namespace PodPoint\ConfigCat\Contracts;

interface FeatureFlagProviderContract
{
    /**
     * @param  string  $featureKey
     * @param  bool|string|int|float  $default
     * @param  mixed|null  $user
     * @return bool|string|int|float
     */
    public function get(string $featureKey, $default = false, $user = null);

    /**
     * @param  array  $flagsToOverride
     * @return void
     */
    public function override(array $flagsToOverride);
}
