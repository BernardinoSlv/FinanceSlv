<?php

namespace App\Models;

use App\Support\Models\Traits\HasMovements;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'project_id',
        "name",
        "complete",
        "description",
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, "project_id", "id")->withTrashed();
    }
}
