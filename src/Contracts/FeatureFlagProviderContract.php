<?php

namespace PodPoint\ConfigCat\Contracts;

interface FeatureFlagProviderContract
{
    /**
     * @param  string  $featureKey
     * @param  mixed|null  $user
     * @return bool|string|int
     */
    public function get(string $featureKey, $user = null);

    /**
     * @param  array  $flagsToOverride
     * @return void
     */
    public function override(array $flagsToOverride);
}
