<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\ExpenseRepositoryContract;
use App\Repositories\Contracts\IdentifierRepositoryContract;
use App\Repositories\Contracts\LeaveRepositoryContract;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class ExpenseController extends Controller
{
    protected ExpenseRepositoryContract $_expenseRepository;

    public function __construct(ExpenseRepositoryContract $expenseRepository)
    {
        $this->_expenseRepository = $expenseRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = $this->_expenseRepository->allByUser(auth()->user()->id);

        return view("expenses.index", compact("expenses"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(IdentifierRepositoryContract $identifierRepository)
    {
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("expenses.create", compact(
            "identifiers"
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreExpenseRequest $request,
        LeaveRepositoryContract $leaveRepository
    ) {
        $expense = $this->_expenseRepository->create(auth()->user()->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount")),
            "effetive_at" => $request->input("effetive_at") ?? Carbon::now(),
        ]);

        if (!$request->input("effetive_at")) {
            $leaveRepository->create(auth()->id(), [
                "leaveable_type" => Expense::class,
                "leaveable_id" => $expense->id
            ]);
        }

        return redirect()->route("expenses.index")->with(
            Alert::success("Despesa criada com sucesso.")
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Expense $expense)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IdentifierRepositoryContract $identifierRepository, Expense $expense)
    {
        if (Gate::denies("expense-edit", $expense)) {
            abort(404);
        }
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("expenses.edit", compact("expense", "identifiers"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        if (Gate::denies("expense-edit", $expense)) {
            abort(404);
        }
        $this->_expenseRepository->update($expense->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("expenses.edit", $expense)->with(
            Alert::success("Despesa atualizada com sucesso.")
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LeaveRepositoryContract $leaveRepository, Expense $expense)
    {
        if (Gate::denies("expense-edit", $expense)) {
            abort(404);
        }

        $this->_expenseRepository->delete($expense->id);
        $leaveRepository->deletePolymorph(Expense::class, $expense->id);

        return redirect()->route("expenses.index")->with(
            Alert::success("Despesa removida com sucesso")
        );
    }
}
