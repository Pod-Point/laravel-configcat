<?php

use PodPoint\ConfigCat\Facades\ConfigCat;

if (! function_exists('configcat')) {
    /**
     * Retrieves a feature flag from a configured feature flag Provider configured within
     * the config/features.php file. It can return a boolean or string/int based flag.
     * If no feature flag is found, false will be returned.
     *
     * @param  string  $featureKey
     * @param  mixed|null  $user
     * @return bool|string|int
     */
    function configcat(string $featureKey, $user = null)
    {
        return $user
            ? ConfigCat::get($featureKey, $user)
            : ConfigCat::get($featureKey);
    }
}
