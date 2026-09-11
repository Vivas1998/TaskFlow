import './bootstrap';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import esLocale from '@fullcalendar/core/locales/es';

const navigationToggle = document.querySelector('[data-navigation-toggle]');
const navigation = document.querySelector('[data-navigation]');

if (navigationToggle && navigation) {
    navigationToggle.addEventListener('click', () => {
        const isOpen = navigation.dataset.open === 'true';

        navigation.dataset.open = String(!isOpen);
        navigationToggle.setAttribute('aria-expanded', String(!isOpen));
    });
}

const initialiseCalendar = () => {
    const calendarElement = document.querySelector('[data-calendar]');

    if (!calendarElement) {
        return;
    }

    const statusElement = document.querySelector('[data-calendar-status]');

    try {
        const calendar = new Calendar(calendarElement, {
            plugins: [dayGridPlugin, interactionPlugin],
            locale: esLocale,
            firstDay: 1,
            initialView: 'dayGridMonth',
            height: 'auto',
            fixedWeekCount: false,
            dayMaxEvents: true,
            events: calendarElement.dataset.feedUrl,
            loading: (isLoading) => {
                if (statusElement) {
                    statusElement.hidden = !isLoading;
                    statusElement.textContent = 'Cargando calendario…';
                }
            },
            eventSourceFailure: () => {
                if (statusElement) {
                    statusElement.hidden = false;
                    statusElement.textContent = 'No se pudieron cargar los eventos. Actualiza la página para intentarlo de nuevo.';
                    statusElement.classList.add('calendar-status--error');
                }
            },
            dateClick: ({ dateStr }) => {
                if (calendarElement.dataset.createEventUrl) {
                    window.location.href = `${calendarElement.dataset.createEventUrl}?date=${encodeURIComponent(dateStr)}`;
                }
            },
            eventClick: ({ event }) => {
                if (event.extendedProps.editUrl) {
                    window.location.href = event.extendedProps.editUrl;
                }
            },
            eventDidMount: ({ event, el }) => {
                const type = event.extendedProps.kind === 'task' ? 'Tarea' : 'Evento';
                el.title = `${type}: ${event.title}. ${event.extendedProps.assignmentLabel}`;
            },
        });

        calendar.render();
    } catch (error) {
        console.error('No se pudo iniciar el calendario.', error);

        if (statusElement) {
            statusElement.hidden = false;
            statusElement.textContent = 'No se pudo iniciar el calendario. Actualiza la página para intentarlo de nuevo.';
            statusElement.classList.add('calendar-status--error');
        }
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initialiseCalendar, { once: true });
} else {
    initialiseCalendar();
}

const eventForm = document.querySelector('[data-event-form]');

if (eventForm) {
    const allDay = eventForm.querySelector('[name="all_day"]');
    const timeInputs = eventForm.querySelectorAll('[data-time-input]');
    const colorSelect = eventForm.querySelector('[data-event-color-select]');
    const colorPreview = eventForm.querySelector('[data-event-color-preview]');

    const syncTimeInputs = () => {
        timeInputs.forEach((input) => {
            input.disabled = allDay.checked;
        });
    };

    allDay.addEventListener('change', syncTimeInputs);
    syncTimeInputs();

    const syncColorPreview = () => {
        const selectedOption = colorSelect?.options[colorSelect.selectedIndex];

        if (selectedOption && colorPreview) {
            colorPreview.style.setProperty('--event-color', selectedOption.dataset.color);
        }
    };

    colorSelect?.addEventListener('change', syncColorPreview);
    syncColorPreview();
}

document.querySelectorAll('[data-subtask-editor]').forEach((editor) => {
    const list = editor.querySelector('[data-subtask-list]');
    const template = editor.querySelector('[data-subtask-template]');
    const addButton = editor.querySelector('[data-subtask-add]');
    let nextIndex = list.querySelectorAll('[data-subtask-row]').length;

    addButton.addEventListener('click', () => {
        const fragment = template.content.cloneNode(true);
        fragment.querySelectorAll('[name]').forEach((input) => {
            input.name = input.name.replace('__INDEX__', String(nextIndex));
        });
        nextIndex += 1;
        list.append(fragment);
        list.querySelector('[data-subtask-row]:last-child .form-field__input').focus();
    });

    list.addEventListener('click', (event) => {
        const removeButton = event.target.closest('[data-subtask-remove]');

        if (removeButton) {
            removeButton.closest('[data-subtask-row]').remove();
        }
    });
});
