<?php

use PodPoint\ConfigCat\Facades\Features;

if (! function_exists('feature')) {
    /**
     * Retrieves a feature flag from a configured feature flag Provider configured within
     * the config/features.php file. It can return a boolean or string/int based flag.
     * If no feature flag is found, false will be returned.
     *
     * @param string $feature
     * @param mixed|null $user
     * @return bool|string|int
     */
    function feature(string $feature, $user = null)
    {
        return $user
            ? Features::get($feature, $user)
            : Features::get($feature);
    }
}
