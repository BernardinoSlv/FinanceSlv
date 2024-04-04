<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreInvestimentLeaveRequest;
use App\Models\Investiment;
use App\Models\Leave;
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
