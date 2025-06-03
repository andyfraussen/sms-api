<?php

use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['auth:sanctum'])
    ->group(function () {

        Route::apiResource('schools', SchoolController::class);

        Route::apiResource('students', StudentController::class);
        Route::apiResource('attendances', AttendanceController::class);             // teachers only
    });
