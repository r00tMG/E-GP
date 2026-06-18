<?php

namespace App\Http\Middleware;

use Closure;
use GeoIp2\Database\Reader;
use Illuminate\Http\Request;

class GeoBlock
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\JsonResponse
     */
    public function handle(Request $request, Closure $next)
    {
        logger('salut');
        $geoDbPath = public_path('geoip/GeoLite2-Country.mmdb ');
        $reader = new Reader($geoDbPath);
        $ipAddress = $request->ip();
        logger('address ip', ['ipAddress' => $ipAddress]);
        try {
            $record = $reader->country($ipAddress);
            $countryCode = $record->country->isoCode;
            logger('recherche de la logalistion par ip', ['record'=>$record, 'countryCode'=>$countryCode]);
            $blockedCountry = ['MA'];
            if (in_array($countryCode,$blockedCountry))
            {
                return response()->json([
                    'message' => 'Accés refusé pour vore région'
                ]);
            }

        }catch (\Exception $e)
        {
            return  response()->json([
                'message' => 'Impossible de determiner votre emplacement'
            ]);
        }
        return $next($request);
    }
}
