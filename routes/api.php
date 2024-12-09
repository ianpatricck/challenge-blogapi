<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\LikeController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

// AuthController API routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/user', [AuthController::class, 'findLoggedUser'])
    ->middleware('auth:sanctum');
Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

// PostController API routes
Route::get('/posts', [PostController::class, 'findAll']);
Route::get('/posts/{postId}', [PostController::class, 'findOne']);
Route::post('/posts/create', [PostController::class, 'create'])
    ->middleware('auth:sanctum');
Route::put('/posts/update/{postId}', [PostController::class, 'update'])
    ->middleware('auth:sanctum');
Route::delete('/posts/delete/{postId}', [PostController::class, 'deleteOne'])
    ->middleware('auth:sanctum');

// CommentController API routes    
Route::post('/comments/create/{postId}', [CommentController::class, 'create'])
    ->middleware('auth:sanctum');
Route::delete('/comments/delete/{commentId}', [CommentController::class, 'delete'])
    ->middleware('auth:sanctum');

// LikeController API routes  
Route::post('/likes/create', [LikeController::class, 'create'])
    ->middleware('auth:sanctum');
