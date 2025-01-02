<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReplyController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/books', [BookController::class, 'index']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books', [BookController::class, 'store']);
    Route::get('/books/{id}', [BookController::class, 'show']);
    Route::put('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);

    Route::get('/books/{bookId}/replies', [ReplyController::class, 'index']);
    Route::post('replies', [ReplyController::class, 'store']);
    Route::get('/replies/{id}', [ReplyController::class, 'show']);
    Route::put('/replies/{id}', [ReplyController::class, 'update']);
    Route::delete('/replies/{id}', [ReplyController::class, 'destroy']);


    Route::post('/logout', [AuthController::class, 'logout']);
});
