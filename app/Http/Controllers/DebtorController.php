<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreDebtorRequest;
use App\Http\Requests\UpdateDebtorRequest;
use App\Models\Debtor;
use App\Repositories\Contracts\DebtorRepositoryContract;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class DebtorController extends Controller
{
    protected DebtorRepositoryContract $_debtorRepository;

    public function __construct(DebtorRepositoryContract $debtorRepository)
    {
        $this->_debtorRepository = $debtorRepository;
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $debtors = $this->_debtorRepository->allByUser(auth()->user()->id);

        return view("debtors.index", compact("debtors"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("debtors.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDebtorRequest $request)
    {
        $this->_debtorRepository->create(auth()->user()->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("debtors.index")->with(
            Alert::success("Devedor criado com sucesso.")
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Debtor $debtor)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debtor $debtor)
    {
        if (Gate::denies("debtor-edit", $debtor)) {
            abort(404);
        }

        return view("debtors.edit", compact("debtor"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDebtorRequest $request, Debtor $debtor)
    {
        if (Gate::denies("debtor-edit", $debtor)) {
            abort(404);
        }

        $this->_debtorRepository->update($debtor->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("debtors.edit", $debtor)->with(
            Alert::success("Atualização realizada com sucesso.")
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debtor $debtor)
    {
        if (Gate::denies("debtor-edit", $debtor)) {
            abort(404);
        }

        $this->_debtorRepository->delete($debtor->id);

        return redirect()->route("debtors.index")->with(
            Alert::success("Registro deletado com sucesso.")
        );
    }
}
