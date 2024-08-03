<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreIdentifierRequest;
use App\Http\Requests\UpdateIdentifierRequest;
use App\Models\Identifier;
use App\Repositories\Contracts\IdentifierRepositoryContract;
use App\Repositories\IdentifierRepository;
use Illuminate\Support\Facades\Gate;

class IdentifierController extends Controller
{
    public function __construct(
        protected IdentifierRepositoryContract $_identifierRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $identifiers = auth()->user()->identifiers()->orderBy("identifiers.id", "DESC")
            ->paginate();

        return view("identifiers.index", compact(
            "identifiers"
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("identifiers.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIdentifierRequest $request)
    {
        $data = $request->validated();

        if ($request->file("avatar")) {
            $data["avatar"] = $request->file("avatar")->store("avatar");
        }

        $this->_identifierRepository->create(auth()->id(), $data);

        return redirect()->route("identifiers.index")->with(
            Alert::success("Entidade criada com sucesso.")
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Identifier $identifier)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Identifier $identifier)
    {
        if (Gate::denies("identifier-edit", $identifier)) {
            abort(404);
        }

        return  view("identifiers.edit", compact(
            "identifier"
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateIdentifierRequest $request, Identifier $identifier)
    {
        if (Gate::denies("identifier-edit", $identifier)) {
            abort(404);
        }

        $data = $request->validated();
        if ($request->file("avatar")) {
            $data["avatar"] = $request->file("avatar")->store("avatar");
        }

        $this->_identifierRepository->update($identifier->id, $data);

        return redirect()->route("identifiers.edit", $identifier)->with(
            Alert::success("Entidade atualizada com sucesso.")
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Identifier $identifier)
    {
        if (Gate::denies("identifier-edit", $identifier)) {
            abort(404);
        }

        $this->_identifierRepository->delete($identifier->id);
        return redirect()->route("identifiers.index")->with(
            Alert::success("Entidade removida com sucesso.")
        );
    }
}
