<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Auth routes
    Route::get('/user', [AuthController::class, 'me']);
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    
    // Users (Admin/Manager only)
    Route::middleware('role:Admin,Manager')->group(function () {
        Route::get('/users', [UserController::class, 'index']);
        Route::get('/users/{user}', [UserController::class, 'show']);
        Route::put('/users/{user}/role', [UserController::class, 'updateRole']);
    });
    
    // Projects
    Route::apiResource('projects', ProjectController::class);
    Route::post('/projects/{project}/members', [ProjectController::class, 'addMember']);
    Route::delete('/projects/{project}/members', [ProjectController::class, 'removeMember']);
    
    // Tasks with rate limiting
    Route::middleware('throttle:60,1')->group(function () {
        Route::apiResource('tasks', TaskController::class);
        Route::post('/tasks/{task}/assign', [TaskController::class, 'assign']);
        Route::delete('/tasks/{task}/assign', [TaskController::class, 'unassign']);
        Route::get('/search/tasks', [TaskController::class, 'search']);
    });
    
    // Comments
    Route::post('/tasks/{task}/comments', [CommentController::class, 'store']);
    Route::get('/tasks/{task}/comments', [CommentController::class, 'index']);
    Route::put('/comments/{comment}', [CommentController::class, 'update']);
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
});
