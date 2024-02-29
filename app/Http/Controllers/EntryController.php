<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Models\Entry;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\IdentifierRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class EntryController extends Controller
{
    protected EntryRepositoryContract $_entryRepository;

    public function __construct(
        EntryRepositoryContract $entryRepository
    ) {
        $this->_entryRepository = $entryRepository;
    }

    public function index()
    {
        $entries = $this->_entryRepository->allByUser(auth()->user()->id, true);

        return view("entries.index", compact(
            "entries"
        ));
    }

    public function create(IdentifierRepositoryContract $identifierRepository)
    {
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("entries.create", compact(
            "identifiers"
        ));
    }

    public function store(
        StoreEntryRequest $request,
        MovementRepositoryContract $movementRepository
    ) {
        $entry = $this->_entryRepository->create(auth()->user()->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        $movementRepository->create(auth()->id(), [
            "movementable_type" => Entry::class,
            "movementable_id" => $entry->id
        ]);

        return redirect(route("entries.index"))->with(
            Alert::success("Entrada criada com sucesso.")
        );
    }

    public function edit(IdentifierRepositoryContract $identifierRepository, Entry $entry)
    {
        if (!Gate::allows("entry-edit", $entry)) {
            abort(404);
        }
        $identifiers = $identifierRepository->allByUser(auth()->id());
        return view("entries.edit", compact(
            "entry",
            "identifiers"
        ));
    }

    public function update(UpdateEntryRequest $request, Entry $entry)
    {
        if (!Gate::allows("entry-edit", $entry)) {
            abort(404);
        }
        $this->_entryRepository->update($entry->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        return redirect()->route("entries.edit", [
            "entry" => $entry->id,
        ])->with(
            Alert::success("Entrada atualizada")
        );
    }

    public function destroy(MovementRepositoryContract $movementRepository, Entry $entry)
    {
        if (!Gate::allows("entry-edit", $entry)) {
            abort(404);
        }
        $this->_entryRepository->delete($entry->id);
        $movementRepository->deletePolymorph(Entry::class, $entry->id);

        return redirect()->route("entries.index")->with(
            Alert::success("Entrada excluída com sucesso")
        );
    }
}
