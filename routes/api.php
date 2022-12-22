<?php

use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CateoryController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ProfileController;
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

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware(['auth:api'])->group(function () {
    Route::get('profile', [ProfileController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::get('categories', [CateoryController::class, 'index']);
    Route::post('post', [PostController::class, 'create']);
    Route::get('post', [PostController::class, 'index']);
    Route::get('post/{id}', [PostController::class, 'show']);

    Route::get('profile-posts', [ProfileController::class, 'posts']);
});
