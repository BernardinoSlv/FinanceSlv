<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Http\Requests\StoreNeedRequest;
use App\Http\Requests\UpdateNeedRequest;
use App\Models\Leave;
use App\Models\Need;
use App\Repositories\Contracts\IdentifierRepositoryContract;
use App\Repositories\Contracts\LeaveRepositoryContract;
use App\Repositories\Contracts\MovementRepositoryContract;
use App\Repositories\Contracts\NeedRepositoryContract;
use App\Repositories\LeaveRepository;
use Illuminate\Support\Facades\Gate;
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
    public function create(IdentifierRepositoryContract $identifierRepository)
    {
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("needs.create", compact(
            "identifiers"
        ));
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
    public function edit(IdentifierRepositoryContract $identifierRepository, Need $need)
    {
        if (Gate::denies("need-edit", $need)) {
            abort(404);
        }
        $identifiers = $identifierRepository->allByUser(auth()->id());

        return view("needs.edit", compact(
            "need",
            "identifiers"
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(
        UpdateNeedRequest $request,
        LeaveRepositoryContract $leaveRepository,
        MovementRepositoryContract $movementRepository,
        Need $need
    ) {
        if (Gate::denies("need-edit", $need)) {
            abort(404);
        }

        $this->_needRepository->update($need->id, [
            ...$request->validated(),
            "amount" => RealToFloatParser::parse($request->input("amount"))
        ]);

        if (intval($request->completed)) {
            $leave = $leaveRepository->create(auth()->id(), [
                "leaveable_type" => Need::class,
                "leaveable_id" => $need->id
            ]);
            $movementRepository->create(auth()->id(), [
                "movementable_type" => Leave::class,
                "movementable_id" => $leave->id
            ]);
        } else {
            if ($need->leave) {
                if ($need->leave->movement) {
                    $movementRepository->forceDelete($need->leave->movement->id);
                }
                $leaveRepository->forceDelete($need->leave->id);
            }
        }

        return redirect()->route("needs.edit", $need)->with(
            Alert::success("Registro atualizado com sucesso.")
        );
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(
        MovementRepositoryContract $movementRepository,
        LeaveRepositoryContract $leaveRepository,
        Need $need
    ) {
        if (Gate::denies("need-edit", $need)) {
            abort(404);
        }

        if ($need->leave) {
            if ($need->leave->movement) {
                $movementRepository->forceDelete($need->leave->movement->id);
            }
            $leaveRepository->forceDelete($need->leave->id);
        }
        $need->delete();
        return redirect()->route("needs.index")->with(
            Alert::success("Registro removido com sucesso")
        );
    }
}
