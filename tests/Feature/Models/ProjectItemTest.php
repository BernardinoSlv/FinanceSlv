<?php

namespace Tests\Feature\Models;

use App\Models\Debt;
use App\Models\Identifier;
use App\Models\Project;
use App\Models\ProjectItem;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProjectItemTest extends TestCase
{
    use RefreshDatabase;

    /** deve retornar Project */
    public function test_project_relation(): void
    {
        Project::factory(2)->create();

        $project = Project::factory()->create();
        $projectItem = ProjectItem::factory()->create(["project_id" => $project]);

        $this->assertEquals($project->id, $projectItem->project->id);
    }

    /** deve retornar Project mesmo deletado */
    public function test_project_relation_trashed_project(): void
    {
        Project::factory(2)->create();

        $project = Project::factory()->trashed()->create();
        $projectItem = ProjectItem::factory()->create(["project_id" => $project]);

        $this->assertEquals($project->id, $projectItem->project->id);
    }
}
