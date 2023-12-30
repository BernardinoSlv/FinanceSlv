<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreLeaveRequest;
use App\Http\Requests\UpdateLeaveRequest;
use App\Models\Leave;
use App\Repositories\Contracts\LeaveRepositoryContract;
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

        return view("leave.index", compact(
            "leaves"
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("leave.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLeaveRequest $request)
    {
        $this->_leaveRepository->create(auth()->user()->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
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
    public function edit(Leave $leave)
    {
        if (!Gate::allows("leave-edit", $leave)) {
            abort(404);
        }

        return view("leave.edit", compact(
            "leave"
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLeaveRequest $request, Leave $leave)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Leave $leave)
    {
        //
    }
}
