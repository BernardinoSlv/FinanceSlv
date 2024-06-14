<?php

namespace App\Http\Controllers;

use App\Enums\MovementTypeEnum;
use App\Http\Requests\StoreDebtRequest;
use App\Http\Requests\UpdateDebtRequest;
use App\Models\Debt;
use Illuminate\Support\Facades\Gate;

class DebtPaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Debt $debt)
    {
        if (Gate::denies("debt-edit", $debt)) {
            abort(403);
        }

        $movements = $debt->movements()
            ->where("type", MovementTypeEnum::OUT->value)
            ->paginate();

        return view("debts.payments.index", compact("debt", "movements"));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
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
