<?php

namespace App\Http\Controllers;

use App\Support\Message;
use App\Models\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpKernel\Exception\HttpException;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = auth()->user()->projects()
            ->withCount([
                "projectItems",
                "projectItems as completed_project_items_count" => fn(Builder $query) => $query
                    ->where("complete", 1)
            ])
            ->paginate();

        return view("projects.index", compact("projects"));
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
    public function store(Request $request): JsonResponse
    {
        $attributes =  $request->validate([
            "name" => [
                "required",
                "max:255",
                Rule::unique("projects", "name")->where("user_id", auth()->id())
            ],
            "description" => ["nullable"]
        ]);
        $project = auth()->user()->projects()->create($attributes);
        Message::flashSuccess("Projeto criado com sucesso.");

        return response()->json([
            "message" => "Projeto criado com sucesso."
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Project $project)
    {
        if (Gate::denies("is-owner", $project))
            throw new HttpException(403, "Não autorizado.");

        $attributes =  $request->validate([
            "name" => [
                "required",
                "max:255",
                Rule::unique("projects", "name")->where("user_id", auth()->id())
                    ->ignore($project->id)
            ],
            "description" => ["nullable"]
        ]);

        $project->fill($attributes);
        $project->save();
        Message::flashSuccess("Projeto atualizado.");

        return response()->json([
            "message" => "Projeto atualizado."
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        if (Gate::denies("is-owner", $project))
            abort(403);
        $project->delete();

        return redirect()->route("projects.index")->with(
            Message::success("Projeto deletado.")
        );
    }
}
