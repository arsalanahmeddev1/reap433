<?php

use App\Http\Controllers\Api\AnswerController;
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
Route::post('/forgot-password', [UserController::class, 'forgotPassword']);

Route::middleware('api.auth')->group(function () {
    // User
    Route::post('/change-password', [UserController::class, 'changePassword']);
    Route::put('/update-profile', [UserController::class, 'updateProfile']);
    Route::get('/get-profile', [UserController::class, 'getProfile']);
    Route::post('/logout', [UserController::class, 'logout']);

    // Quiz Categories
    Route::get('/categories', [QuizeCategoryController::class, 'index']);
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
});
