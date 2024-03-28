<?php

namespace App\Http\Controllers;

use App\Models\Debtor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class DebtorPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Debtor $debtor)
    {
        if (Gate::denies("debtor-edit", $debtor)) {
            abort(403);
        }

        $debtor->load("entries");

        return view("debtors.payments.index", compact("debtor"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Debtor $debtor)
    {
        if (Gate::denies("debtor-edit", $debtor)) {
            abort(403);
        }

        return view("debtors.payments.create", compact("debtor"));
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
