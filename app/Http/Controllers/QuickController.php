<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreQuickRequest;
use App\Http\Requests\UpdateQuickRequest;
use App\Models\Quick;

class QuickController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        /**
         * @var User
         */
        $user =  auth()->user();
        $paginator = $user->quicks()->with(["movement", "identifier"])->paginate(
            request("per_page", 10)
        )
            ->withQueryString();

        return view("quicks.index", compact("paginator"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        /**
         * @var User
         */
        $user = auth()->user();
        $identifiers = $user->identifiers;

        return view("quicks.create", compact("identifiers"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreQuickRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Quick $quick)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quick $quick)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuickRequest $request, Quick $quick)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quick $quick)
    {
        //
    }
}
