<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreDebtPaymentRequest;
use App\Http\Requests\UpdateDebtorPaymentRequest;
use App\Models\Debtor;
use App\Models\Entry;
use App\Repositories\Contracts\EntryRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

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
    public function store(
        StoreDebtPaymentRequest $request,
        EntryRepositoryContract $entryRepository,
        MovementRepositoryContract $movementRepository,
        Debtor $debtor
    ) {
        if (Gate::denies("debtor-edit", $debtor)) {
            abort(403);
        }

        $entry = $entryRepository->create(auth()->id(), [
            ...$request->validated(),
            "entryable_type" => Debtor::class,
            "entryable_id" => $debtor->id,
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);
        $movementRepository->create(auth()->id(), [
            "movementable_type" => Entry::class,
            "movementable_id" => $entry->id
        ]);

        return redirect()->route("debtors.payments.index", $debtor)
            ->with(Alert::success("Pagamento adicionado com sucesso"));
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
    public function edit(Debtor $debtor, Entry $entry)
    {
        if (Gate::denies("entry-edit", $entry)) {
            abort(403);
        }

        return view("debtors.payments.edit", compact("debtor", "entry"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateDebtorPaymentRequest $request,
        EntryRepositoryContract $entryRepository,
        Debtor $debtor,
        Entry $entry
    ) {
        if (Gate::denies("entry-edit", $entry)) {
            abort(403);
        }

        $entryRepository->update($entry->id, [
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("debtors.payments.edit", [
            "debtor" => $debtor,
            "entry" => $entry
        ])
            ->with(Alert::success("Pagamento atualizado com sucesso"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
