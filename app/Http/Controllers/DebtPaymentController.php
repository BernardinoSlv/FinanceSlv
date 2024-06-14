<?php

namespace App\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Helpers\Alert;
use App\Http\Requests\StoreDebtPaymentRequest;
use App\Http\Requests\UpdateDebtRequest;
use App\Models\Debt;
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
    public function edit(Debt $debt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDebtPaymentRequest $request, Debt $debt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt)
    {
        //
    }
}
