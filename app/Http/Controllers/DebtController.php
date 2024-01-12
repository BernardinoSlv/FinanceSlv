<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDebtRequest;
use App\Http\Requests\UpdateDebtRequest;
use App\Models\Debt;
use App\Repositories\Contracts\DebtRepositoryContract;

class DebtController extends Controller
{
    public function __construct(
        protected DebtRepositoryContract $_debtRepository
    ) {
    }


    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $debts = $this->_debtRepository->allByUser(auth()->user()->id);

        return view("debt.index", compact(
            "debts"
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("debt.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDebtRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Debt $debt)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Debt $debt)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDebtRequest $request, Debt $debt)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Debt $debt)
    {
        //
    }
}
