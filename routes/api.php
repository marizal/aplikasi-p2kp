<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

// Route::get('index', [App\Http\Controllers\AuthController::class, 'index']);

Route::get('/greeting', function () {
    return 'Hello World';
});
Route::post('register', [App\Http\Controllers\AuthController::class, 'register']);
Route::post('login', [App\Http\Controllers\AuthController::class, 'login']);


//admin
Route::prefix('user',)->group(function(){

    Route::middleware(['auth:sanctum', 'user'])->group(function(){
        //user
        Route::get('info', [App\Http\Controllers\AuthController::class, 'user']);
        Route::post('logout', [App\Http\Controllers\AuthController::class, 'logout']);

        //transaction
        Route::get('transaction',[App\Http\Controllers\TransactionController::class, 'index']);
        Route::post('transaction/store', [App\Http\Controllers\TransactionController::class, 'store']);
        Route::get('spending', [App\Http\Controllers\TransactionController::class, 'spending']);
        Route::get('income', [App\Http\Controllers\TransactionController::class, 'income']);
    
    });
});
