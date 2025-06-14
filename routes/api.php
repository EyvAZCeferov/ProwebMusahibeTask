<?php

use App\Http\Controllers\Api\AccountController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BankNotesController;
use App\Http\Controllers\Api\CurrenciesController;
use App\Http\Controllers\Api\WithdrawalController;
use App\Http\Controllers\Api\TransactionController;
use App\Http\Controllers\Api\TransferController;
use App\Http\Controllers\Api\TranslationsController;
use App\Http\Controllers\System\FallbackController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    Route::get("me", [AuthController::class, 'me']);
    Route::apiResource("account", AccountController::class);
    Route::get("currencies", [CurrenciesController::class, 'currencies']);

    Route::middleware(['throttle:10,1', 'role:person'])->group(function () {
        Route::post('/withdraw', WithdrawalController::class);

        Route::post('/transfers/self', [TransferController::class, 'selfTransfer']);
        Route::post('/transfers/external', [TransferController::class, 'externalTransfer']);
    });
    Route::apiResource('transactions', TransactionController::class)->only(['index', 'show']);

    Route::middleware('role:superadmin')->group(function () {
        Route::delete('/transactions/{transaction}', [TransactionController::class, 'destroy']);
        Route::apiResource("translations", TranslationsController::class)->except([
            'show',
            'update',
        ]);
    });

    Route::middleware('role:superadmin|manager')->group(function () {
        Route::apiResource("banknotes", BankNotesController::class);
    });
});

Route::fallback([FallbackController::class, 'indexApi']);
