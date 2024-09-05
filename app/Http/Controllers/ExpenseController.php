<?php

namespace App\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Helpers\Alert;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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
        $identifiers = auth()->user()->identifiers()->orderBy("name")->get();

        return view("expenses.create", compact("identifiers"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        $expense = auth()->user()->expenses()->create([
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->amount)
        ]);

        if (now()->day < $expense->due_day)
            $expense->movements()->create([
                "type" => MovementTypeEnum::OUT->value,
                "user_id" => auth()->id(),
                "effetive_at" => now()->day(
                    $expense->due_day > now()->daysInMonth
                        ? now()->daysInMonth
                        : $expense->due_day
                ),
                "closed_at" => null,
                "amount" => $expense->amount,
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
        if (Gate::denies("is-owner", $expense))
            abort(403);
        $identifiers = auth()->user()->identifiers()->orderBy("name")->get();

        return view("expenses.edit", compact("identifiers", "expense"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        if (Gate::denies("is-owner", $expense))
            abort(403);
        $expense->fill([
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->amount)
        ]);
        if ($expense->isDirty()) {
            if (($previousDueDay = $expense->getOriginal("due_day")) !== $expense->due_day) {
                // where define if will create a new movements .
            }
            $expense->save();
        }
        return redirect()->route("expenses.edit", $expense)
            ->with(Alert::success("Despesa atualizada com sucesso."));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        //
    }
}
