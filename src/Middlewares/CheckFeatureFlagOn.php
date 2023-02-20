<?php

namespace PodPoint\ConfigCat\Middlewares;

use Closure;
use PodPoint\ConfigCat\Facades\ConfigCat;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureFlagOn
{
    /**
     * Aborts the Request with a 404 if a feature flag is undefined or explicitly set to false.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $featureName
     * @return mixed
     */
    public function handle($request, Closure $next, string $featureName)
    {
        abort_unless(ConfigCat::get($featureName) === true, Response::HTTP_NOT_FOUND);

        return $next($request);
    }
}
