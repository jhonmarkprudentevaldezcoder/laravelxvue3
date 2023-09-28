<?php

use App\Http\Controllers\TaskController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/tasks',[TaskController::class, 'index']);

Route::prefix('/tasks')->group(function() {
    Route::get('/completed',[TaskController::class, 'completed']);
    Route::post('/store',[TaskController::class, 'store']);
    Route::put('/{id}',[TaskController::class, 'update']);
    Route::get('search/',[TaskController::class, 'search']);
    Route::put('/completed/{id}',[TaskController::class, 'updatecompleted']);
    Route::put('/incompleted/{id}',[TaskController::class, 'incompleted']);
    Route::delete('/{id}',[TaskController::class, 'destroy']); // search all data
    Route::get('/task/{id}',[TaskController::class, 'show']);
});