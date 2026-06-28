<?php

use App\Http\Controllers\Api\DeviceApiController;
use App\Http\Controllers\Api\GatewayController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| WebWA REST API (v1) — authenticated by API key
|--------------------------------------------------------------------------
| Header: Authorization: Bearer wwa_xxx.yyy   (atau X-Api-Key: ...)
*/
Route::prefix('v1')->middleware('apikey')->group(function () {
    Route::get('/me', function (Request $request) {
        $user = $request->user();

        return response()->json([
            'success' => true,
            'data' => [
                'name' => $user->name,
                'email' => $user->email,
                'plan' => $user->plan,
                'quota' => $user->monthly_quota,
                'used' => $user->messagesUsedThisMonth(),
                'remaining' => $user->remainingQuota(),
                'devices' => $user->devices()->count(),
            ],
        ]);
    });

    Route::post('/messages/text', [GatewayController::class, 'sendText']);
    Route::post('/messages/media', [GatewayController::class, 'sendMedia']);

    Route::get('/devices', [DeviceApiController::class, 'index']);
    Route::get('/devices/{device}/status', [DeviceApiController::class, 'status']);
    Route::get('/devices/{device}/qr', [DeviceApiController::class, 'qr']);
});
