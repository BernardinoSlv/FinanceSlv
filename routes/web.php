<?php

use App\Console\Commands\CreateMovementToExpense;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DebtController;
use App\Http\Controllers\DebtPaymentController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\IdentifierController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectItemController;
use App\Http\Controllers\QuickController;
use App\Models\ProjectItem;
use Illuminate\Support\Facades\Artisan;
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
    ->prefix('autenticacao')
    ->name('auth.')
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/', 'indexStore')->name('index_store');
        Route::get('cadastrar', 'create')->name('create');
        Route::post('cadastrar', 'store')->name('store');
        Route::get('/sair', 'logout')->name('logout');
    });

// rotas protegidas
Route::middleware('checkAuth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])
        ->name('dashboard.index');

    // quicks
    Route::resource('simples', QuickController::class)
        ->parameter('simples', 'quick')
        ->names([
            'index' => 'quicks.index',
            'create' => 'quicks.create',
            'store' => 'quicks.store',
            'show' => 'quicks.show',
            'edit' => 'quicks.edit',
            'update' => 'quicks.update',
            'destroy' => 'quicks.destroy',
        ]);
    Route::resource('movimentacoes', MovementController::class)
        ->parameter('movimentacoes', 'movement')
        ->names([
            'index' => 'movements.index',
            'create' => 'movements.create',
            'store' => 'movements.store',
            'show' => 'movements.show',
            'edit' => 'movements.edit',
            'update' => 'movements.update',
            'destroy' => 'movements.destroy',
        ]);
    Route::resource('dividas', DebtController::class)
        ->parameter('dividas', 'debt')
        ->names([
            'index' => 'debts.index',
            'create' => 'debts.create',
            'store' => 'debts.store',
            'show' => 'debts.show',
            'edit' => 'debts.edit',
            'update' => 'debts.update',
            'destroy' => 'debts.destroy',
        ]);
    Route::resource('dividas.pagamentos', DebtPaymentController::class)
        ->parameter('dividas', 'debt')
        ->only(['index', 'store', 'update', 'destroy'])
        ->parameter('pagamentos', 'movement')
        ->names([
            'index' => 'debts.payments.index',
            'create' => 'debts.payments.create',
            'store' => 'debts.payments.store',
            'show' => 'debts.payments.show',
            'edit' => 'debts.payments.edit',
            'update' => 'debts.payments.update',
            'destroy' => 'debts.payments.destroy',
        ])->scoped();
    // // expenses
    // Route::resource("despesas", ExpenseController::class)
    //     ->parameter("despesas", "expense")
    //     ->names([
    //         "index" => "expenses.index",
    //         "create" => "expenses.create",
    //         "store" => "expenses.store",
    //         "show" => "expenses.show",
    //         "edit" => "expenses.edit",
    //         "update" => "expenses.update",
    //         "destroy" => "expenses.destroy",
    //     ]);

    // identifiers
    Route::resource('identificadores', IdentifierController::class)
        ->parameter('identificadores', 'identifier')
        ->names([
            'index' => 'identifiers.index',
            'create' => 'identifiers.create',
            'store' => 'identifiers.store',
            'show' => 'identifiers.show',
            'edit' => 'identifiers.edit',
            'update' => 'identifiers.update',
            'destroy' => 'identifiers.destroy',
        ]);

    // expenses
    Route::resource('despesas', ExpenseController::class)
        ->parameter('despesas', 'expense')
        ->names([
            'index' => 'expenses.index',
            'create' => 'expenses.create',
            'store' => 'expenses.store',
            'show' => 'expenses.show',
            'edit' => 'expenses.edit',
            'update' => 'expenses.update',
            'destroy' => 'expenses.destroy',
        ]);

    // projects
    Route::resource('projetos', ProjectController::class)
        ->parameter('projetos', 'project')
        ->names([
            'index' => 'projects.index',
            'create' => 'projects.create',
            'store' => 'projects.store',
            'show' => 'projects.show',
            'edit' => 'projects.edit',
            'update' => 'projects.update',
            'destroy' => 'projects.destroy',
        ]);
    // projects
    Route::resource('projetos.itens', ProjectItemController::class)
        ->parameter('projetos', 'project')
        ->parameter('itens', 'projectItem')
        ->names([
            'index' => 'projects.items.index',
            'create' => 'projects.items.create',
            'store' => 'projects.items.store',
            'show' => 'projects.items.show',
            'edit' => 'projects.items.edit',
            'update' => 'projects.items.update',
            'destroy' => 'projects.items.destroy',
        ])
        ->scoped();
});


Route::get("cron", function () {
    Artisan::call("app:cmte");
});
