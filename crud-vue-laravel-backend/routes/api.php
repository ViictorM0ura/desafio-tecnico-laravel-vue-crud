<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ProfileController; // Importar o ProfileController
use App\Http\Controllers\Api\UserController;    // Importar o UserController
use App\Http\Controllers\Api\AddressController;  // Importar o AddressController

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

// Rotas de recursos para Profiles
Route::apiResource('profiles', ProfileController::class);

// Rotas de recursos para Users
Route::apiResource('users', UserController::class);

// Rotas de recursos para Addresses
Route::apiResource('addresses', AddressController::class);