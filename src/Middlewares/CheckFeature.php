<?php

namespace PodPoint\ConfigCat\Middlewares;

use Closure;
use PodPoint\ConfigCat\Facades\Features;
use Symfony\Component\HttpFoundation\Response;

class CheckFeature
{
    /**
     * Aborts with a 404 if a feature flag is undefined or explicitly set to false.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $featureName
     * @return mixed
     */
    public function handle($request, Closure $next, string $featureName)
    {
        abort_if(Features::get($featureName) === false, Response::HTTP_NOT_FOUND);

        return $next($request);
    }
}
