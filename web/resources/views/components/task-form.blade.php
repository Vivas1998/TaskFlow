@props(['action', 'method' => 'POST', 'project', 'task' => null, 'statuses', 'priorities', 'recurrences'])

@php
    $selectedAssignees = old('assignee_ids', $task?->assignees->pluck('id')->all() ?? [auth()->id()]);
    $subtaskRows = old('subtasks', $task?->subtasks->map(fn ($subtask) => [
        'id' => $subtask->id,
        'title' => $subtask->title,
        'completed' => $subtask->is_completed,
    ])->all() ?? []);

    if (count($subtaskRows) === 0) {
        $subtaskRows = [['id' => null, 'title' => '', 'completed' => false]];
    }
@endphp

<form class="form" action="{{ $action }}" method="post">
    @csrf
    @if ($method !== 'POST') @method($method) @endif

    <div class="form-field">
        <label class="form-field__label" for="name">Nombre de la tarea</label>
        <input class="form-field__input" id="name" name="name" type="text" value="{{ old('name', $task?->name) }}" maxlength="160" required autofocus>
        @error('name')<p class="form-field__error">{{ $message }}</p>@enderror
    </div>

    <div class="form-field">
        <label class="form-field__label" for="description">Descripción</label>
        <textarea class="form-field__input form-field__input--textarea" id="description" name="description" maxlength="5000" required>{{ old('description', $task?->description) }}</textarea>
        @error('description')<p class="form-field__error">{{ $message }}</p>@enderror
    </div>

    <fieldset class="form-field subtask-editor" data-subtask-editor>
        <div class="subtask-editor__heading">
            <div>
                <legend class="form-field__label">Subtareas <span class="form-field__optional">Opcional</span></legend>
                <p class="form-field__help">Divide el trabajo en pasos sencillos que solo pueden estar pendientes o completados.</p>
            </div>
            <button class="button button--secondary subtask-editor__add" type="button" data-subtask-add>+ Añadir subtarea</button>
        </div>
        <div class="subtask-editor__list" data-subtask-list>
            @foreach ($subtaskRows as $index => $subtask)
                <div class="subtask-editor__row" data-subtask-row>
                    @if (! empty($subtask['id']))<input name="subtasks[{{ $index }}][id]" type="hidden" value="{{ $subtask['id'] }}">@endif
                    <input name="subtasks[{{ $index }}][completed]" type="hidden" value="0">
                    <label class="subtask-editor__check" title="Subtarea completada">
                        <input name="subtasks[{{ $index }}][completed]" type="checkbox" value="1" @checked((bool) ($subtask['completed'] ?? false))>
                        <span aria-hidden="true">✓</span>
                    </label>
                    <input class="form-field__input" name="subtasks[{{ $index }}][title]" type="text" value="{{ $subtask['title'] ?? '' }}" maxlength="255" placeholder="Por ejemplo: añadir CSS" aria-label="Nombre de la subtarea {{ $index + 1 }}">
                    <button class="icon-button" type="button" data-subtask-remove aria-label="Quitar subtarea" title="Quitar subtarea">×</button>
                </div>
            @endforeach
        </div>
        <template data-subtask-template>
            <div class="subtask-editor__row" data-subtask-row>
                <input name="subtasks[__INDEX__][completed]" type="hidden" value="0">
                <label class="subtask-editor__check" title="Subtarea completada">
                    <input name="subtasks[__INDEX__][completed]" type="checkbox" value="1">
                    <span aria-hidden="true">✓</span>
                </label>
                <input class="form-field__input" name="subtasks[__INDEX__][title]" type="text" maxlength="255" placeholder="Nueva subtarea" aria-label="Nombre de la nueva subtarea">
                <button class="icon-button" type="button" data-subtask-remove aria-label="Quitar subtarea" title="Quitar subtarea">×</button>
            </div>
        </template>
        @error('subtasks')<p class="form-field__error">{{ $message }}</p>@enderror
        @error('subtasks.*.title')<p class="form-field__error">{{ $message }}</p>@enderror
    </fieldset>

    <div class="form__columns">
        <div class="form-field">
            <label class="form-field__label" for="status">Estado</label>
            <select class="form-field__input" id="status" name="status" required>
                @foreach ($statuses as $status)<option value="{{ $status->value }}" @selected(old('status', $task?->status?->value ?? 'not_started') === $status->value)>{{ $status->label() }}</option>@endforeach
            </select>
        </div>
        <div class="form-field">
            <label class="form-field__label" for="priority">Prioridad <span class="form-field__optional">Opcional</span></label>
            <select class="form-field__input" id="priority" name="priority">
                <option value="">Sin prioridad</option>
                @foreach ($priorities as $priority)<option value="{{ $priority->value }}" @selected(old('priority', $task?->priority?->value) === $priority->value)>{{ $priority->label() }}</option>@endforeach
            </select>
        </div>
    </div>

    <div class="form__columns">
        <div class="form-field">
            <label class="form-field__label" for="due_at">Fecha límite <span class="form-field__optional">Opcional</span></label>
            <input class="form-field__input" id="due_at" name="due_at" type="datetime-local" value="{{ old('due_at', $task?->due_at?->format('Y-m-d\TH:i')) }}">
            @error('due_at')<p class="form-field__error">{{ $message }}</p>@enderror
        </div>
        <label class="form-check form-check--boxed"><input name="show_in_calendar" type="checkbox" value="1" @checked(old('show_in_calendar', $task?->show_in_calendar))><span><strong>Mostrar en calendario</strong><small>Requiere fecha límite.</small></span></label>
    </div>

    <fieldset class="form-field">
        <legend class="form-field__label">Personas asignadas</legend>
        <div class="assignee-grid">
            @foreach ($project->members as $member)
                <label class="assignee-option"><input name="assignee_ids[]" type="checkbox" value="{{ $member->id }}" @checked(in_array($member->id, $selectedAssignees))><span class="avatar" aria-hidden="true">{{ $member->initials }}</span><span><strong>{{ $member->full_name }}</strong><small>{{ $member->email }}</small></span></label>
            @endforeach
        </div>
        @error('assignee_ids')<p class="form-field__error">{{ $message }}</p>@enderror
    </fieldset>

    <div class="form__columns">
        <div class="form-field">
            <label class="form-field__label" for="recurrence">Repetición <span class="form-field__optional">Opcional</span></label>
            <select class="form-field__input" id="recurrence" name="recurrence">
                <option value="">No se repite</option>
                @foreach ($recurrences as $recurrence)<option value="{{ $recurrence->value }}" @selected(old('recurrence', $task?->recurrence?->value) === $recurrence->value)>{{ $recurrence->label() }}</option>@endforeach
            </select>
        </div>
        <div class="form-field">
            <label class="form-field__label" for="recurrence_ends_at">Final de repetición <span class="form-field__optional">Opcional</span></label>
            <input class="form-field__input" id="recurrence_ends_at" name="recurrence_ends_at" type="date" value="{{ old('recurrence_ends_at', $task?->recurrence_ends_at?->format('Y-m-d')) }}">
            @error('recurrence_ends_at')<p class="form-field__error">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="form__actions"><a class="button button--secondary" href="{{ route('projects.show', $project) }}">Cancelar</a><button class="button button--primary" type="submit">{{ $task ? 'Guardar cambios' : 'Crear tarea' }}</button></div>
</form>
