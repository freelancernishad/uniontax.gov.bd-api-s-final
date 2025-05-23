<?php

namespace App\Http\Middleware;

use App\Models\AllowedOrigin;
use Closure;

class WhitelistOriginMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        // Get the 'Origin' header from the request
        $origin = $request->header('Origin');


        if ($request->is('api/global/divisions') || $request->is('api/global/districts/*')) {
            return $next($request);
        }


        // If the origin is empty, check if there is a wildcard (empty string) in the allowed origins
        if ($origin === '' || $origin === null) {
            // Check if there's an empty string '' in the allowed origins
            $allowedOrigin = AllowedOrigin::where('origin_url', 'postman')->exists();


            if ($request->is('payment/report/download') || $request->is('sonod/d/*') || $request->is('sonod/download/*') || $request->is('document/d/*') || $request->is('applicant/copy/download/*') || $request->is('sonod/invoice/download/*') || $request->is('download/reports/get-reports') || $request->is('holding/tax/invoice/*') || $request->is('holding/tax/certificate_of_honor/*')) {
                return $next($request);
            }



            // If empty origin is not allowed in the database, return a 403 response
            if (!$allowedOrigin) {
                return response()->json([
                    'message' => 'Access denied. Empty origin is not allowed.',
                ], 403);
            }



        } else {
            // Check if the origin exists in the database for non-empty origins
            $allowedOrigin = AllowedOrigin::where('origin_url', $origin)->exists();

            // If the origin is not allowed, return a 403 response
            if (!$allowedOrigin) {
                return response()->json([
                    'message' => 'Access denied. Your origin is not allowed.',
                ], 403);
            }
        }

        // If the origin is allowed, proceed with the request
        return $next($request);
    }
}
