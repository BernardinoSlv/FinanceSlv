<?php

namespace App\Http\Controllers;

use App\Helpers\Alert;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = auth()->user()->projects()->paginate();

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
    public function store(Request $request)
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
        Alert::flashSuccess("Projeto criado com sucesso.");

        return response()->json([
            "message" => "Projeto criado com sucesso."
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project)
    {
        //
    }

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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project)
    {
        //
    }
}
