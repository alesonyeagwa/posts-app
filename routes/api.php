<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use CloudCreativity\LaravelJsonApi\Facades\JsonApi;
use CloudCreativity\LaravelJsonApi\Routing\RouteRegistrar as Api;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
//Route::post('/login', [AuthController::class, 'login']);

JsonApi::register('default')->withNamespace('App\Http\Controllers')->routes(function (Api $api) {

    $api->post('/login', [LoginController::class, 'login'])->middleware('throttle:10,1');
    $api->delete('/logout', [LoginController::class, 'logout'])->middleware(['throttle:10,1', 'auth:sanctum']);
    $api->post('/register', [RegisterController::class, 'register'])->middleware('throttle:10,1');

    $api->resource('users')->relationships(function ($relations) {
        $relations->hasMany('posts')->only('related', 'read');
        $relations->hasMany('comments')->only('related', 'read');
    })->except('index', 'create', 'delete')->controller();

    $api->resource('posts')->relationships(function ($relations) {
        $relations->hasOne('author')->only('read');
        $relations->hasMany('comments')->only('related', 'read');
    })->controller();
    
    $api->resource('comments')->relationships(function ($relations) {
        $relations->hasOne('commenter')->only('read');
        $relations->hasOne('post')->only('read');
    })->only('create', 'update', 'delete')->authorizer('app');
});