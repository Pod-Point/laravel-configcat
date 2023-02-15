<?php

namespace PodPoint\ConfigCat;

use PodPoint\ConfigCat\Contracts\ProviderContract;
use ConfigCat\ClientInterface;
use ConfigCat\ConfigCatClient;
use ConfigCat\Override\FlagOverrides;
use ConfigCat\Override\OverrideBehaviour;
use ConfigCat\Override\OverrideDataSource;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class ConfigCat implements ProviderContract
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
     * @param string $feature
     * @param mixed|null $user
     * @return bool|string|int
     */
    public function get(string $feature, $user = null)
    {
        $user = $this->transformUser($user ?: auth()->user());

        return $this->configCatClient->getValue($feature, false, $user);
    }

    private function transformUser($user = null): ?\ConfigCat\User
    {
        return $user && $this->userHandler && is_callable($this->userHandler)
            ? call_user_func($this->userHandler, $user)
            : null;
    }

    public static function overrides(string $filepath): ?FlagOverrides
    {
        return $filepath ? new FlagOverrides(
            OverrideDataSource::localFile(self::localFile($filepath)),
            OverrideBehaviour::LOCAL_ONLY
        ) : null;
    }

    /**
     * Usually preferred for end-to-end test scenario where fakes/mocks are not applicable.
     * The feature flags are saved temporarily into a JSON file and will **only** be read
     *  from it if overrides are enabled from the configuration.
     *
     * @param array $flagsToOverride
     * @return void
     */
    public function override(array $flagsToOverride)
    {
        if (app()->environment('testing') && $this->overridesFilePath) {
            File::put(self::localFile($this->overridesFilePath), json_encode(['flags' => $flagsToOverride]));
        }
    }

    private static function localFile(string $filepath): string
    {
        if (! File::exists($filepath)) {
            $directory = rtrim(Str::before($filepath, basename($filepath)), '/');
            File::makeDirectory($directory, 0755, true, true);
            File::put($filepath, '{"flags":{}}');
        }

        return $filepath;
    }
}
