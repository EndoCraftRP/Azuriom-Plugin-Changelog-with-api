<?php

use Azuriom\Plugin\Changelog\Controllers\Api\ApiController;
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
Route::get('updates', [ApiController::class, 'index']);
Route::post('updates', [ApiController::class, 'store'])
    ->middleware([\Azuriom\Plugin\Changelog\Middleware\AuthenticateChangelogApiToken::class, 'throttle:60,1']);
