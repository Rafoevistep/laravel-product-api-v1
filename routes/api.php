<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\NewPasswordController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ReviewController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    // Route::get('/user-profile', [AuthController::class, 'userProfile']);
});

Route::post('forgot-password', [NewPasswordController::class, 'forgotPassword']);
Route::post('reset-password', [NewPasswordController::class, 'reset']);

Route::controller(ProductController::class)->group(function() {
    Route::get('/products', 'index');
    Route::get('/products/{product}', 'show');
    Route::post('/products','store');
    Route::put('/products/{product}', 'update');
    Route::delete('/products/{product}', 'destroy');
});

Route::controller(ReviewController::class)->group(function() {
    Route::post('/review/{product}', 'store');
    Route::put('/review/{review}', 'update');
    Route::delete('/review/{review}', 'destroy');
});
