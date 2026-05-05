<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\ApiSetting;

class VerifyGasToken
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized - Token not provided'], 401);
        }

        $apiSetting = ApiSetting::where('api_token', $token)->first();

        if (!$apiSetting || !$apiSetting->is_active) {
            return response()->json(['error' => 'Unauthorized - Invalid or inactive token'], 401);
        }

        return $next($request);
    }
}
