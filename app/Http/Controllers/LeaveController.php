<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\UpdateLeaveRequest;
use App\Models\Leave;
use App\Repositories\Contracts\IdentifierRepositoryContract;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class LeaveController extends Controller
{
    protected LeaveRepositoryContract $_leaveRepository;

    public function __construct(LeaveRepositoryContract $leaveRepository)
    {
        $this->_leaveRepository = $leaveRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $leaves = $this->_leaveRepository->allByUser(auth()->user()->id, true);

        return view("leaves.index", compact(
            "leaves"
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(IdentifierRepositoryContract $identifierRepository)
    {
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("leaves.create", compact(
            "identifiers"
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreLeaveRequest $request,
        MovementRepositoryContract $movementRepository
    ) {
        $leave = $this->_leaveRepository->create(auth()->user()->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        $movementRepository->create(auth()->id(), [
            "movementable_type" => Leave::class,
            "movementable_id" => $leave->id
        ]);

        return redirect()->route("leaves.index")->with(
            Alert::success("Saída criada com sucesso.")
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Leave $leave)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IdentifierRepositoryContract $identifierRepository, Leave $leave)
    {
        if (!Gate::allows("leave-edit", $leave)) {
            abort(404);
        }
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("leaves.edit", compact(
            "leave",
            "identifiers"
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveRequest $request, Leave $leave)
    {
        if (!Gate::allows("leave-edit", $leave)) {
            abort(404);
        }
        $this->_leaveRepository->update($leave->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("leaves.index")->with(
            Alert::success("Saída atualizada com sucesso.")
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(MovementRepositoryContract $movementRepository, Leave $leave)
    {
        if (!Gate::allows("leave-edit", $leave)) {
            abort(404);
        }
        $this->_leaveRepository->delete($leave->id);
        $movementRepository->deletePolymorph(Leave::class, $leave->id);

        return redirect()->route("leaves.index")->with(
            Alert::success("Saída excluída com sucesso.")
        );
    }
}
