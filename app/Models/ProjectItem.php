<?php

namespace App\Models;

use App\Support\Models\Traits\HasMovements;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectItem extends Model
{
    use HasFactory, HasMovements;

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, "project_id", "id")->withTrashed();
    }

    public function debt(): BelongsTo
    {
        return $this->belongsTo(Debt::class, "debt_id", "id")->withTrashed();
    }
}
