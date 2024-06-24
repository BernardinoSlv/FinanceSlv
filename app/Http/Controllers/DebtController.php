<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreDebtRequest;
use App\Http\Requests\UpdateDebtRequest;
use App\Models\Debt;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class DebtController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $debts = auth()->user()->debts()
            ->with("identifier")
            ->withSum("movements", "amount")
            ->orderBy("id", "desc")
            ->paginate();

        return view("debts.index", compact("debts"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $identifiers = auth()->user()->identifiers()->orderBy('name')->orderBy("id")->get();

        return view("debts.create", compact("identifiers"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDebtRequest $request)
    {
        $debt = new Debt([
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input('amount'))
        ]);
        $debt->user_id = auth()->id();
        $debt->save();

        return redirect()->route("debts.index")->with(
            Alert::success("Dívida criada com sucesso.")
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt)) {
            abort(403);
        }

        $identifiers = auth()->user()->identifiers()
            ->orderBy("name")
            ->orderBy("id")
            ->get();

        return view("debts.edit", compact("debt", "identifiers"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDebtRequest $request, Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt))
            abort(403);

        $debt->fill([
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ])->save();

        return redirect()->route("debts.edit", $debt)
            ->with(Alert::success("Dívida atualizada com sucesso."));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt))
            abort(403);

        $debt->delete();

        return redirect()->route("debts.index")
            ->with(Alert::success("Dívida deletada com sucesso."));
    }
}
