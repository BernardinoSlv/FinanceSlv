<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DebtorController;
use App\Http\Controllers\DebtorPaymentController;
use App\Http\Controllers\DebtPaymentController;
use App\Http\Controllers\IdentifierController;
use App\Http\Controllers\QuickEntryController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\InvestimentController;
use App\Http\Controllers\InvestimentEntryController;
use App\Http\Controllers\InvestimentLeaveController;
use App\Http\Controllers\QuickLeaveController;
use App\Http\Controllers\NeedController;
use App\Models\Debt;
use App\Models\Debtor;
use App\Models\Expense;
use App\Models\Identifier;
use App\Models\Investiment;
use App\Models\Need;
use App\Models\QuickEntry;
use App\Models\QuickLeave;
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
    Route::resource("devedores.pagamentos", DebtorPaymentController::class)
        ->parameters([
            "devedores" => "debtor",
            "pagamentos" => "entry"
        ])
        ->names([
            "index" => "debtors.payments.index",
            "create" => "debtors.payments.create",
            "store" => "debtors.payments.store",
            "show" => "debtors.payments.show",
            "edit" => "debtors.payments.edit",
            "update" => "debtors.payments.update",
            "destroy" => "debtors.payments.destroy",
        ])
        ->scoped();


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
    Route::resource("dividas.pagamentos", DebtPaymentController::class)
        ->parameters([
            "dividas" => "debt",
            "pagamentos" => "leave"
        ])
        ->names([
            "index" => "debts.payments.index",
            "create" => "debts.payments.create",
            "store" => "debts.payments.store",
            "show" => "debts.payments.show",
            "edit" => "debts.payments.edit",
            "update" => "debts.payments.update",
            "destroy" => "debts.payments.destroy",
        ])->scoped();

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
    Route::resource("investimentos.retiradas", InvestimentEntryController::class)
        ->parameters([
            "investimentos" => "investiment",
            "retiradas" => "entry"
        ])
        ->names([
            "index" => "investiments.entries.index",
            "create" => "investiments.entries.create",
            "store" => "investiments.entries.store",
            "show" => "investiments.entries.show",
            "edit" => "investiments.entries.edit",
            "update" => "investiments.entries.update",
            "destroy" => "investiments.entries.destroy",
        ])->scoped();
    Route::resource("investimentos.depositos", InvestimentLeaveController::class)
        ->parameters([
            "investimentos" => "investiment",
            "depositos" => "leave"
        ])
        ->names([
            "index" => "investiments.leaves.index",
            "create" => "investiments.leaves.create",
            "store" => "investiments.leaves.store",
            "show" => "investiments.leaves.show",
            "edit" => "investiments.leaves.edit",
            "update" => "investiments.leaves.update",
            "destroy" => "investiments.leaves.destroy",
        ])->scoped();

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
