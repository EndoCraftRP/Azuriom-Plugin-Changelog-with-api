<?php

namespace Azuriom\Plugin\Changelog\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthenticateChangelogApiToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();

        if (empty($token)) {
            Log::warning('Changelog API: Missing API token', ['ip' => $request->ip()]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $configuredTokens = setting('changelog.api_tokens');

        if (empty($configuredTokens)) {
            Log::warning('Changelog API: No API tokens configured in plugin settings', ['ip' => $request->ip()]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $tokens = array_map('trim', explode(',', $configuredTokens));

        $authenticated = false;
        foreach ($tokens as $configuredToken) {
            if (hash_equals($configuredToken, $token)) {
                $authenticated = true;
                break;
            }
        }

        if (! $authenticated) {
            Log::warning('Changelog API: Invalid API token', ['ip' => $request->ip()]);
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }
}
