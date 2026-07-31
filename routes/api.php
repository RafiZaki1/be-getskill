<?php

use App\Http\Controllers\Course\CourseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;

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
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::get('/login', function () {
        return response()->json(['message' => 'Unauthenticated. Please login first.'], 401);
    })->name('login');
    
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/logout', [AuthController::class, 'logout']);

        // ==========================================
        // GUEST / STUDENT LEARNING & CHECKOUT ROUTES
        // ==========================================

        // 1. Checkout & Pembayaran
        Route::post('/checkout', [\App\Http\Controllers\Guest\CheckoutController::class, 'checkout']);
        Route::get('/checkout/status/{transaction}', [\App\Http\Controllers\Guest\CheckoutController::class, 'checkStatus']);
        Route::post('/checkout/cancel/{transaction}', [\App\Http\Controllers\Guest\CheckoutController::class, 'cancel']);

        // 2. Pre-Test
        Route::get('/pre-test/{course}', [\App\Http\Controllers\Guest\PreTestController::class, 'showInformation']);
        Route::post('/pre-test/{course}', [\App\Http\Controllers\Guest\PreTestController::class, 'store']);
        Route::get('/pre-test/result/{course}', [\App\Http\Controllers\Guest\PreTestController::class, 'showResult']);

        // 3. Materi Modul
        Route::get('/modules/{module}/material', [\App\Http\Controllers\Guest\ModuleMaterialController::class, 'showMaterial']);

        // 4. Tugas Modul
        Route::get('/modules/{module}/tasks', [\App\Http\Controllers\Guest\ModuleTaskController::class, 'index']);
        Route::post('/tasks/{task}/submit', [\App\Http\Controllers\Guest\ModuleTaskController::class, 'submitTask']);
        Route::get('/tasks/submission/{submission}', [\App\Http\Controllers\Guest\ModuleTaskController::class, 'showSubmission']);

        // 5. Kuis
        Route::get('/quizzes/{quiz}', [\App\Http\Controllers\Guest\QuizController::class, 'showInformation']);
        Route::post('/quizzes/{quiz}', [\App\Http\Controllers\Guest\QuizController::class, 'store']);
        Route::get('/quizzes/result/{quiz}', [\App\Http\Controllers\Guest\QuizController::class, 'showResult']);

        // 6. Post-Test
        Route::get('/post-test/{course}', [\App\Http\Controllers\Guest\PostTestController::class, 'showInformation']);
        Route::post('/post-test/{course}', [\App\Http\Controllers\Guest\PostTestController::class, 'store']);
        Route::get('/post-test/result/{course}', [\App\Http\Controllers\Guest\PostTestController::class, 'showResult']);

        // 7. Sertifikat
        Route::post('/certificates/verify-name', [\App\Http\Controllers\Guest\CertificateController::class, 'verifyName']);
        Route::get('/certificates/download/{code}', [\App\Http\Controllers\Guest\CertificateController::class, 'download']);
    });
});
