<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('v1')->group(function () {
    // name of the url (api/album) and resourceful routes
    Route::apiResource('album', \App\Http\Controllers\V1\AlbumController::class);

    // Image related routes 
    Route::get('image', [\App\Http\Controllers\V1\ImageController::class, 'index']);
    Route::get('image/{image}', [\App\Http\Controllers\V1\ImageController::class, 'show']);
    Route::get('image/by-album/{album}', [\App\Http\Controllers\V1\ImageController::class, 'byAlbum']);
    Route::post('image/resize', [\App\Http\Controllers\V1\ImageController::class, 'resize']);
    Route::delete('image/{image}', [\App\Http\Controllers\V1\ImageController::class, 'destroy']);
});
