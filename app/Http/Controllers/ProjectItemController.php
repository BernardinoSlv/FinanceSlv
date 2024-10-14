<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Models\Project;
use App\Models\ProjectItem;
use App\Rules\Amount;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        $projectItems = $project->items()->with("identifier")->paginate();
        $identifiers = auth()->user()->identifiers;

        return view("projects.items.index", compact("project", "projectItems", "identifiers"));
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
            "identifier_id" => [
                "nullable",
                Rule::exists("identifiers", "id")->where("user_id", auth()->id())
            ],
            "amount" => ["nullable", new Amount],
            "description" => ["nullable"]
        ]);

        $projectItem = $project->items()->create([
            ...$attributes,
            "amount" => RealToFloatParser::parse($request->amount),
            "complete" => 0
        ]);
        Alert::flashSuccess("Item criado.");

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
    public function update(Request $request, ProjectItem $projectItem)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProjectItem $projectItem)
    {
        //
    }
}
