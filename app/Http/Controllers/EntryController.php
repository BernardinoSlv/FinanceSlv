<?php

namespace App\Http\Controllers;

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
}
