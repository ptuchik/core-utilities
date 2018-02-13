<?php

namespace Ptuchik\CoreUtilities\Middlewares;

use Closure;
use Ptuchik\CoreUtilities\Constants\HttpStatusCode;

/**
 * Class AppEngineCron - Filters App Engine Cron jobs
 * @package Ptuchik\CoreUtilities\Middlewares
 */
class AppEngineCron
{
    /**
     * Handle an incoming request.
     *
     * @param         $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\JsonResponse|mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        // If AppEngine's Cron header does not exist, reject request
        if (!$request->hasHeader('X-Appengine-Cron')) {
            return response()->json(trans(config('ptuchik-core-utilities.translations_prefix').'.unauthorized'),
                HttpStatusCode::UNAUTHORIZED);
        }

        return $next($request);
    }
}