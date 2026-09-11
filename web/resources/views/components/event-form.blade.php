@props(['action', 'method' => 'POST', 'project', 'colors', 'event' => null, 'selectedDate' => null])

@php
    $selectedColor = old('color', $event?->color?->value ?? \App\Enums\EventColor::Blue->value);
    $selectedColorOption = collect($colors)->first(fn ($color) => $color->value === $selectedColor) ?? \App\Enums\EventColor::Blue;
@endphp

<form class="form" action="{{ $action }}" method="post" data-event-form>
    @csrf
    @if ($method !== 'POST') @method($method) @endif
    <div class="form-field"><label class="form-field__label" for="title">Título del evento</label><input class="form-field__input" id="title" name="title" type="text" value="{{ old('title', $event?->title) }}" maxlength="160" required autofocus>@error('title')<p class="form-field__error">{{ $message }}</p>@enderror</div>
    <div class="form-field"><label class="form-field__label" for="description">Descripción <span class="form-field__optional">Opcional</span></label><textarea class="form-field__input form-field__input--textarea" id="description" name="description" maxlength="5000">{{ old('description', $event?->description) }}</textarea>@error('description')<p class="form-field__error">{{ $message }}</p>@enderror</div>
    <div class="form-field">
        <label class="form-field__label" for="color">Color en el calendario</label>
        <div class="event-color-picker">
            <span class="event-color-picker__preview" data-event-color-preview style="--event-color: {{ $selectedColorOption->hex() }}" aria-hidden="true"></span>
            <select class="form-field__input" id="color" name="color" data-event-color-select required>
                @foreach ($colors as $color)
                    <option value="{{ $color->value }}" data-color="{{ $color->hex() }}" @selected($selectedColor === $color->value)>{{ $color->label() }}</option>
                @endforeach
            </select>
        </div>
        <p class="form-field__help">Este color identifica el evento en todas las vistas del calendario.</p>
        @error('color')<p class="form-field__error">{{ $message }}</p>@enderror
    </div>
    <label class="form-check form-check--boxed"><input name="all_day" type="checkbox" value="1" @checked(old('all_day', $event?->all_day))><span><strong>Evento de día completo</strong><small>No necesita horas de inicio ni final.</small></span></label>
    <div class="form__columns">
        <div class="form-field"><label class="form-field__label" for="start_date">Fecha de inicio</label><input class="form-field__input" id="start_date" name="start_date" type="date" value="{{ old('start_date', $event?->starts_at?->format('Y-m-d') ?? $selectedDate) }}" required>@error('start_date')<p class="form-field__error">{{ $message }}</p>@enderror</div>
        <div class="form-field"><label class="form-field__label" for="start_time">Hora de inicio</label><input class="form-field__input" id="start_time" name="start_time" type="time" data-time-input value="{{ old('start_time', $event && ! $event->all_day ? $event->starts_at->format('H:i') : '') }}">@error('start_time')<p class="form-field__error">{{ $message }}</p>@enderror</div>
    </div>
    <div class="form__columns">
        <div class="form-field"><label class="form-field__label" for="end_date">Fecha final <span class="form-field__optional">Opcional</span></label><input class="form-field__input" id="end_date" name="end_date" type="date" value="{{ old('end_date', $event?->ends_at?->format('Y-m-d')) }}">@error('end_date')<p class="form-field__error">{{ $message }}</p>@enderror</div>
        <div class="form-field"><label class="form-field__label" for="end_time">Hora final <span class="form-field__optional">Opcional</span></label><input class="form-field__input" id="end_time" name="end_time" type="time" data-time-input value="{{ old('end_time', $event && ! $event->all_day ? $event->ends_at?->format('H:i') : '') }}">@error('end_time')<p class="form-field__error">{{ $message }}</p>@enderror</div>
    </div>
    <div class="form__actions"><a class="button button--secondary" href="{{ route('calendar.index', $project) }}">Cancelar</a><button class="button button--primary" type="submit">{{ $event ? 'Guardar cambios' : 'Crear evento' }}</button></div>
</form>
