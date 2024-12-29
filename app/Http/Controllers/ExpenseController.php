<?php

namespace App\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Support\Message;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Requests\UpdateExpenseRequest;
use App\Models\Expense;
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
            ->with('identifier')
            ->orderBy('expenses.id', 'DESC')
            ->paginate();

        return view('expenses.index', compact('expenses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $identifiers = auth()->user()->identifiers()->orderBy('name')->get();

        return view('expenses.create', compact('identifiers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExpenseRequest $request)
    {
        $expense = auth()->user()->expenses()->create([
            ...$request->validated(),
            'amount' => $request->is_variable ? 0 : RealToFloatParser::parse($request->amount),
        ]);

        if (now()->day < $expense->due_day) {
            $expense->movements()->create([
                'user_id' => auth()->id(),
                'identifier_id' => $expense->identifier_id,
                'type' => MovementTypeEnum::OUT->value,
                'effetive_date' => now()->day(
                    $expense->due_day > now()->daysInMonth
                        ? now()->daysInMonth
                        : $expense->due_day
                ),
                'closed_date' => null,
                'amount' => $expense->amount,
            ]);
        }

        return redirect()->route('expenses.index')
            ->with(Message::success('Despesa criada com sucesso.'));
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
        if (Gate::denies('is-owner', $expense)) {
            abort(403);
        }
        $identifiers = auth()->user()->identifiers()->orderBy('name')->get();

        return view('expenses.edit', compact('identifiers', 'expense'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateExpenseRequest $request, Expense $expense)
    {
        if (Gate::denies('is-owner', $expense)) {
            abort(403);
        }
        $expense->fill([
            ...$request->validated(),
            'amount' => RealToFloatParser::parse($request->amount),
        ]);
        if ($expense->isDirty()) {
            // quando alterar o identifier_id mudar das movimentações da despesa
            if ($expense->isDirty('identifier_id')) {
                $expense->movements()->update([
                    'identifier_id' => $expense->identifier_id,
                ]);
            }

            if (($previousDueDay = $expense->getOriginal('due_day')) !== $expense->due_day) {
                // caso o novo dia de vencimento ainda não tenha passado
                if (
                    now()->day($previousDueDay)->diffInDays(now()->day($expense->due_day)) >= 0
                    || now()->day($expense->due_day)->diffInDays(now()) >= 0
                ) {
                    if (
                        ! $expense->movements()
                            ->withTrashed()
                            ->whereYear('effetive_date', now()->year)
                            ->whereMonth('effetive_date', now()->month)->count()
                    ) {
                        $expense->movements()->create([
                            'identifier_id' => $expense->identifier_id,
                            'user_id' => auth()->id(),
                            'type' => MovementTypeEnum::OUT->value,
                            'amount' => $expense->amount,
                            'fees_amount' => 0,
                            'effetive_date' => now()->day(
                                $expense->due_day > now()->daysInMonth
                                    ? now()->daysInMonth
                                    : $expense->due_day
                            ),
                            'closed_date' => null,
                        ]);
                    }
                }
            }
            $expense->save();
        }

        return redirect()->route('expenses.edit', $expense)
            ->with(Message::success('Despesa atualizada com sucesso.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Expense $expense)
    {
        if (Gate::denies('is-owner', $expense)) {
            abort(403);
        }
        $expense->delete();

        return redirect()->route('expenses.index')
            ->with(Message::success('Despesa removida com sucesso.'));
    }
}
