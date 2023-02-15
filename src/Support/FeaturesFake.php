<?php

namespace PodPoint\ConfigCat\Support;

use PodPoint\ConfigCat\Contracts\FeatureFlagProviderContract;
use Illuminate\Support\Arr;

class FeaturesFake
{
    /** @var FeatureFlagProviderContract */
    protected $provider;
    /** @var array */
    protected $featureFlags = [];
    /** @var array */
    protected $flagCounts = [];

    public function __construct(FeatureFlagProviderContract $provider, $featureFlags = [])
    {
        $this->provider = $provider;

        $this->fake($featureFlags);
    }

    /**
     * Defines the faked feature flags.
     *
     * @param array $featureFlags
     * @return self
     */
    public function fake($featureFlags = []): self
    {
        $this->featureFlags = Arr::wrap($featureFlags);

        return $this;
    }

    /**
     * Retrieve a faked feature flag if it exists. Returns false if the faked
     * feature flag is undefined.
     *
     * @param string $feature
     * @return bool|string|int
     */
    public function get(string $feature)
    {
        $featureValue = $this->featureFlags[$feature] ?? false;

        if (is_array($featureValue)) {
            $execution = $this->getCount($feature);

            // if the array has run out of values, then use the last position
            $featureValue = ! is_null($featureValue[$execution] ?? null) ?
                $featureValue[$execution] :
                Arr::last($featureValue);
        }

        Arr::set($this->flagCounts, $feature, $this->getCount($feature) + 1);

        return $featureValue;
    }

    /**
     * Forwards any other calls to the feature flag provider instance.
     *
     * @param string $method
     * @param array $args
     * @return void
     */
    public function __call(string $method, array $args)
    {
        return $this->provider->{$method}(...$args);
    }

    protected function getCount(string $feature)
    {
        return Arr::get($this->flagCounts, $feature, 0);
    }
}
