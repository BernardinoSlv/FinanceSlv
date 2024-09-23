<?php

namespace App\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Helpers\Alert;
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
            ->withSum('movements', 'amount')
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
        ]);
        $debt->user_id = auth()->id();
        $debt->save();

        if ($request->input('to_balance')) {
            $debt->movements()->create([
                'identifier_id' => $debt->identifier_id,
                'user_id' => $debt->user_id,
                'type' => MovementTypeEnum::IN->value,
                'amount' => $debt->amount,
            ]);
        }

        return redirect()->route('debts.index')->with(
            Alert::success('Dívida criada com sucesso.')
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt)
    {
    }

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
        ])->save();
        $debt->movements()->update([
            'identifier_id' => $request->input('identifier_id'),
        ]);
        DB::commit();

        return redirect()->route('debts.edit', $debt)
            ->with(Alert::success('Dívida atualizada com sucesso.'));
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
            ->with(Alert::success('Dívida deletada com sucesso.'));
    }
}
