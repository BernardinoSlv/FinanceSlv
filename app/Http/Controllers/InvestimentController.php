<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreInvestimentRequest;
use App\Http\Requests\UpdateInvestimentRequest;
use App\Models\Investiment;
use App\Repositories\Contracts\InvestimentRepositoryContract;
use Src\Parsers\RealToFloatParser;

class InvestimentController extends Controller
{
    public function __construct(
        protected InvestimentRepositoryContract $_investimentRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $investiments = $this->_investimentRepository->allByUser(auth()->user()->id);

        return view("investiment.index", compact(
            "investiments"
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("investiment.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInvestimentRequest $request)
    {
        $this->_investimentRepository->create(auth()->user()->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("investiments.index")->with(
            Alert::success("Investimento criado com sucesso.")
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Investiment $investiment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Investiment $investiment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvestimentRequest $request, Investiment $investiment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Investiment $investiment)
    {
        //
    }
}
