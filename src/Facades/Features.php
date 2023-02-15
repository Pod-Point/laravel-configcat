<?php

namespace PodPoint\ConfigCat\Facades;

use Illuminate\Support\Facades\Facade;
use PodPoint\ConfigCat\Support\FeaturesFake;

/**
 * @see \PodPoint\ConfigCat\ConfigCat
 */
class Features extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'features';
    }

    /**
     * Recommended to be used with in-memory unit/integration tests scenario.
     *
     * @param array $flagsToFake
     * @return FeaturesFake
     */
    public static function fake(array $flagsToFake = []): FeaturesFake
    {
        if (! app()->environment('testing')) {
            throw new \RuntimeException('fake() can only be used within a *testing* environment');
        }

        if (static::isFake()) {
            return (static::$resolvedInstance[static::getFacadeAccessor()])->fake($flagsToFake);
        }

        static::swap($fake = new FeaturesFake(static::getFacadeRoot(), $flagsToFake));

        return $fake;
    }

    protected static function isFake(): bool
    {
        $name = static::getFacadeAccessor();

        return isset(static::$resolvedInstance[$name]) &&
            static::$resolvedInstance[$name] instanceof FeaturesFake;
    }
}
