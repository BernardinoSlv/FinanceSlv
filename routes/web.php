<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\IdentifierController;
use App\Http\Controllers\ExpenseController;
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
    // expenses
    Route::resource("despesas", ExpenseController::class)
        ->parameter("despesas", "expense")
        ->names([
            "index" => "expenses.index",
            "create" => "expenses.create",
            "store" => "expenses.store",
            "show" => "expenses.show",
            "edit" => "expenses.edit",
            "update" => "expenses.update",
            "destroy" => "expenses.destroy",
        ]);

    // identifiers
    Route::resource("identificadores", IdentifierController::class)
        ->parameter("identificadores", "identifier")
        ->names([
            "index" => "identifiers.index",
            "create" => "identifiers.create",
            "store" => "identifiers.store",
            "show" => "identifiers.show",
            "edit" => "identifiers.edit",
            "update" => "identifiers.update",
            "destroy" => "identifiers.destroy",
        ]);
});
