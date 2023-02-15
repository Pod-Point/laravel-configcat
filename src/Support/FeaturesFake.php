<?php

namespace PodPoint\ConfigCat\Support;

use PodPoint\ConfigCat\Contracts\ProviderContract;
use Illuminate\Support\Arr;

class FeaturesFake
{
    protected $provider;
    protected $featureFlags = [];
    protected $flagCounts = [];

    public function __construct(ProviderContract $provider, $featureFlags = [])
    {
        $this->provider = $provider;

        $this->fake($featureFlags);
    }

    public function fake($featureFlags = []): self
    {
        $this->featureFlags = Arr::wrap($featureFlags);

        return $this;
    }

    /**
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

    public function __call(string $method, array $args)
    {
        return $this->provider->{$method}(...$args);
    }

    protected function getCount(string $feature)
    {
        return Arr::get($this->flagCounts, $feature, 0);
    }
}
