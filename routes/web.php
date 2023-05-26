<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\MastodonController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/post/comment/form', [BlogController::class, 'commentForm']);
Route::get('/post/comment/{sourceIds?}', [BlogController::class, 'comments']);
Route::get('/post/{id}/{lang?}', [BlogController::class, 'post']);

Route::get('/mastodon/app/{instance}', [MastodonController::class, 'getAppCredentials']);
Route::get('/mastodon/token/{instance}/{code}', [MastodonController::class, 'getToken']);
Route::get('/mastodon/post/{instance}', [MastodonController::class, 'getPostInfoOnInstance']);
Route::post('/mastodon/toot/{instance}', [MastodonController::class, 'sendToot']);
