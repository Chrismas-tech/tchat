<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('auth.login');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/conversation/{userId}', [App\Http\Controllers\MessageController::class, 'conversation'])
->name('message.conversation');

Route::post('send-message', [App\Http\Controllers\MessageController::class, 'sendMessage'])
->name('message.send-message');

Route::post('message-groups', [App\Http\Controllers\MessageGroupController::class, 'store'])
->name('message-groups.store');

Route::get('message-groups/{groupId}', [App\Http\Controllers\MessageGroupController::class, 'show'])->name('message-groups.show');

Route::post('send-group-message', [App\Http\Controllers\MessageController::class, 'sendGroupMessage'])
->name('message.send-group-message');

Route::post('send-image', [App\Http\Controllers\MessageController::class, 'sendImage'])
->name('message.send-image');




