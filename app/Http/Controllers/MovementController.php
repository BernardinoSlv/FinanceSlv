<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreMovementRequest;
use App\Http\Requests\UpdateMovementRequest;
use App\Models\Movement;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /** @var User $user */
        $user = auth()->user();
        $movements = $user->movements()
            ->with(["movementable", "movementable.identifier"])
            ->orderBy('id', "DESC")
            ->paginate();

        return view("movements.index", compact("movements"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreMovementRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Movement $movement)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Movement $movement)
    {
        if (Gate::denies("movement-edit", $movement)) {
            abort(403);
        }

        return view("movements.edit", compact('movement'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateMovementRequest $request, Movement $movement)
    {
        if (Gate::denies("movement-edit", $movement)) abort(403);

        $movement->fill([
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ])->save();

        return redirect()->route("movements.edit", $movement)
            ->with(Alert::success("Movimentação editada com sucesso."));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Movement $movement)
    {
        //
    }
}
