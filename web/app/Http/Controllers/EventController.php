<?php

namespace App\Http\Controllers;

use App\Enums\EventColor;
use App\Models\Event;
use App\Models\Project;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class EventController extends Controller
{
    public function create(Request $request, Project $project): View
    {
        $this->authorize('createEvent', $project);

        return view('events.create', [
            'project' => $project,
            'projects' => $request->user()->projects()->orderBy('name')->get(),
            'selectedDate' => $request->date('date')?->format('Y-m-d') ?? now()->format('Y-m-d'),
            'colors' => EventColor::cases(),
        ]);
    }

    public function store(Request $request, Project $project): RedirectResponse
    {
        $this->authorize('createEvent', $project);
        $validated = $this->validatedEvent($request);

        $project->events()->create([
            ...$validated,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('calendar.index', $project)->with('status', 'Evento creado.');
    }

    public function edit(Request $request, Project $project, Event $event): View
    {
        $this->ensureEventBelongsToProject($event, $project);
        $this->authorize('update', $event);

        return view('events.edit', [
            'event' => $event,
            'project' => $project,
            'projects' => $request->user()->projects()->orderBy('name')->get(),
            'colors' => EventColor::cases(),
        ]);
    }

    public function update(Request $request, Project $project, Event $event): RedirectResponse
    {
        $this->ensureEventBelongsToProject($event, $project);
        $this->authorize('update', $event);
        $event->update($this->validatedEvent($request));

        return redirect()->route('calendar.index', $project)->with('status', 'Evento actualizado.');
    }

    public function destroy(Request $request, Project $project, Event $event): RedirectResponse
    {
        $this->ensureEventBelongsToProject($event, $project);
        $this->authorize('delete', $event);
        $event->update(['deleted_by' => $request->user()->id]);
        $event->delete();

        return redirect()->route('calendar.index', $project)->with('status', 'Evento enviado a la papelera.');
    }

    public function trash(Request $request, Project $project): View
    {
        $this->authorize('view', $project);

        return view('events.trash', [
            'project' => $project,
            'projects' => $request->user()->projects()->orderBy('name')->get(),
            'events' => $project->events()->onlyTrashed()->latest('deleted_at')->get(),
        ]);
    }

    public function restore(Project $project, int $event): RedirectResponse
    {
        $eventModel = Event::onlyTrashed()->findOrFail($event);
        $this->ensureEventBelongsToProject($eventModel, $project);
        $this->authorize('restore', $eventModel);
        $eventModel->restore();
        $eventModel->update(['deleted_by' => null]);

        return back()->with('status', 'Evento restaurado.');
    }

    /** @return array<string, mixed> */
    private function validatedEvent(Request $request): array
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'description' => ['nullable', 'string', 'max:5000'],
            'start_date' => ['required', 'date'],
            'start_time' => ['nullable', 'date_format:H:i'],
            'end_date' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date_format:H:i'],
            'all_day' => ['nullable', 'boolean'],
            'color' => ['nullable', Rule::enum(EventColor::class)],
        ]);

        $allDay = $request->boolean('all_day');

        if (! $allDay && empty($validated['start_time'])) {
            throw ValidationException::withMessages(['start_time' => 'Indica una hora o marca el evento como día completo.']);
        }

        if (! empty($validated['end_time']) && empty($validated['end_date'])) {
            throw ValidationException::withMessages(['end_date' => 'Indica la fecha de finalización.']);
        }

        $startsAt = Carbon::parse($validated['start_date'].' '.($allDay ? '00:00' : $validated['start_time']));
        $endsAt = null;

        if (! empty($validated['end_date'])) {
            $endsAt = Carbon::parse($validated['end_date'].' '.($allDay ? '00:00' : ($validated['end_time'] ?? $validated['start_time'])));

            if ($endsAt->lt($startsAt)) {
                throw ValidationException::withMessages(['end_date' => 'El final no puede ser anterior al inicio.']);
            }
        }

        return [
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'all_day' => $allDay,
            'color' => $validated['color'] ?? EventColor::Blue->value,
        ];
    }

    private function ensureEventBelongsToProject(Event $event, Project $project): void
    {
        abort_unless($event->project_id === $project->id, 404);
    }
}
