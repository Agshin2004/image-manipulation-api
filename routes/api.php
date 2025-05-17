<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::prefix('v1')->group(function () {
    // name of the url (api/album) and resourceful routes
    Route::apiResource('album', \App\Http\Controllers\V1\AlbumController::class);
});
