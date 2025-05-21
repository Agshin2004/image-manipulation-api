<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::prefix('auth')->middleware('throttle:api')->group(function () {
    Route::post('register', [\App\Http\Controllers\AuthController::class, 'register']);
    Route::post('login', [\App\Http\Controllers\AuthController::class, 'login']);
    Route::post('logout', [\App\Http\Controllers\AuthController::class, 'logout'])->middleware('auth:sanctum');
});

// protecting all routes with rate limiter and auth:sanctum meaning we will have to pass api access token in each request AND
// auth:sanctum is what tells laravel to authenticate the incoming request using the token
// Parse the token from the Authorization header,
// Validate it,
// Load the user, and
// Bind the correct currentAccessToken() to it
Route::prefix('v1')->middleware(['throttle:api', 'auth:sanctum'])->group(function () {
    // name of the url (api/album) and resourceful routes
    Route::apiResource('album', \App\Http\Controllers\V1\AlbumController::class);

    // * Image related routes
    Route::get('image', [\App\Http\Controllers\V1\ImageController::class, 'index']);
    Route::get('image/{image}', [\App\Http\Controllers\V1\ImageController::class, 'show']);
    Route::get('image/by-album/{album}', [\App\Http\Controllers\V1\ImageController::class, 'byAlbum']);
    Route::post('image/resize', [\App\Http\Controllers\V1\ImageController::class, 'resize']);
    Route::delete('image/{image}', [\App\Http\Controllers\V1\ImageController::class, 'destroy']);
});
