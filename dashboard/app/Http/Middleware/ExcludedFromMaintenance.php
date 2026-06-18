<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;

class ExcludedFromMaintenance
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(App::isDownForMaintenance())
        {
            //dd('salut');
            //$excludedRoutes = config('maintenance.excluded_routes',[]);
            $excludedRoutes = Config::get('maintenance.excluded_routes', []);
            //dd($excludedRoutes);
            logger('Salut1');
            foreach ($excludedRoutes as $route)
            {
                logger('route', ['route'=>$route]);
                if ($request->is($route))
                {
                    logger('route1');
                    return $next($request);
                }
            }
            logger('salut2');
            abort(503);
        }
        return $next($request);
    }
}
