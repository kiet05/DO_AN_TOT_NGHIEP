<?php

namespace App\Services;

use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ActivityLogger
{
    public static function log(?int $userId, string $action, array $payload = [], ?int $causerId = null, ?Request $request = null): void
    {
        $request = $request ?: request();

        UserActivity::create([
            'user_id' => $userId,
            'causer_id' => $causerId ?? Auth::id(),
            'action' => $action,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'payload' => $payload,
        ]);
    }
}
