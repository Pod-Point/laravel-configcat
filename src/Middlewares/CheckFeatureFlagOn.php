<?php

namespace PodPoint\ConfigCat\Middlewares;

use Closure;
use Illuminate\Http\Request;
use PodPoint\ConfigCat\Facades\ConfigCat;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureFlagOn
{
    /**
     * Aborts the Request with a 404 if a feature flag is undefined or explicitly set to false.
     *
     * @param Request $request
     * @param Closure $next
     * @param string  $featureKey
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $featureKey): mixed
    {
        abort_unless(ConfigCat::get($featureKey) === true, Response::HTTP_NOT_FOUND);

        return $next($request);
    }
}
