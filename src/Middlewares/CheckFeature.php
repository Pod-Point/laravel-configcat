<?php

namespace PodPoint\ConfigCat\Middlewares;

use PodPoint\ConfigCat\ConfigCat;
use Closure;
use Symfony\Component\HttpFoundation\Response;

class CheckFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $featureName
     * @return mixed
     */
    public function handle($request, Closure $next, string $featureName)
    {
        abort_if(ConfigCat::get($featureName) === false, Response::HTTP_NOT_FOUND);

        return $next($request);
    }
}
