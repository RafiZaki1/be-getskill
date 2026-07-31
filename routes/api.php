<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Auth & Password Reset Controllers
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordResetController;

// Course Management Controllers
use App\Http\Controllers\Course\CourseController;
use App\Http\Controllers\Course\CategoryController;
use App\Http\Controllers\Course\SubCategoryController;
use App\Http\Controllers\Course\ModuleController;
use App\Http\Controllers\Course\SubModuleController;
use App\Http\Controllers\Course\CourseTestController;
use App\Http\Controllers\CourseTestQuestionController; // Might be in root or Course folder, depending on the original, I copied it to root.
use App\Http\Controllers\Course\UserCourseController;
use App\Http\Controllers\Course\UserCourseTestController;
use App\Http\Controllers\Course\QuizController;
use App\Http\Controllers\Course\UserQuizController;
use App\Http\Controllers\Course\CourseReviewController;
use App\Http\Controllers\Course\CourseVoucherController;
use App\Http\Controllers\Course\CourseVoucherUserController;
use App\Http\Controllers\Course\ModuleQuestionController;
use App\Http\Controllers\Course\ModuleTaskController;
use App\Http\Controllers\Course\SubmissionTaskController;

Route::middleware('enable.cors')->group(function () {
    
    // --- Public Course Routes ---
    Route::get('categories', [CategoryController::class, 'index']);
    Route::get('sub-categories/category/{category}', [SubCategoryController::class, 'getByCategory']);
    Route::get('sub-categories', [SubCategoryController::class, 'index']);
    
    Route::get('courses', [CourseController::class, 'index']);
    Route::get('courses/{slug}', [CourseController::class, 'show']);
    Route::get('course-by-submodule/{subModule}', [CourseController::class, 'getBySubModule']);
    Route::get('course-reviews', [CourseReviewController::class, 'index']);
    Route::get('course-reviews/{course_review}', [CourseReviewController::class, 'show']);
    Route::get('course-reviews-latest', [CourseReviewController::class, 'latest']);
    
    Route::get('guest-courses-user', [UserCourseController::class, 'userCourseActivity']);

    // --- Authentication ---
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/verify-otp', [AuthController::class, 'verifyOtp']);
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/forgot-password', [PasswordResetController::class, 'forgotPassword']);
    Route::post('/reset-password', [PasswordResetController::class, 'resetPassword']);

    // --- Authenticated Routes ---
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/user', function (Request $request) {
            return $request->user();
        });
        Route::post('/logout', [AuthController::class, 'logout']);

        // Management Course - General
        Route::resource('courses', CourseController::class)->except(['index', 'show']);
        Route::get('course-statistic/{slug}', [CourseController::class, 'statistic']);
        Route::patch('courses-ready/{course}', [CourseController::class, 'readyToUse']);
        Route::patch('courses-make-draft/{course}', [CourseController::class, 'makeDraft']);

        // Categories & SubCategories Admin
        Route::apiResource('categories', CategoryController::class)->except('index');
        Route::resource('sub-categories', SubCategoryController::class)->only(['update', 'destroy']);
        Route::post('sub-categories/{category}', [SubCategoryController::class, 'store']);

        // Modules
        Route::get('modules/{slug}', [ModuleController::class, 'index']);
        Route::get('modules-list/{slug}', [ModuleController::class, 'indexList']);
        Route::get('modules-by-slug/{slug}', [ModuleController::class, 'showByCourseSlug']);
        Route::get('modules/detail/{module}', [ModuleController::class, 'show']);
        Route::post('modules/{slug}', [ModuleController::class, 'store']);
        Route::resource('modules', ModuleController::class)->only(['update', 'destroy']);
        Route::patch('modules-forward/{module}', [ModuleController::class, 'forward']);
        Route::patch('modules-backward/{module}', [ModuleController::class, 'backward']);
        Route::put('course/{course}/arrange-modules', [CourseController::class, 'arrangeModules']);

        // SubModules
        Route::get('sub-modules/detail/{slug}', [SubModuleController::class, 'show']); // Added middleware in controller if needed
        Route::get('sub-modules/next/{slug}', [SubModuleController::class, 'next']);
        Route::get('sub-modules/prev/{slug}', [SubModuleController::class, 'prev']);
        Route::patch('sub-modules-forward/{subModule}', [SubModuleController::class, 'forward']);
        Route::patch('sub-modules-backward/{subModule}', [SubModuleController::class, 'backward']);
        Route::post('sub-modules/{module}', [SubModuleController::class, 'store']);
        Route::get('sub-modules/{sub_module}/edit', [SubModuleController::class, 'edit']);
        Route::post('sub-modules-update/{sub_module}', [SubModuleController::class, 'update']);
        Route::delete('sub-modules/{sub_module}', [SubModuleController::class, 'destroy']);
        Route::put('module/{module}/arrange-submodules', [ModuleController::class, 'arrangeSubModules']);
        Route::post('upload-image-auth', [SubModuleController::class, 'uploadImage']);

        // Quizzes
        Route::get('quizzes/working/{quiz}', [QuizController::class, 'show']);
        Route::get('quizzes-result/{user_quiz}', [QuizController::class, 'result']);
        Route::post('quizzes', [QuizController::class, 'store']);
        Route::post('quizzes-submit/{user_quiz}', [QuizController::class, 'submit']);
        Route::get('user-module-quizzes', [QuizController::class, 'getByAuthModule']);
        Route::delete('quizzes/{quiz}', [QuizController::class, 'destroy']);
        Route::get('quizzes/{slug}', [QuizController::class, 'index']);

        // User Quiz
        Route::get('user-quizzes', [UserQuizController::class, 'index']);
        Route::get('user-course-quizzes/{courseSlug}', [UserQuizController::class, 'getByUserCourse']);

        // Module Questions
        Route::post('module-questions/{module}', [ModuleQuestionController::class, 'store']);
        Route::get('module-question/{module_question}/edit', [ModuleQuestionController::class, 'edit']);
        Route::put('module-question/{module_question}/update', [ModuleQuestionController::class, 'update']);
        Route::delete('module-questions/{module_question}', [ModuleQuestionController::class, 'destroy']);
        Route::get('module-questions/detail/{module}', [ModuleQuestionController::class, 'index']);

        // Course Tests & Pre/Post Test
        Route::get('course-tests-get', [CourseTestController::class, 'get']);
        Route::get('course-pre-test/{course_test}', [CourseTestController::class, 'preTest']);
        Route::get('course-post-test/{course_test}', [CourseTestController::class, 'postTest']);
        Route::get('user-course-test-id/{course_test}', [CourseTestController::class, 'getPostTestId']);
        Route::post('course-submit-test/{user_course_test}', [CourseTestController::class, 'submit']);
        Route::get('course-test-statistic/{user_course_test}', [CourseTestController::class, 'statistic']);
        Route::get('course-test-identifiers/{recordId}', [CourseTestController::class, 'getTestIds']);
        Route::get('course-tests/{slug}', [CourseTestController::class, 'index']);
        Route::get('course-test-start/{course_test}', [CourseTestController::class, 'show']);
        Route::post('course-tests/{slug}', [CourseTestController::class, 'store']);
        Route::resource('course-tests', CourseTestController::class)->only(['update', 'destroy']);
        Route::delete('/reset-test/{id}', [CourseTestController::class, 'resetTest']);

        // Course Test Questions
        Route::get('course-test-questions/{course_test}', [CourseTestQuestionController::class, 'index']);
        Route::post('course-test-questions/{course_test}', [CourseTestQuestionController::class, 'store']);
        Route::resource('course-test-questions', CourseTestQuestionController::class)->only(['show', 'update', 'destroy']);

        // User Course Tests
        Route::get('user-course-tests', [UserCourseTestController::class, 'index']);
        Route::get('user-course-tests/{slug}', [UserCourseTestController::class, 'getByCourse']);
        Route::delete('remove-score/{userCourseTestId}', [UserCourseTestController::class, 'removeScore']);

        // Module Tasks
        Route::get('module-tasks/course/{courseSlug}', [ModuleTaskController::class, 'getByCourse']);
        Route::get('course-tasks/{slug}', [ModuleTaskController::class, 'getByCourseWithPaginate']);
        Route::get('module-tasks/{module}', [ModuleTaskController::class, 'index']);
        Route::get('module-tasks-detail/{module_task}', [ModuleTaskController::class, 'show']);
        Route::get('module-task-answer/{moduleTask}', [ModuleTaskController::class, 'showWithAnswer']);
        Route::post('module-tasks/{module}', [ModuleTaskController::class, 'store']);
        Route::resource('module-tasks', ModuleTaskController::class)->only(['update', 'destroy']);

        // Submission Tasks
        Route::post('submission-tasks/{moduleTask}', [SubmissionTaskController::class, 'store']);
        Route::post('submission-tasks/link/{moduleTask}', [SubmissionTaskController::class, 'storeLink']);
        Route::get('submission-tasks/detail/{submissionTask}', [SubmissionTaskController::class, 'show']);
        Route::get('submission-tasks/{moduleTask}', [SubmissionTaskController::class, 'index']);

        // User Courses
        Route::get('user-courses', [UserCourseController::class, 'guest']);
        Route::get('get-courses-by-user/{user}', [UserCourseController::class, 'getByUser']);
        Route::put('user-courses/{slug}/{sub_module}', [UserCourseController::class, 'userLastStep']);
        Route::get('/user/stats', [UserCourseController::class, 'getUserStats']);
        Route::post('user-courses-check', [UserCourseController::class, 'checkPayment']);
        Route::delete('remove-pre-test/{slug}', [UserCourseController::class, 'removePreTest']);

        // Vouchers & Reviews
        Route::post('course-vouchers/{courseSlug}', [CourseVoucherController::class, 'store']);
        Route::delete('course-vouchers/{courseVoucher}', [CourseVoucherController::class, 'destroy']);
        Route::put('course-vouchers/{courseVoucher:code}', [CourseVoucherController::class, 'update']);
        Route::get('course-vouchers/{courseSlug}', [CourseVoucherController::class, 'index']);
        Route::get('course-vouchers/{courseSlug}/check', [CourseVoucherController::class, 'checkCode']);
        Route::post('course-voucher-users', [CourseVoucherUserController::class, 'store']);
        
        Route::post('course-reviews/{course}', [CourseReviewController::class, 'store']);
        Route::put('course-reviews/{course_review}', [CourseReviewController::class, 'update']);
        Route::delete('course-reviews/{course_review}', [CourseReviewController::class, 'destroy']);
    });
});
