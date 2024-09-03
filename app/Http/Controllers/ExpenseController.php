<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;
use Src\Parsers\RealToFloatParser;

class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $expenses = auth()->user()->expenses()
            ->with("identifier")
            ->orderBy("expenses.id", 'DESC')
            ->paginate();

        return view("expenses.index", compact("expenses"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $identifiers = auth()->user()->identifiers()->get();

        return view("expenses.create", compact("identifiers"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        $expenses = auth()->user()->expenses()->create([
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->amount)
        ]);

        return redirect()->route("expenses.index")
            ->with(Alert::success("Despesa criada com sucesso."));
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
    public function update(Request $request, Expense $expense)
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
