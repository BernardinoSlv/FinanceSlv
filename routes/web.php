<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DebtorController;
use App\Http\Controllers\IdentifierController;
use App\Http\Controllers\QuickEntryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvestimentController;
use App\Http\Controllers\QuickLeaveController;
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
    Route::resource("entradas-rapidas", QuickEntryController::class)
        ->parameter("entradas-rapidas", "quickEntry")
        ->names([
            "index" => "quick-entries.index",
            "create" => "quick-entries.create",
            "store" => "quick-entries.store",
            "show" => "quick-entries.show",
            "edit" => "quick-entries.edit",
            "update" => "quick-entries.update",
            "destroy" => "quick-entries.destroy",
        ]);

    // leaves
    Route::resource("saidas-rapidas", QuickLeaveController::class)
        ->parameter("saidas-rapidas", "quickLeave")
        ->names([
            "index" => "quick-leaves.index",
            "create" => "quick-leaves.create",
            "store" => "quick-leaves.store",
            "show" => "quick-leaves.show",
            "edit" => "quick-leaves.edit",
            "update" => "quick-leaves.update",
            "destroy" => "quick-leaves.destroy",
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
