<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreInvestimentEntryRequest;
use App\Http\Requests\UpdateInvestimentEntryRequest;
use App\Models\Entry;
use App\Models\Investiment;
use App\Models\Movement;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class InvestimentEntryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Investiment $investiment)
    {
        if (Gate::denies("investiment-edit", $investiment)) {
            abort(403);
        }

        $investiment->load("entries");

        return view("investiments.entries.index", compact("investiment"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Investiment $investiment)
    {
        if (Gate::denies("investiment-edit", $investiment)) {
            abort(403);
        }

        return view("investiments.entries.create", compact("investiment"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(
        StoreInvestimentEntryRequest $request,
        EntryRepositoryContract $entryRepository,
        MovementRepositoryContract $movementRepository,
        Investiment $investiment
    ) {
        if (Gate::denies("investiment-edit", $investiment)) {
            abort(403);
        }

        $entry = $entryRepository->create(auth()->id(), [
            "entryable_type" => Investiment::class,
            "entryable_id" => $investiment->id,
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        $movementRepository->create(auth()->id(), [
            "movementable_type" => Entry::class,
            "movementable_id" => $entry->id
        ]);

        return redirect()->route("investiments.entries.index", $investiment)
            ->with(Alert::success("Depósito adicionado com sucesso"));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Investiment $investiment, Entry $entry)
    {
        if (Gate::denies("entry-edit", $entry)) {
            abort(403);
        }

        return view("investiments.entries.edit", compact("investiment", "entry"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateInvestimentEntryRequest $request,
        EntryRepositoryContract $entryRepository,
        Investiment $investiment,
        Entry $entry
    ) {
        if (Gate::denies("entry-edit", $entry)) {
            abort(403);
        }

        $entryRepository->update($entry->id, [
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("investiments.entries.edit", [
            "investiment" => $investiment,
            "entry" => $entry
        ])->with(Alert::success("Depósito atualizado com sucesso"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        MovementRepositoryContract $movementRepository,
        EntryRepositoryContract $entryRepository,
        Investiment $investiment,
        Entry $entry
    ) {
        if (Gate::denies("entry-edit", $entry)) {
            abort(403);
        }

        $movementRepository->delete($entry->movement->id);
        $entryRepository->delete($entry->id);

        return redirect()->route("investiments.entries.index", $investiment)
            ->with(Alert::success("Depósito removido com sucesso"));
    }
}
