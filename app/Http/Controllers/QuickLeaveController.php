<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\UpdateLeaveRequest;
use App\Models\Leave;
use App\Models\QuickLeave;
use App\Repositories\Contracts\IdentifierRepositoryContract;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\QuickLeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class QuickLeaveController extends Controller
{
    protected QuickLeaveRepositoryContract $_quickLeaveRepository;

    public function __construct(QuickLeaveRepositoryContract $quickLeaveRepository)
    {
        $this->_quickLeaveRepository = $quickLeaveRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $quickLeaves = $this->_quickLeaveRepository->allByUser(auth()->user()->id, true);

        return view("quick-leaves.index", compact(
            "quickLeaves"
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(IdentifierRepositoryContract $identifierRepository)
    {
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("quick-leaves.create", compact(
            "identifiers"
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreLeaveRequest $request,
        LeaveRepositoryContract $leaveRepository,
        MovementRepositoryContract $movementRepository
    ) {
        $quickLeave = $this->_quickLeaveRepository->create(auth()->id(), [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        $leave = $leaveRepository->create(auth()->id(), [
            "leaveable_type" => QuickLeave::class,
            "leaveable_id" => $quickLeave->id
        ]);
        $movementRepository->create(auth()->id(), [
            "movementable_type" => Leave::class,
            "movementable_id" => $leave->id
        ]);

        return redirect()->route("quick-leaves.index")->with(
            Alert::success("Saída criada com sucesso.")
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(QuickLeave $leave)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(IdentifierRepositoryContract $identifierRepository, QuickLeave $quickLeave)
    {
        if (!Gate::allows("quick-leave-edit", $quickLeave)) {
            abort(404);
        }
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("quick-leaves.edit", compact(
            "quickLeave",
            "identifiers"
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveRequest $request, QuickLeave $quickLeave)
    {
        if (!Gate::allows("quick-leave-edit", $quickLeave)) {
            abort(404);
        }
        $this->_quickLeaveRepository->update($quickLeave->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("quick-leaves.index")->with(
            Alert::success("Saída atualizada com sucesso.")
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        LeaveRepositoryContract $leaveRepository,
        MovementRepositoryContract $movementRepository,
        QuickLeave $quickLeave
    ) {
        if (!Gate::allows("quick-leave-edit", $quickLeave)) {
            abort(404);
        }
        $this->_quickLeaveRepository->delete($quickLeave->id);
        $movementRepository->deletePolymorph(Leave::class, $quickLeave->leave->id);
        $leaveRepository->delete($quickLeave->leave->id);

        return redirect()->route("quick-leaves.index")->with(
            Alert::success("Saída excluída com sucesso.")
        );
    }
}
