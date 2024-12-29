<?php

namespace App\Http\Controllers;

use App\Support\Message;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Rules\Amount;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redis;
use Illuminate\Validation\Rule;
use Src\Parsers\RealToFloatParser;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProjectItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Project $project)
    {
        if (Gate::denies("is-owner", $project))
            abort(403);

        $projectItems = $project->projectItems()
            ->paginate();

        return view("projects.items.index", compact("project", "projectItems"));
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
    public function store(Request $request, Project $project): JsonResponse
    {
        if (Gate::denies("is-owner", $project))
            throw new HttpException(403, "Não autorizado.");

        $attributes = $request->validate([
            "name" => [
                "required",
                "max:255",
                Rule::unique("project_items", "name")->where("project_id", $project->id)
            ],
            "description" => ["nullable"]
        ]);

        $projectItem = $project->projectItems()->create([
            ...$attributes,
            "complete" => 0
        ]);
        Message::flashSuccess("Item criado.");

        return response()->json([
            "message" => "Item criado."
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(ProjectItem $projectItem)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ProjectItem $projectItem)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project, ProjectItem $projectItem)
    {
        if (Gate::denies("is-owner", $project))
            throw new HttpException(403, "Não autorizado.");

        $attributes = $request->validate([
            "name" => [
                "required",
                "max:255",
                Rule::unique("project_items", "name")->where("project_id", $project->id)
                    ->ignore($projectItem->id)
            ],
            "description" => ["nullable"],
            "complete" => ["nullable", "in:on,1"]
        ]);

        $projectItem->fill([
            ...$attributes,
            "complete" => (int) boolval($request->complete)
        ]);
        $projectItem->save();

        Message::flashSuccess("Item atualizado.");

        return response()->json([
            "message" => "Item atualizado."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project, ProjectItem $projectItem)
    {
        if (Gate::denies("is-owner", $project))
            abort(403);
        $projectItem->forceDelete();

        return redirect()->route("projects.items.index", $project)
            ->with(Message::success("Item removido."));
    }
}
