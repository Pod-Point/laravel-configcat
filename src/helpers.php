<?php

use PodPoint\ConfigCat\Facades\ConfigCat;

if (! function_exists('configcat')) {
    /**
     * Retrieves a feature flag from a configured feature flag Provider configured within
     * the config/features.php file. It can return a boolean or string/int based flag.
     * If no feature flag is found, false will be returned.
     *
     * @param  string  $featureKey
     * @param  mixed|null  $default
     * @param  mixed|null  $user
     * @return mixed
     */
    function configcat(string $featureKey, $default = null, $user = null)
    {
        return call_user_func_array([ConfigCat::class, 'get'], func_get_args());
    }
}
