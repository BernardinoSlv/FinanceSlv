<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreNeedRequest;
use App\Http\Requests\UpdateNeedRequest;
use App\Models\Need;
use App\Repositories\Contracts\NeedRepositoryContract;
use Src\Parsers\RealToFloatParser;

class NeedController extends Controller
{
    public function __construct(
        protected NeedRepositoryContract $_needRepository
    ) {
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $needs = $this->_needRepository->allByUser(auth()->id());

        return view("needs.index", compact(
            "needs"
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view("needs.create");
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreNeedRequest $request)
    {
        $this->_needRepository->create(auth()->id(), [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        return redirect()->route("needs.index")->with(
            Alert::success("Necessidade criada com sucesso.")
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(Need $need)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Need $need)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateNeedRequest $request, Need $need)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Need $need)
    {
        //
    }
}
