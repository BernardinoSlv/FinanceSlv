<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\LeaveController;
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
    ->prefix("autenticacao")
    ->name("auth.")
    ->group(function () {
        Route::get("/", "index")->name("index");
        Route::post("/", "indexStore")->name("index_store");
        Route::get("cadastrar", "create")->name("create");
        Route::post("cadastrar", "store")->name("store");
        Route::get("/sair", "logout")->name("logout");
    });

// rotas protegidas
Route::middleware("checkAuth")->group(function () {
    // dashboard
    Route::controller(DashboardController::class)
        ->as("dashboard.")
        ->group(function () {
            Route::get("/", "index")->name("index");
        });

    // entries
    Route::resource("entries", EntryController::class);

    // leaves
    Route::resource("leaves", LeaveController::class);
});
