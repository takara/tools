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
    return view('markdown');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::get('/user', [App\Http\Controllers\admin\UserController::class, 'index'])->name('user');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/replace_keyword', [App\Http\Controllers\admin\ReplaceKeywordController::class, 'index']);
    Route::get('/replace_keyword/edit/{id}', [App\Http\Controllers\admin\ReplaceKeywordController::class, 'edit']);
});


