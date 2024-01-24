<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use App\Models\Entity;
use App\Repositories\Contracts\EntityRepositoryContract;
use App\Repositories\EntityRepository;
use Illuminate\Support\Facades\Gate;

class EntityController extends Controller
{
    public function __construct(
        protected EntityRepositoryContract $_entityRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $entities = $this->_entityRepository->allByUser(auth()->id());

        return view("entities.index", compact(
            "entities"
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("entities.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreEntityRequest $request)
    {
        $data = $request->validated();

        if ($request->file("avatar")) {
            $data["avatar"] = $request->file("avatar")->store("avatar");
        }

        $this->_entityRepository->create(auth()->id(), $data);

        return redirect()->route("entities.index")->with(
            Alert::success("Entidade criada com sucesso.")
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Entity $entity)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Entity $entity)
    {
        if (Gate::denies("entity-edit", $entity)) {
            abort(404);
        }

        return  view("entities.edit", compact(
            "entity"
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntityRequest $request, Entity $entity)
    {
        if (Gate::denies("entity-edit", $entity)) {
            abort(404);
        }

        $data = $request->validated();
        if ($request->file("avatar")) {
            $data["avatar"] = $request->file("avatar")->store("avatar");
        }

        $this->_entityRepository->update($entity->id, $data);

        return redirect()->route("entities.edit", $entity)->with(
            Alert::success("Entidade atualizada com sucesso.")
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entity $entity)
    {
        //
    }
}
