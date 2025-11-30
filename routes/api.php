<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AiChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Lấy thông tin user (mặc định Laravel)
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ========================
// ⭐ ROUTE AI CHAT (KHÔNG CSRF, KHÔNG 403)
// ========================
Route::post('/ai/chat', [AiChatController::class, 'chat']);
