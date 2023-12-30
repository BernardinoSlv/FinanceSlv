<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreEntryRequest;
use App\Http\Requests\UpdateEntryRequest;
use App\Models\Entry;
use App\Repositories\Contracts\EntryRepositoryContract;
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

        return view("entry.index", compact(
            "entries"
        ));
    }

    public function create()
    {
        return view("entry.create");
    }

    public function store(StoreEntryRequest $request)
    {
        $this->_entryRepository->create(auth()->user()->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        return redirect(route("entry.index"))->with(
            Alert::success("Entrada criada com sucesso.")
        );
    }

    public function edit(Entry $entry)
    {
        if (!Gate::allows("entry-edit", $entry)) {
            abort(404);
        }

        return view("entry.edit", compact(
            "entry"
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
        return redirect()->route("entry.edit", [
            "entry" => $entry->id,
        ])->with(
            Alert::success("Entrada atualizada")
        );
    }

    public function destroy(Entry $entry)
    {
        if (!Gate::allows("entry-edit", $entry)) {
            abort(404);
        }
        $this->_entryRepository->delete($entry->id);

        return redirect()->route("entry.index")->with(
            Alert::success("Entrada excluída com sucesso")
        );
    }
}
