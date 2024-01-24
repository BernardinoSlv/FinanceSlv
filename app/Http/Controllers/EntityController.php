<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEntityRequest;
use App\Http\Requests\UpdateEntityRequest;
use App\Models\Entity;
use App\Repositories\Contracts\EntityRepositoryContract;
use App\Repositories\EntityRepository;

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
        //
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateEntityRequest $request, Entity $entity)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Entity $entity)
    {
        //
    }
}
