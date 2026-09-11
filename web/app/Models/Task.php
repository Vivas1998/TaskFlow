<?php

namespace App\Models;

use App\Enums\RecurrenceType;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Support\ColorPalette;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'project_id',
        'created_by',
        'name',
        'description',
        'due_at',
        'show_in_calendar',
        'priority',
        'status',
        'recurrence',
        'recurrence_ends_at',
        'previous_occurrence_id',
        'deleted_by',
    ];

    protected function casts(): array
    {
        return [
            'due_at' => 'datetime',
            'show_in_calendar' => 'boolean',
            'priority' => TaskPriority::class,
            'status' => TaskStatus::class,
            'recurrence' => RecurrenceType::class,
            'recurrence_ends_at' => 'date',
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function assignmentColor(): string
    {
        return ColorPalette::forAssignees($this->loadedAssignees());
    }

    public function assignmentLabel(): string
    {
        $assignees = $this->loadedAssignees();

        return $assignees->count() === 1
            ? $assignees->first()->full_name
            : 'Compartida';
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(Subtask::class)->orderBy('sort_order')->orderBy('id');
    }

    public function previousOccurrence(): BelongsTo
    {
        return $this->belongsTo(self::class, 'previous_occurrence_id');
    }

    public function nextOccurrence(): HasOne
    {
        return $this->hasOne(self::class, 'previous_occurrence_id');
    }

    /** @return Collection<int, User> */
    private function loadedAssignees(): Collection
    {
        return $this->relationLoaded('assignees')
            ? $this->assignees
            : $this->assignees()->get();
    }
}
