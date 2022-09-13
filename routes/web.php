<?php

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
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/fetch-user', 'ChatController@fetch_user')->name('fetch-user');
Route::get('/update-last-activity', 'ChatController@update_last_activity')->name('update-last-activity');
Route::post('/fetch-user-chat-history', 'ChatController@fetch_user_chat_histories')->name('fetch-user-chat-history');
Route::post('/insert-chat', 'ChatController@insert_chat')->name('insert-chat');
Route::post('/upload', 'ChatController@upload')->name('upload');
Route::post('/update-is-type-status', 'ChatController@update_is_type_status')->name('update-is-type-status');
