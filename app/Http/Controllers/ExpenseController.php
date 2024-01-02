<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use App\Repositories\Contracts\ExpenseRepositoryContract;

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

        return view("expense.index", compact("expenses"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("expense.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        //
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
    public function edit(Expense $expense)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        //
    }
}
