<?php

namespace PodPoint\ConfigCat\Contracts;

interface ProviderContract
{
    /**
     * @param string $feature
     * @param mixed|null $user
     * @return bool|string|int
     */
    public function get(string $feature, $user = null);

    /**
     * @param array $flagsToOverride
     * @return void
     */
    public function override(array $flagsToOverride);
}
