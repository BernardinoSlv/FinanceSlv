<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

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
    public function store(Request $request)
    {
        //
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
