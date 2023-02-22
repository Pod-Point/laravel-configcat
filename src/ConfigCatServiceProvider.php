<?php

namespace PodPoint\ConfigCat;

use ConfigCat\Cache\LaravelCache;
use ConfigCat\ClientInterface;
use ConfigCat\ClientOptions;
use ConfigCat\ConfigCatClient;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;
use PodPoint\ConfigCat\Middlewares\CheckFeatureFlagOff;
use PodPoint\ConfigCat\Middlewares\CheckFeatureFlagOn;
use PodPoint\ConfigCat\Rules\RequiredIfFeature;

class ConfigCatServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerConfigCatClient();

        $this->registerFacade();

        $this->mergeConfigFrom(__DIR__.'/../config/configcat.php', 'configcat');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/configcat.php' => config_path('configcat.php'),
            ]);
        }

        $this->bladeDirectives();

        $this->middlewares();

        $this->validationRules();
    }

    private function registerConfigCatClient()
    {
        $this->app->singleton(ClientInterface::class, function ($app) {
            $logger = $app->version() >= '5.6.0'
                ? Log::channel($app['config']['configcat.log.channel'])
                : $app['log'];

            $options = [
                ClientOptions::CACHE => new LaravelCache(Cache::store($app['config']['configcat.cache.store'])),
                ClientOptions::CACHE_REFRESH_INTERVAL => $app['config']['configcat.cache.interval'],
                ClientOptions::LOGGER => $logger,
                ClientOptions::LOG_LEVEL => $app['config']['configcat.log.level'],
                ClientOptions::FLAG_OVERRIDES => $app['config']['configcat.overrides.enabled']
                    ? ConfigCat::overrides($app['config']['configcat.overrides.file'])
                    : null,
            ];

            return new ConfigCatClient($app['config']['configcat.key'], $options);
        });
    }

    private function registerFacade()
    {
        $this->app->singleton('configcat', function ($app) {
            return new ConfigCat(
                $app->make(ClientInterface::class),
                $app['config']['configcat.default'],
                $app['config']['configcat.user'],
                $app['config']['configcat.overrides.enabled']
                    ? $app['config']['configcat.overrides.file']
                    : null
            );
        });
    }

    protected function bladeDirectives()
    {
        Blade::directive('configcat', function (string $featureKey, $user = null) {
            $expression = $user ? "{$featureKey}, {$user}" : "{$featureKey}";

            return "<?php if (configcat({$expression}) === true): ?>";
        });

        Blade::directive('elseconfigcat', function (string $feature, $user = null) {
            $expression = $user ? "{$feature}, {$user}" : "{$feature}";

            return "<?php elseif (configcat({$expression}) === true): ?>";
        });

        Blade::directive('unlessconfigcat', function (string $feature, $user = null) {
            $expression = $user ? "{$feature}, {$user}" : "{$feature}";

            return "<?php if (configcat({$expression}) === false): ?>";
        });

        Blade::directive('endconfigcat', function () {
            return '<?php endif; ?>';
        });
    }

    protected function middlewares()
    {
        $this->app->make(Router::class)
            ->aliasMiddleware('configcat.on', CheckFeatureFlagOn::class)
            ->aliasMiddleware('configcat.off', CheckFeatureFlagOff::class);
    }

    protected function validationRules()
    {
        Validator::extendImplicit('required_if_configcat', RequiredIfFeature::class);
    }
}
