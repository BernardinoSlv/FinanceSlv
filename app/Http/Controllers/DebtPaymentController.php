<?php

namespace App\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Helpers\Alert;
use App\Http\Requests\StoreDebtPaymentRequest;
use App\Http\Requests\UpdateDebtPaymentRequest;
use App\Models\Debt;
use App\Models\Movement;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class DebtPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt)) {
            abort(403);
        }

        $movements = $debt->movements()
            ->where("type", MovementTypeEnum::OUT->value)
            ->orderBy('id', "DESC")
            ->paginate();

        return view("debts.payments.index", compact("debt", "movements"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt)) {
            abort(403);
        }

        return view("debts.payments.create", compact("debt"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDebtPaymentRequest $request, Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt)) {
            abort(403);
        }

        $debt->movements()->create([
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount")),
            "user_id" => auth()->id(),
            "type" => MovementTypeEnum::OUT->value
        ]);

        return redirect()->route("debts.payments.create", $debt)
            ->with(Alert::success("Pagamento adicionado com sucesso."));
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debt $debt, Movement $movement)
    {
        if (Gate::denies("debt-edit", $debt) || Gate::denies("movement-edit", $movement)) {
            abort(403);
        }

        return view("debts.payments.edit", compact("debt", "movement"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDebtPaymentRequest $request, Debt $debt, Movement $movement)
    {
        if (Gate::denies("debt-edit", $debt) || Gate::denies("movement-edit", $movement)) {
            abort(403);
        }

        $movement->fill($request->validated());
        $movement->amount = RealToFloatParser::parse($request->input("amount"));
        $movement->save();

        return redirect()->route("debts.payments.edit", [
            "debt" => $debt,
            "movement" => $movement
        ])->with(Alert::success("Pagamento atualizado com sucesso."));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt, Movement $movement)
    {
        if (Gate::denies("debt-edit", $debt) || Gate::denies("movement-edit", $movement)) {
            abort(403);
        }

        $movement->delete();

        return redirect()->route("debts.payments.index", $debt)->with(
            Alert::success("Pagamento deletado com sucesso.")
        );
    }
}
