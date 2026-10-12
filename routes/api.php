<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\CaseApiController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Medical Case Management REST API Routes
|--------------------------------------------------------------------------
|
| Authenticated via Laravel Sanctum Bearer tokens.
| Return shapes conform to { success, message, data, (errors) }.
|
*/

// Public authentication endpoint
Route::post('/login', [AuthApiController::class, 'login'])->name('api.login');

// Protected API routes
Route::middleware('auth:sanctum')->group(function () {
    // Current authenticated user & token revocation
    Route::get('/user', fn (Request $request) => response()->json([
        'success' => true,
        'message' => 'Current user profile.',
        'data' => $request->user(),
    ]));
    Route::post('/logout', [AuthApiController::class, 'logout'])->name('api.logout');

    // Case Management endpoints
    Route::get('/cases', [CaseApiController::class, 'index'])->name('api.cases.index');
    Route::post('/cases', [CaseApiController::class, 'store'])->name('api.cases.store');
    Route::get('/cases/{id}', [CaseApiController::class, 'show'])->name('api.cases.show');
    Route::put('/cases/{id}', [CaseApiController::class, 'update'])->name('api.cases.update');
    Route::delete('/cases/{id}', [CaseApiController::class, 'destroy'])->name('api.cases.destroy');
});
