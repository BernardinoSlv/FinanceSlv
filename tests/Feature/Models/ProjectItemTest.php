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

    /** deve retornar null */
    public function test_debt_relation_missing_debt(): void
    {
        Debt::factory(2)->create();

        $projectItem = ProjectItem::factory()->create();

        $this->assertNull($projectItem->debt);
    }

    /** deve retornar o Debt */
    public function test_debt_relation(): void
    {
        Debt::factory(2)->create();

        $debt = Debt::factory()->create();
        $projectItem = ProjectItem::factory()->create([
            "debt_id" => $debt
        ]);

        $this->assertEquals($debt->id, $projectItem->debt->id);
    }

    /** deve retornar o Debt */
    public function test_debt_relation_trashed_debt(): void
    {
        Debt::factory(2)->create();

        $debt = Debt::factory()->trashed()->create();
        $projectItem = ProjectItem::factory()->create([
            "debt_id" => $debt
        ]);

        $this->assertEquals($debt->id, $projectItem->debt->id);
    }

    /** deve retornar null */
    public function test_identifier_relation_missing_identifier(): void
    {
        Identifier::factory(2)->create();

        $projectItem = ProjectItem::factory()->create();

        $this->assertNull($projectItem->identifier);
    }

    /** deve retornar o Identifier */
    public function test_identifier_relation(): void
    {
        Identifier::factory(2)->create();

        $identifier = Identifier::factory()->create();
        $projectItem = ProjectItem::factory()->create([
            "identifier_id" => $identifier
        ]);

        $this->assertEquals($identifier->id, $projectItem->identifier->id);
    }

    /** deve retornar o Identifier */
    public function test_identifier_relation_trashed_identifier(): void
    {
        Identifier::factory(2)->create();

        $identifier = Identifier::factory()->trashed()->create();
        $projectItem = ProjectItem::factory()->create([
            "identifier_id" => $identifier
        ]);

        $this->assertEquals($identifier->id, $projectItem->identifier->id);
    }
}
