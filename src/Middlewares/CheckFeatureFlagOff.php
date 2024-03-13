<?php

namespace PodPoint\ConfigCat\Middlewares;

use Closure;
use Illuminate\Http\Request;
use PodPoint\ConfigCat\Facades\ConfigCat;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureFlagOff
{
    /**
     * Aborts the Request with a 404 if a feature flag is explicitly set to true.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $featureKey
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $featureKey): mixed
    {
        abort_if(ConfigCat::get($featureKey) === true, Response::HTTP_NOT_FOUND);

        return $next($request);
    }
}
