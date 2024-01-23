<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreDebtRequest;
use App\Http\Requests\UpdateDebtRequest;
use App\Models\Debt;
use App\Repositories\Contracts\DebtRepositoryContract;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class DebtController extends Controller
{
    public function __construct(
        protected DebtRepositoryContract $_debtRepository
    ) {
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $debts = $this->_debtRepository->allByUser(auth()->user()->id);

        return view("debts.index", compact(
            "debts"
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("debts.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDebtRequest $request)
    {
        $this->_debtRepository->create(auth()->user()->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("debts.index")->with(
            Alert::success("Registro criado com sucesso.")
        );
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
        if (Gate::denies("debt-edit", $debt)) {
            abort(404);
        }

        return view("debts.edit", compact(
            "debt"
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDebtRequest $request, Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt)) {
            abort(404);
        }
        $this->_debtRepository->update($debt->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("debts.index")->with(
            Alert::success("Registro atualizado com sucesso.")
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt)) {
            abort(404);
        }

        $this->_debtRepository->delete($debt->id);

        return redirect()->route("debts.index")->with(
            Alert::success("Registro removido com sucesso.")
        );
    }
}
