<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreDebtPaymentRequest;
use App\Http\Requests\UpdateDebtPaymentRequest;
use App\Models\Debt;
use App\Models\Leave;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Http\Request;
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
            abort(404);
        }

        $debt->load("leaves");

        return view("debts.payments.index", compact("debt"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt)) {
            abort(404);
        }

        return view("debts.payments.create", compact("debt"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreDebtPaymentRequest $request,
        LeaveRepositoryContract $leaveRepository,
        MovementRepositoryContract $movementRepository,
        Debt $debt
    ) {
        if (Gate::denies("debt-edit", $debt)) {
            abort(404);
        }

        $leave = $leaveRepository->create(auth()->id(), [
            "leaveable_type" => Debt::class,
            "leaveable_id" => $debt->id,
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        $movementRepository->create(auth()->id(), [
            "movementable_type" => Leave::class,
            "movementable_id" => $leave->id
        ]);

        return redirect()->route("debts.payments.index", $debt)
            ->with(Alert::success("Pagamento adicionado com sucesso"));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debt $debt, Leave $leave)
    {
        if (Gate::denies("leave-edit", $leave)) {
            abort(403);
        }

        return view("debts.payments.edit", compact("debt", "leave"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateDebtPaymentRequest $request,
        LeaveRepositoryContract $leaveRepository,
        Debt $debt,
        Leave $leave
    ) {
        if (Gate::denies("leave-edit", $leave)) {
            abort(403);
        }

        $leaveRepository->update($leave->id, [
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("debts.payments.edit", [
            "debt" => $debt,
            "leave" => $leave
        ])->with(Alert::success("Pagamento atualizado com sucesso"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        LeaveRepositoryContract $leaveRepository,
        MovementRepositoryContract $movementRepository,
        Debt $debt,
        Leave $leave
    ) {
        if (Gate::denies("leave-edit", $leave)) {
            abort(403);
        }

        $movementRepository->delete($leave->movement->id);
        $leaveRepository->delete($leave->id);

        return redirect()->route("debts.payments.index", $debt)
            ->with(Alert::success("Registro removido com sucesso"));
    }
}
