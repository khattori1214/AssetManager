<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TopController;

Route::get('/', function () {
    return view('welcome');
});
// ログイン画面
Route::get('/login', [LoginController::class,'index']);

// ログイン処理
Route::post('/login',[LoginController::class,'login']);

// ログアウト処理
Route::post('/logout', [LoginController::class, 'logout']);

// トップ画面表示
Route::get('/top',[TopController::class,'index']);
