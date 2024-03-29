<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Models\Entry;
use App\Models\QuickEntry;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\QuickEntryRepositoryContract;
use App\Repositories\Contracts\IdentifierRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use App\Repositories\EntryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

class QuickEntryController extends Controller
{
    protected QuickEntryRepositoryContract $_quickEntryRepository;

    public function __construct(
        QuickEntryRepositoryContract $quickEntryRepository
    ) {
        $this->_quickEntryRepository = $quickEntryRepository;
    }

    public function index()
    {
        $quickEntries = $this->_quickEntryRepository->allByUser(auth()->user()->id, true);

        return view("quick-entries.index", compact(
            "quickEntries"
        ));
    }

    public function create(IdentifierRepositoryContract $identifierRepository)
    {
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("quick-entries.create", compact(
            "identifiers"
        ));
    }

    public function store(
        StoreEntryRequest $request,
        EntryRepositoryContract $entryRepository,
        MovementRepositoryContract $movementRepository
    ) {
        $quickEntry = $this->_quickEntryRepository->create(auth()->user()->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        $entry = $entryRepository->create(auth()->id(), [
            "entryable_type" => QuickEntry::class,
            "entryable_id" => $quickEntry->id
        ]);
        $movementRepository->create(auth()->id(), [
            "movementable_type" => Entry::class,
            "movementable_id" => $entry->id
        ]);

        return redirect(route("quick-entries.index"))->with(
            Alert::success("Entrada criada com sucesso.")
        );
    }

    public function edit(
        IdentifierRepositoryContract $identifierRepository,
        QuickEntry $quickEntry
    ) {
        if (!Gate::allows("quick-entry-edit", $quickEntry)) {
            abort(404);
        }
        $identifiers = $identifierRepository->allByUser(auth()->id());
        return view("quick-entries.edit", compact(
            "quickEntry",
            "identifiers"
        ));
    }

    public function update(UpdateEntryRequest $request, QuickEntry $quickEntry)
    {
        if (!Gate::allows("quick-entry-edit", $quickEntry)) {
            abort(404);
        }
        $this->_quickEntryRepository->update($quickEntry->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        return redirect()->route("quick-entries.edit", $quickEntry)->with(
            Alert::success("Entrada atualizada")
        );
    }

    public function destroy(
        MovementRepositoryContract $movementRepository,
        EntryRepositoryContract $entryRepository,
        QuickEntry $quickEntry
    ) {
        if (!Gate::allows("quick-entry-edit", $quickEntry)) {
            abort(404);
        }
        $this->_quickEntryRepository->delete($quickEntry->id);
        $movementRepository->deletePolymorph(Entry::class, $quickEntry->entry->id);
        $entryRepository->delete($quickEntry->entry->id);

        return redirect()->route("quick-entries.index")->with(
            Alert::success("Entrada excluída com sucesso")
        );
    }
}
