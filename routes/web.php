<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DebtorController;
use App\Http\Controllers\EntryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvestimentController;
use App\Http\Controllers\LeaveController;
use App\Http\Controllers\NeedController;
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
    Route::resource("entradas", EntryController::class)
        ->parameter("entradas", "entry")
        ->names([
            "index" => "entries.index",
            "create" => "entries.create",
            "store" => "entries.store",
            "show" => "entries.show",
            "edit" => "entries.edit",
            "update" => "entries.update",
            "destroy" => "entries.destroy",
        ]);

    // leaves
    Route::resource("saidas", LeaveController::class)
        ->parameter("saidas", "leave")
        ->names([
            "index" => "leaves.index",
            "create" => "leaves.create",
            "store" => "leaves.store",
            "show" => "leaves.show",
            "edit" => "leaves.edit",
            "update" => "leaves.update",
            "destroy" => "leaves.destroy",
        ]);

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

    // debtors
    Route::resource("devedores", DebtorController::class)
        ->parameter("devedores", "debtor")
        ->names([
            "index" => "debtors.index",
            "create" => "debtors.create",
            "store" => "debtors.store",
            "show" => "debtors.show",
            "edit" => "debtors.edit",
            "update" => "debtors.update",
            "destroy" => "debtors.destroy",
        ]);

    // debts
    Route::resource("dividas", DebtController::class)
        ->parameter("dividas", "debt")
        ->names([
            "index" => "debts.index",
            "create" => "debts.create",
            "store" => "debts.store",
            "show" => "debts.show",
            "edit" => "debts.edit",
            "update" => "debts.update",
            "destroy" => "debts.destroy",
        ]);

    // investiments
    Route::resource("investimentos", InvestimentController::class)
        ->parameter("investimentos", "investiment")
        ->names([
            "index" => "investiments.index",
            "create" => "investiments.create",
            "store" => "investiments.store",
            "show" => "investiments.show",
            "edit" => "investiments.edit",
            "update" => "investiments.update",
            "destroy" => "investiments.destroy",
        ]);

    Route::resource("necessidades", NeedController::class)
        ->parameter("necessidades", "need")
        ->names([
            "index" => "needs.index",
            "create" => "needs.create",
            "store" => "needs.store",
            "show" => "needs.show",
            "edit" => "needs.edit",
            "update" => "needs.update",
            "destroy" => "needs.destroy",
        ]);
});
