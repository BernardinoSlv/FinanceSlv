<?php

namespace App\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Support\Message;
use App\Http\Requests\StoreDebtRequest;
use App\Http\Requests\UpdateDebtRequest;
use App\Models\Debt;
use App\Pipes\Debt\FilterByTextPipe;
use App\Pipes\Debt\OrderByPipe;
use App\Pipes\Debt\StatusPipe;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Pipeline $pipeline)
    {
        $debts = $pipeline->send(auth()->user()->debts())
            ->through([
                FilterByTextPipe::class,
                OrderByPipe::class,
                StatusPipe::class,
            ])
            ->thenReturn()
            ->select('debts.*')
            ->with('identifier')
            ->withSum(
                ['movements as movements_paid_sum_amount' => fn($query) => $query->whereNotNull("closed_date")->where("type", MovementTypeEnum::OUT->value)],
                "amount"
            )
            ->paginate()
            ->withQueryString();

        return view('debts.index', compact('debts'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $identifiers = auth()->user()->identifiers()->orderBy('name')->orderBy('id')->get();

        return view('debts.create', compact('identifiers'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDebtRequest $request)
    {

        $debt = new Debt([
            ...$request->validated(),
            'amount' => RealToFloatParser::parse($request->input('amount')),
            'installments' => intval($request->installments) > 0 ? $request->installments : 1,
        ]);
        $debt->user_id = auth()->id();
        $debt->save();

        // colocar o saldo na conta
        if ($request->input('to_balance')) {
            $debt->movements()->create([
                'identifier_id' => $debt->identifier_id,
                'user_id' => $debt->user_id,
                'type' => MovementTypeEnum::IN->value,
                'amount' => $debt->amount,
            ]);
        }

        // verifica se o primeiro vencimento ocorre nesse mês
        if (
            $request->date("due_date") &&
            ($dueDate = $request->date("due_date"))->month === now()->month
        ) {
            $debt->movements()->create([
                "type" => MovementTypeEnum::OUT->value,
                "closed_date" => null,
                "effetive_date" => $debt->due_date->format("Y-m-d"),
                "amount" => $debt->amount / $debt->installments,
                "user_id" => auth()->id(),
                "identifier_id" => $debt->identifier_id
            ]);
        }

        return redirect()->route('debts.index')->with(
            Message::success('Dívida criada com sucesso.')
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debt $debt)
    {
        if (Gate::denies('is-owner', $debt)) {
            abort(403);
        }

        $identifiers = auth()->user()->identifiers()
            ->orderBy('name')
            ->orderBy('id')
            ->get();

        return view('debts.edit', compact('debt', 'identifiers'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDebtRequest $request, Debt $debt)
    {
        if (Gate::denies('is-owner', $debt)) {
            abort(403);
        }

        DB::beginTransaction();
        $debt->fill([
            ...$request->validated(),
            'amount' => RealToFloatParser::parse($request->input('amount')),
        ]);
        if ($debt->isDirty("to_balance")) {
            if ($debt->to_balance)
                $debt->movements()->create([
                    'identifier_id' => $debt->identifier_id,
                    'user_id' => $debt->user_id,
                    'type' => MovementTypeEnum::IN->value,
                    'amount' => $debt->amount,
                ]);
            else
                $debt->movements()->where("type", "in")->delete();
        }

        // atualiza o valor da entrada
        if ($debt->to_balance) {
            $debt->movements()->where("type", "in")->update([
                "amount" => $debt->amount
            ]);
        }
        $debt->save();
        $debt->movements()->update([
            'identifier_id' => $request->input('identifier_id'),
        ]);
        DB::commit();

        return redirect()->route('debts.edit', $debt)
            ->with(Message::success('Dívida atualizada com sucesso.'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt)
    {
        if (Gate::denies('is-owner', $debt)) {
            abort(403);
        }

        $debt->delete();
        $debt->movements()->delete();

        return redirect()->route('debts.index')
            ->with(Message::success('Dívida deletada com sucesso.'));
    }
}
