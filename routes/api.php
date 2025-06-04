<?php

use App\Http\Controllers\Api\AssessmentController;
use App\Http\Controllers\Api\AttendanceController;
use App\Http\Controllers\Api\SchoolController;
use App\Http\Controllers\Api\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')
    ->middleware(['auth:sanctum'])
    ->group(function () {
        Route::get('students/trashed', [StudentController::class, 'trashed'])
            ->name('students.trashed');

        Route::delete('students/{student}/force',
            [StudentController::class, 'delete'])
            ->name('students.delete');

        Route::post('students/{student}/restore', [StudentController::class, 'restore'])
            ->withTrashed()
            ->name('students.restore');

        Route::apiResource('schools', SchoolController::class);

        Route::apiResource('students', StudentController::class);

        Route::apiResource('attendances', AttendanceController::class);

        Route::apiResource('assessments', AssessmentController::class);
    });
