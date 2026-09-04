<?php

use App\Http\Controllers\Api\AchievementController;
use App\Http\Controllers\Api\AnswerController;
use App\Http\Controllers\Api\ProgressController;
use App\Http\Controllers\Api\QuizeCategoryController;
use App\Http\Controllers\Api\QuizController;
use App\Http\Controllers\Api\QuizTypeController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| These routes are loaded with the `/api` prefix and the `api` middleware
| group. Controllers live in App\Http\Controllers\Api.
| JSON transformers live in App\Http\Resources.
| Form requests live in App\Http\Requests\Api.
|
| Middleware aliases:
|   api.auth  → authentication (401 JSON)
|   api.role  → authorization by role, e.g. api.role:admin or api.role:admin|user
|
*/

Route::post('/sign-up', [UserController::class, 'signUp']);
Route::post('/sign-in', [UserController::class, 'signIn']);
Route::post('/social-login', [UserController::class, 'socialLogin']);
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);
Route::post('/forgot-password/reset', [UserController::class, 'resetOtpPassword']);

Route::middleware('api.auth')->group(function () {
    // User
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::put('/update-profile', [UserController::class, 'updateProfile']);
    Route::get('/get-profile', [UserController::class, 'getProfile']);
    Route::get('/user-ranking', [UserController::class, 'userRanking']);
    Route::post('/logout', [UserController::class, 'logout']);

    // Quiz Categories
    Route::get('/categories', [QuizeCategoryController::class, 'index']);
    Route::get('/categories/continue', [QuizeCategoryController::class, 'continueQuizCategory']);
    Route::get('/categories/{slug}', [QuizeCategoryController::class, 'show']);

    // Quiz Types
    Route::get('/quiz-types', [QuizTypeController::class, 'index']);
    Route::get('/quiz-types/{slug}', [QuizTypeController::class, 'show']);

    // Quizzes
    Route::get('/quiz/start', [QuizController::class, 'index']);
    Route::get('/quiz/{slug}', [QuizController::class, 'show']);

    // Answer
    Route::post('/quiz/answer', [AnswerController::class, 'verify']);
    Route::post('/quiz/complete', [AnswerController::class, 'completeQuiz']);

    // Progress
    Route::get('/progress', [ProgressController::class, 'index']);

    // Achievements
    Route::get('/achievements', [AchievementController::class, 'index']);
    Route::post('/achievements/claim', [AchievementController::class, 'claim']);
    Route::get('/achievements/{slug}', [AchievementController::class, 'show']);
});
