<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiController;
use App\Http\Middleware\AdminRole;

//Register

Route::post('/register', [ApiController::class, 'register']);
Route::post('/login', [ApiController::class, 'login']);

Route::group([
    "middleware" => ["auth:sanctum"]
],function(){
    //profile
    Route::get('/profile', [ApiController::class, 'profile']);

    //logout
    Route::get('/logout', [ApiController::class, 'logout']);

    //busek
    Route::delete('/delete', [ApiController::class, 'delete']);

    Route::patch('/update', [ApiController::class, 'update']);

    Route::get('/index', [ApiController::class, 'index'])->middleware(AdminRole::class);

});

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');