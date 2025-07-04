<?php

use App\Http\Controllers\Api\AuthApiController;
use App\Http\Controllers\Api\PlanController;
use App\Http\Controllers\Api\TenantController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [AuthApiController::class, 'register']);

Route::middleware(['auth:sanctum','ability:tenant-logged'])->group(function () {
    Route::prefix('tenant')->group(function () {
        Route::post('update', [TenantController::class, 'update']);
        Route::post('destroy', [TenantController::class, 'destroy']);
        Route::post('assign-plan', [TenantController::class, 'assignPlan']);
    });

    Route::prefix('plan')->group(function () {
        Route::get('list', [PlanController::class, 'list']);
        Route::post('store', [PlanController::class, 'store']);
        Route::post('update', [PlanController::class, 'update']);
        Route::post('destroy/{plan}', [PlanController::class, 'destroy']);
    });

    Route::prefix('user')->group(function () {
        Route::get('list', [UserController::class, 'list']);
        Route::post('store', [UserController::class, 'store']);
        Route::post('update/{user}', [UserController::class, 'update']);
        Route::post('destroy/{user}', [UserController::class, 'destroy']);
    });
});
