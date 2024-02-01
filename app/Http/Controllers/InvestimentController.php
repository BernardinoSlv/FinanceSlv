<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreInvestimentRequest;
use App\Http\Requests\UpdateInvestimentRequest;
use App\Models\Investiment;
use App\Repositories\Contracts\IdentifierRepositoryContract;
use App\Repositories\Contracts\InvestimentRepositoryContract;
use App\Repositories\IdentifierRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Gate;
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

        return view("investiments.index", compact(
            "investiments"
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(IdentifierRepositoryContract $identifierRepository)
    {
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("investiments.create", compact(
            "identifiers"
        ));
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
    public function edit(
        IdentifierRepositoryContract $identifierRepository,
        Investiment $investiment
    ) {
        if (Gate::denies("investiment-edit", $investiment)) {
            abort(404);
        }
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("investiments.edit", compact(
            "investiment",
            "identifiers"
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateInvestimentRequest $request, Investiment $investiment)
    {
        if (Gate::denies("investiment-edit", $investiment)) {
            abort(404);
        }

        $this->_investimentRepository->update($investiment->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("investiments.index")->with(
            Alert::success("Registro atualizado com sucesso.")
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Investiment $investiment)
    {
        if (Gate::denies("investiment-edit", $investiment)) {
            abort(404);
        }

        $this->_investimentRepository->delete($investiment->id);

        return redirect()->route("investiments.index")->with(
            Alert::success("Investimento removido com sucesso.")
        );
    }
}
