<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::controller(AuthController::class)
    ->prefix("auth")
    ->name("auth.")
    ->group(function () {
        Route::get("/", "index")->name("index");
        Route::post("/", "indexStore")->name("index_store");
        Route::get("signup", "create")->name("create");
        Route::post("signup", "store")->name("store");
    });

// rotas protegidas
Route::middleware("checkAuth")->group(function () {
    Route::controller(DashboardController::class)
        ->as("dashboard.")
        ->group(function () {
            Route::get("/", "index")->name("index");
        });
});
