<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AuthenticateApiKey
{
    public function handle(Request $request, Closure $next): Response
    {
        $plain = $this->extractKey($request);

        if (! $plain) {
            return response()->json([
                'success' => false,
                'message' => 'API key tidak ditemukan. Sertakan header Authorization: Bearer <key> atau X-Api-Key.',
            ], 401);
        }

        $apiKey = ApiKey::findByPlain($plain);

        if (! $apiKey) {
            return response()->json([
                'success' => false,
                'message' => 'API key tidak valid atau sudah dinonaktifkan.',
            ], 401);
        }

        $user = $apiKey->user;

        if (! $user || ! $user->is_active) {
            return response()->json([
                'success' => false,
                'message' => 'Akun pemilik API key tidak aktif.',
            ], 403);
        }

        $apiKey->markUsed();

        $request->setUserResolver(fn () => $user);
        $request->attributes->set('api_key', $apiKey);

        return $next($request);
    }

    protected function extractKey(Request $request): ?string
    {
        if ($bearer = $request->bearerToken()) {
            return $bearer;
        }

        return $request->header('X-Api-Key') ?: $request->input('api_key');
    }
}
