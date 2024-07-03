<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Controllers\Api\DosenController;
use App\Http\Middleware\AdminRole;

//ApiController
Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);
Route::group([
    "middleware" => ["auth:sanctum"]
],function(){
    Route::get('/profile', [ApiController::class, 'profile']);
    Route::get('/logout', [ApiController::class, 'logout']);
    Route::delete('/delete', [ApiController::class, 'delete']);
    Route::patch('/update', [ApiController::class, 'update']);
    Route::get('/index', [ApiController::class, 'index'])->middleware(AdminRole::class);
});

//DosenController
Route::post('/dosen/register', [DosenController::class, 'register'])->middleware(AdminRole::class);
Route::post('/dosen/login', [DosenController::class, 'login']);
Route::group([
    "middleware" => ["auth:sanctum"]
], function(){
    Route::get('/dosen/indexMhs', [DosenController::class, 'indexMhs']);
    Route::get('/dosen/indexDosen', [DosenController::class, 'indexDosen']);
    Route::get('/dosen/logout', [DosenController::class, 'logout']);
    Route::delete('/dosen/delete/{id}', [DosenController::class, 'delete'])->middleware(AdminRole::class);
});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');