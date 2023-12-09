<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreEntryRequest;
use App\Repositories\Contracts\EntryRepositoryContract;
use Illuminate\Http\Request;

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
        $entries = $this->_entryRepository->allByUser(auth()->user()->id);

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
        $this->_entryRepository->create(auth()->user()->id, $request->validated());
        return redirect(route("entry.index"))->with(
            Alert::success("Entrada criada com sucesso.")
        );
    }
}
