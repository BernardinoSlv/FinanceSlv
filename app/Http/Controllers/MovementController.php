<?php

namespace App\Http\Controllers;

use App\Enums\MovementableEnum;
use App\Helpers\Alert;
use App\Http\Requests\StoreMovementRequest;
use App\Http\Requests\UpdateMovementRequest;
use App\Models\Debt;
use App\Models\Movement;
use App\Pipes\Movement\FilterByTextPipe;
use App\Pipes\Movement\OperationTypePipe;
use App\Pipes\Movement\OrderByPipe;
use App\Pipes\Movement\TypePipe;
use Illuminate\Pipeline\Pipeline;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class MovementController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Pipeline $pipeline)
    {
        // Movement::query()->where("movementable_type", Debt::class)
        //     ->each(function (Movement $movement) {
        //         $movement->effetive_at = now()->addDays(rand(0, 30));
        //         $movement->save();
        //     });


        /** @var User $user */
        $user = auth()->user();
        $movements = $pipeline->send($user->movements())
            ->through([
                FilterByTextPipe::class,
                OrderByPipe::class,
                OperationTypePipe::class,
                TypePipe::class
            ])
            ->thenReturn()
            ->addSelect('movements.*')
            ->with(["identifier", "movementable"])
            ->paginate()
            ->withQueryString();

        // dd($movements->first());

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
        if (Gate::denies("movement-edit", $movement)) abort(403);

        $movementable = $movement->movementable;
        $movement->delete();

        if (MovementableEnum::from(get_class($movementable))->canDelete())
            $movementable->delete();

        return redirect()->route("movements.index")
            ->with(Alert::success("Movimentação deletada com sucesso."));
    }
}
