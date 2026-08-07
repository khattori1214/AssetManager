<?php

use App\Http\Controllers\AssetApplicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\TopController;
use App\Http\Controllers\UsageHistoryController;
use App\Http\Controllers\AssetManagementController;


Route::get('/', function () {
    return view('welcome');
});
// ログイン画面
Route::get('/login', [LoginController::class, 'index']);

// ログイン処理
Route::post('/login', [LoginController::class, 'login']);

// ログアウト処理
Route::post('/logout', [LoginController::class, 'logout']);

// トップ画面表示
Route::get('/top', [TopController::class, 'index']);

// 資産一覧・申請画面表示
Route::get('/assets', [AssetApplicationController::class, 'index']);

// 資産一覧・申請画面での検索機能
Route::get('/assets/search', [AssetApplicationController::class, 'search']);

// 資産一覧・申請画面での消耗品減算処理
Route::post('/assets/acquire', [AssetApplicationController::class, 'acquire']);

// 資産一覧・申請画面での貸出資産貸出処理
Route::post('/assets/borrow', [AssetApplicationController::class, 'borrow']);

// 利用履歴・返却画面の表示
Route::get('/histories', [UsageHistoryController::class, 'index']);
Route::post('/histories/return', [UsageHistoryController::class, 'returnAsset']);



// 【管理者用】資産登録・在庫管理画面の表示

Route::get('/admin', [AssetManagementController::class, 'index']);

Route::post('/admin/assets', [AssetManagementController::class, 'store']);

Route::get('/admin/assets/{id}/edit', [AssetManagementController::class, 'edit']);
Route::put('/admin/assets/{id}', [AssetManagementController::class, 'update']);

Route::patch('/admin/assets/{id}/stock', [AssetManagementController::class, 'updateStock']);
Route::delete('/admin/assets/{id}', [AssetManagementController::class, 'destroy']);


// 経理連携用CSV出力バッチ処理
Route::get('/admin/csv/download', [AssetManagementController::class, 'download']);


