<?php

namespace Ptuchik\CoreUtilities\Middlewares;

use Closure;
use Ptuchik\CoreUtilities\Constants\HttpStatusCode;

/**
 * Class ForceSSL
 * @package Ptuchik\CoreUtilities\Middlewares
 */
class ForceSSL
{
    /**
     * Handle an incoming request.
     *
     * @param         $request
     * @param Closure $next
     *
     * @return \Illuminate\Http\RedirectResponse|mixed
     * @throws \Exception
     */
    public function handle($request, Closure $next)
    {
        // If protocol is set to HTTPS and it is not AppEngine's Cron request (as it is always calling HTTP),
        // redirect to HTTPS
        if (!$request->secure() && config('ptuchik-core-utilities.protocol') == 'https' && !$request->hasHeader('X-Appengine-Cron')) {
            return redirect()->secure($request->getRequestUri(), HttpStatusCode::MOVED_PERMANENTLY);
        }

        return $next($request);
    }
}
