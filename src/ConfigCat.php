<?php

namespace PodPoint\ConfigCat;

use ConfigCat\ClientInterface;
use ConfigCat\ConfigCatClient;
use ConfigCat\Override\FlagOverrides;
use ConfigCat\Override\OverrideBehaviour;
use ConfigCat\Override\OverrideDataSource;
use ConfigCat\User;
use Illuminate\Support\Facades\File;
use PodPoint\ConfigCat\Contracts\FeatureFlagProviderContract;

class ConfigCat implements FeatureFlagProviderContract
{
    protected ConfigCatClient|ClientInterface $configCatClient;

    public mixed $defaultValue = false;

    protected ?string $userTransformer = null;

    protected ?string $overridesFilePath;

    public function __construct(
        ClientInterface $configCatClient,
        $defaultValue = false,
        string $userTransformer = null,
        string $overridesFilePath = null
    ) {
        $this->configCatClient = $configCatClient;
        $this->defaultValue = $defaultValue;
        $this->userTransformer = $userTransformer;
        $this->overridesFilePath = $overridesFilePath;

        if ($overridesFilePath) {
            $this->localFile($overridesFilePath);
        }
    }

    /**
     * Retrieve a ConfigCat feature flag. According to the ConfigCat SDK it
     * will return false if the flag is undefined or if something went wrong.
     *
     * @param string  $featureKey
     * @param mixed|null $default
     * @param mixed|null $user
     * @return mixed
     */
    public function get(string $featureKey, mixed $default = null, mixed $user = null): mixed
    {
        $default = is_null($default) ? $this->defaultValue : $default;
        $user = $this->transformUser(is_null($user) ? auth()->user() : $user);

        return $this->configCatClient->getValue($featureKey, $default, $user);
    }

    /**
     * Conditionally apply the transformation of the user representation using
     * a callable Class.
     *
     * @param mixed|null $user
     * @return User|null
     */
    private function transformUser(mixed $user = null): ?User
    {
        if (! $user || ! $this->userTransformer || ! class_exists($this->userTransformer)) {
            return null;
        }

        $transformer = new $this->userTransformer;

        return is_callable($transformer) ? $transformer($user) : null;
    }

    /**
     * Setup the overrides for ConfigCat options.
     *
     * @param ?string $filepath
     * @return FlagOverrides|null
     */
    public static function overrides(?string $filepath): ?FlagOverrides
    {
        return $filepath ? new FlagOverrides(
            OverrideDataSource::localFile(self::localFile($filepath)),
            OverrideBehaviour::LOCAL_ONLY
        ) : null;
    }

    /**
     * Usually preferred for end-to-end test scenario where fakes/mocks are not
     * applicable. The feature flags are saved temporarily into a JSON file and
     * will **only** be read from it if overrides are enabled from the
     * configuration.
     *
     * @param array $flagsToOverride
     * @return void
     */
    public function override(array $flagsToOverride): void
    {
        if (! app()->environment('production') && $this->overridesFilePath) {
            File::put($this->overridesFilePath, json_encode([
                'flags' => $flagsToOverride,
            ]));
        }
    }

    /**
     * Resolve the file path to use with overrides. This will also make sure
     * the path and file exist along the way.
     *
     * @param string $filepath
     * @return string
     */
    private static function localFile(string $filepath): string
    {
        if (! File::exists($filepath)) {
            $directory = rtrim(strstr($filepath, basename($filepath), true), '/');
            File::makeDirectory($directory, 0755, true, true);
            File::put($filepath, '{"flags":{}}');
        }

        return $filepath;
    }
}
