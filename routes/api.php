<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BookController;
use App\Http\Controllers\Api\BorrowingController;
use Illuminate\Support\Facades\Route;

// Public routes

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware(['auth:sanctum','log.request'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', function () {
        return auth()->user();
    });
    // Books accessible to all authenticated users
    Route::get('/books',       [BookController::class, 'index']);
    Route::get('/books/{book}', [BookController::class, 'show']);

    // Admin-only book management
    Route::middleware('role:admin')->group(function () {
        Route::post('/books',       [BookController::class, 'store']);
        Route::put('/books/{book}', [BookController::class, 'update']);
        Route::delete('/books/{book}', [BookController::class, 'destroy']);
    });

    Route::get('/borrowings', [BorrowingController::class, 'index']);
    Route::post('/borrow', [BorrowingController::class, 'borrow']);
    Route::post('/return/{book_id}', [BorrowingController::class, 'returnBook']);
});
