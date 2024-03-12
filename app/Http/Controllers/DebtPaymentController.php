<?php

namespace App\Http\Controllers;

use App\Models\Debt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DebtPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt)) {
            abort(404);
        }

        $debt->load("leaves");

        return view("debts.payments.index", compact("debt"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt)) {
            abort(404);
        }

        return view("debts.payments.create", compact("debt"));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
