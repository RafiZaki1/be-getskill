<?php

use App\Http\Controllers\Course\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');





Route::middleware('enable.cors')->group(function () {
    Route::middleware('auth:sanctum')->group(function () {
        // management course
        Route::resource('courses', CourseController::class)->except(['index', 'show']);
        Route::get('course-statistic/{slug}', [CourseController::class, 'statistic']);
        // Route::post('course-vouchers/{courseSlug}', [CourseVoucherController::class, 'store']);
        // Route::post('course-voucher-users', [CourseVoucherUserController::class, 'store']);
        // Route::post('course-reviews/{course}', [CourseReviewController::class, 'store']);
        // Route::put('course-reviews/{course_review}', [CourseReviewController::class, 'update']);
        // Route::delete('course-reviews/{course_review}', [CourseReviewController::class, 'destroy']);
        Route::get('course-by-submodule/{subModule}', [CourseController::class, 'getBySubModule']);
    });
});
