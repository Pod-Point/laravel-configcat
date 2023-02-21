<?php

use PodPoint\ConfigCat\Facades\ConfigCat;

if (! function_exists('configcat')) {
    /**
     * Retrieves a feature flag from a configured feature flag Provider configured within
     * the config/features.php file. It can return a boolean or string/int based flag.
     * If no feature flag is found, false will be returned.
     *
     * @param  string  $featureKey
     * @param  bool|string|int|float  $default
     * @param  mixed|null  $user
     * @return bool|string|int|float
     */
    function configcat(string $featureKey, $default = false, $user = null)
    {
        return call_user_func_array([ConfigCat::class, 'get'], func_get_args());
    }
}
