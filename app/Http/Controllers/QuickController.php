<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreQuickRequest;
use App\Http\Requests\UpdateQuickRequest;
use App\Models\Quick;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Src\Parsers\RealToFloatParser;

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
        $paginator = $user->quicks()
            ->orderBy("created_at", "desc")
            ->with(["movement", "identifier"])->paginate(
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
        DB::beginTransaction();
        $quick = Quick::query()->create([
            ...$request->validated(),
            "user_id" => auth()->id()
        ]);
        $quick->movement()->create([
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount")),
            "user_id" => auth()->id()
        ]);
        DB::commit();

        return redirect()->route("quicks.index")
            ->with(Alert::success("Registro criado."));
    }

    /**
     * Display the specified resource.
     */
    public function show(Quick $quick)
    {
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Quick $quick)
    {
        if (Gate::denies("quick-edit", $quick)) {
            abort(403);
        }

        /**
         * @var User
         */
        $user = auth()->user();
        $identifiers = $user->identifiers;

        return view("quicks.edit", compact("quick", "identifiers"));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateQuickRequest $request, Quick $quick)
    {
        if (Gate::denies("quick-edit", $quick)) {
            abort(403);
        }
        DB::beginTransaction();
        $quick->fill($request->validated());
        $quick->save();

        $movement = $quick->movement()->withTrashed()->first();
        if ($movement->trashed()) {
            $movement->restore();
        }

        $movement->fill([
            ...$request->validated(),
            'amount' => RealToFloatParser::parse($request->input('amount'))
        ])->save();
        DB::commit();


        return redirect()->route("quicks.edit", $quick)
            ->with(Alert::success("Registro atualizado com sucesso"));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Quick $quick)
    {
        if (Gate::denies("quick-edit", $quick)) {
            abort(403);
        }

        DB::beginTransaction();
        $quick->movement->delete();
        $quick->delete();
        DB::commit();

        return redirect()->route('quicks.index')
            ->with(Alert::success("Registro deletado com suceso."));
    }
}
