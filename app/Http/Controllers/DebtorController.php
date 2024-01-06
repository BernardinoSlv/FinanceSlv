<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreDebtorRequest;
use App\Http\Requests\UpdateDebtorRequest;
use App\Models\Debtor;
use App\Repositories\Contracts\DebtorRepositoryContract;
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

        return view("debtor.index", compact("debtors"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("debtor.create");
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDebtorRequest $request, Debtor $debtor)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debtor $debtor)
    {
        //
    }
}
