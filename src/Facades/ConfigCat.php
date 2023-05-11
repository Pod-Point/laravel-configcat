<?php

namespace PodPoint\ConfigCat\Facades;

use Illuminate\Support\Facades\Facade;
use PodPoint\ConfigCat\Support\ConfigCatFake;

/**
 * @method static mixed get(string $featureKey, $default = null, $user = null)
 * @method static void override(array $flagsToOverride = [])
 *
 * @see \PodPoint\ConfigCat\ConfigCat
 */
class ConfigCat extends Facade
{
    /**
     * @inheritDoc
     */
    protected static function getFacadeAccessor(): string
    {
        return 'configcat';
    }

    /**
     * Fakes the ConfigCat facade completely using while using an array in-memory to
     * store the faked feature flags.
     *
     * Recommended to be used with in-memory unit/integration tests scenario instead
     * of end-to-end browser tests.
     *
     * @param  array  $flagsToFake
     * @return ConfigCatFake
     */
    public static function fake(array $flagsToFake = []): ConfigCatFake
    {
        if (! app()->environment('testing')) {
            throw new \RuntimeException('fake() can only be used within a *testing* environment');
        }

        if (static::isFake()) {
            return static::$resolvedInstance[static::getFacadeAccessor()]->fake($flagsToFake);
        }

        static::swap($fake = new ConfigCatFake(static::getFacadeRoot(), $flagsToFake));

        return $fake;
    }

    protected static function isFake(): bool
    {
        $name = static::getFacadeAccessor();

        return isset(static::$resolvedInstance[$name]) &&
            static::$resolvedInstance[$name] instanceof ConfigCatFake;
    }
}
