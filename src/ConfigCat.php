<?php

namespace PodPoint\ConfigCat;

use ConfigCat\ClientInterface;
use ConfigCat\ConfigCatClient;
use ConfigCat\Override\FlagOverrides;
use ConfigCat\Override\OverrideBehaviour;
use ConfigCat\Override\OverrideDataSource;
use Illuminate\Support\Facades\File;
use PodPoint\ConfigCat\Contracts\FeatureFlagProviderContract;

class ConfigCat implements FeatureFlagProviderContract
{
    /** @var ConfigCatClient */
    protected $configCatClient;
    /** @var callable|null */
    protected $userHandler = null;
    /** @var string|null */
    protected $overridesFilePath;

    public function __construct(
        ClientInterface $configCatClient,
        callable $userHandler = null,
        string $overridesFilePath = null
    ) {
        $this->configCatClient = $configCatClient;
        $this->userHandler = $userHandler;
        $this->overridesFilePath = $overridesFilePath;
    }

    /**
     * Retrieve a ConfigCat feature flag. According to the ConfigCat SDK it
     * will return false if the flag is undefined or if something went wrong.
     *
     * @param  string  $featureKey
     * @param  mixed|null  $user
     * @return bool|string|int
     */
    public function get(string $featureKey, $user = null)
    {
        $user = $this->transformUser($user ?: auth()->user());

        return $this->configCatClient->getValue($featureKey, false, $user);
    }

    /**
     * Conditionally apply the transformation of the user representation.
     *
     * @param  mixed|null  $user
     * @return \ConfigCat\User|null
     */
    private function transformUser($user = null): ?\ConfigCat\User
    {
        return $user && $this->userHandler && is_callable($this->userHandler)
            ? call_user_func($this->userHandler, $user)
            : null;
    }

    /**
     * Setup the overrides for ConfigCat options.
     *
     * @param  string  $filepath
     * @return FlagOverrides|null
     */
    public static function overrides(string $filepath): ?FlagOverrides
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
     * @param  array  $flagsToOverride
     * @return void
     */
    public function override(array $flagsToOverride)
    {
        if (app()->environment('testing') && $this->overridesFilePath) {
            File::put(self::localFile($this->overridesFilePath), json_encode([
                'flags' => $flagsToOverride,
            ]));
        }
    }

    /**
     * Resolve the file path to use with overrides. This will also make sure
     * the path and file exist along the way.
     *
     * @param  string  $filepath
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
