<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreInvestimentLeaveRequest;
use App\Http\Requests\UpdateInvestimentLeaveRequest;
use App\Models\Investiment;
use App\Models\Leave;
use App\Repositories\Contracts\InvestimentRepositoryContract;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class InvestimentLeaveController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Investiment $investiment)
    {
        if (Gate::denies("investiment-edit", $investiment)) {
            abort(403);
        }

        $investiment->load("leaves");

        return view("investiments.leaves.index", compact("investiment"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Investiment $investiment)
    {
        if (Gate::denies("investiment-edit", $investiment)) {
            abort(403);
        }

        return view("investiments.leaves.create", compact("investiment"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreInvestimentLeaveRequest $request,
        LeaveRepositoryContract $leaveRepository,
        MovementRepositoryContract $movementRepository,
        Investiment $investiment
    ) {
        if (Gate::denies("investiment-edit", $investiment)) {
            abort(403);
        }

        $leave = $leaveRepository->create(auth()->id(), [
            "leaveable_type" => Investiment::class,
            "leaveable_id" => $investiment->id,
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        $movementRepository->create(auth()->id(), [
            "movementable_type" => Leave::class,
            "movementable_id" => $leave->id
        ]);

        return redirect()->route("investiments.leaves.index", $investiment)
            ->with(Alert::success("Depósito adicionado com sucesso"));
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
    public function edit(Investiment $investiment, Leave $leave)
    {
        if (Gate::denies("leave-edit", $leave)) {
            abort(403);
        }

        return view("investiments.leaves.edit", compact("investiment", "leave"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateInvestimentLeaveRequest $request,
        LeaveRepositoryContract $leaveRepository,
        Investiment $investiment,
        Leave $leave
    ) {
        if (Gate::denies("leave-edit", $leave)) {
            abort(403);
        }

        $leaveRepository->update($leave->id, [
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("investiments.leaves.edit", [
            "investiment" => $investiment,
            "leave" => $leave
        ])->with(Alert::success("Depósito atualizado com sucesso"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        MovementRepositoryContract $movementRepository,
        LeaveRepositoryContract $leaveRepository,
        InvestimentRepositoryContract $investimentRepository,
        Investiment $investiment,
        Leave $leave
    ) {
        if (Gate::denies("leave-edit", $leave)) {
            abort(403);
        }

        $movementRepository->delete($leave->movement->id);
        $leaveRepository->delete($leave->id);

        return redirect()->route("investiments.leaves.index", [
            "investiment" => $investiment
        ])->with(Alert::success("Depósito removido com sucesso"));
    }
}
