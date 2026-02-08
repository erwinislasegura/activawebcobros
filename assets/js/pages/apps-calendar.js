/**
 * Template Name: UBold - Admin & Dashboard Template
 * By (Author): Coderthemes
 * Module/App (File Name): Apps Calendar
 */

class CalendarSchedule {

    constructor() {
        this.isReadOnly = window.calendarReadOnly === true;
        this.body = document.body;
        this.modal = null;
        const modalElement = document.getElementById('event-modal');
        if (modalElement && !this.isReadOnly) {
            this.modal = new bootstrap.Modal(modalElement, {backdrop: 'static'});
        }
        this.calendar = document.getElementById('calendar');
        this.formEvent = document.getElementById('forms-event');
        this.btnNewEvent = document.querySelectorAll('.btn-new-event');
        this.btnDeleteEvent = document.getElementById('btn-delete-event');
        this.btnSaveEvent = document.getElementById('btn-save-event');
        this.modalTitle = document.getElementById('modal-title');
        this.calendarObj = null;
        this.selectedEvent = null;
        this.newEventData = null;
    }

    onEventClick(info) {
        if (this.isReadOnly) {
            return;
        }
        this.formEvent?.reset();
        this.formEvent.classList.remove('was-validated');
        this.newEventData = null;
        if (this.btnDeleteEvent) {
            this.btnDeleteEvent.style.display = "block";
        }
        if (this.modalTitle) {
            this.modalTitle.text = ('Editar evento');
        }
        this.modal?.show();
        this.selectedEvent = info.event;
        const titleInput = document.getElementById('event-title');
        if (titleInput) {
            titleInput.value = this.selectedEvent.title;
        }
        const categoryInput = document.getElementById('event-category');
        if (categoryInput) {
            if (this.formEvent?.dataset.submit === 'server') {
                categoryInput.value = this.selectedEvent.extendedProps?.tipo || categoryInput.value;
            } else {
                const {classNames} = this.selectedEvent;
                categoryInput.value = Array.isArray(classNames) ? classNames.join(' ') : classNames || '';
            }
        }
        const startInput = document.getElementById('event-start');
        if (startInput && this.selectedEvent.start) {
            startInput.value = this.formatDateTime(this.selectedEvent.start);
        }
        const endInput = document.getElementById('event-end');
        if (endInput && this.selectedEvent.end) {
            endInput.value = this.formatDateTime(this.selectedEvent.end);
        }
        const descriptionInput = document.getElementById('event-description');
        if (descriptionInput) {
            descriptionInput.value = this.selectedEvent.extendedProps?.descripcion || '';
        }
        const locationInput = document.getElementById('event-location');
        if (locationInput) {
            locationInput.value = this.selectedEvent.extendedProps?.ubicacion || '';
        }
        const statusInput = document.getElementById('event-estado');
        if (statusInput) {
            statusInput.value = this.selectedEvent.extendedProps?.estado || statusInput.value;
        }
        const cuposInput = document.getElementById('event-cupos');
        if (cuposInput) {
            cuposInput.value = this.selectedEvent.extendedProps?.cupos ?? '';
        }
        const publicoInput = document.getElementById('event-publico');
        if (publicoInput) {
            publicoInput.value = this.selectedEvent.extendedProps?.publico_objetivo || '';
        }
        const creadorInput = document.getElementById('event-creador');
        if (creadorInput) {
            creadorInput.value = this.selectedEvent.extendedProps?.creado_por || creadorInput.value;
        }
        const encargadoInput = document.getElementById('event-encargado');
        if (encargadoInput) {
            const encargadoValue = this.selectedEvent.extendedProps?.encargado_id;
            encargadoInput.value = encargadoValue ? encargadoValue : '';
        }
        const eventIdInput = document.getElementById('event-id');
        if (eventIdInput) {
            eventIdInput.value = this.selectedEvent.id || '';
        }
        const actionInput = document.getElementById('event-action');
        if (actionInput) {
            actionInput.value = 'save';
        }
    }

    onSelect(info) {
        if (this.isReadOnly) {
            return;
        }
        this.formEvent?.reset();
        this.formEvent?.classList.remove('was-validated');
        this.selectedEvent = null;
        this.newEventData = info;
        if (this.btnDeleteEvent) {
            this.btnDeleteEvent.style.display = "none";
        }
        if (this.modalTitle) {
            this.modalTitle.text = ('Crear evento');
        }
        this.modal?.show();
        this.calendarObj.unselect();
        const startInput = document.getElementById('event-start');
        if (startInput && info?.date) {
            startInput.value = this.formatDateTime(info.date);
        }
        const endInput = document.getElementById('event-end');
        if (endInput && info?.date) {
            endInput.value = this.formatDateTime(info.date);
        }
        const eventIdInput = document.getElementById('event-id');
        if (eventIdInput) {
            eventIdInput.value = '';
        }
        const actionInput = document.getElementById('event-action');
        if (actionInput) {
            actionInput.value = 'save';
        }
    }

    formatDateTime(date) {
        const localDate = new Date(date);
        const year = localDate.getFullYear();
        const month = String(localDate.getMonth() + 1).padStart(2, '0');
        const day = String(localDate.getDate()).padStart(2, '0');
        const hours = String(localDate.getHours()).padStart(2, '0');
        const minutes = String(localDate.getMinutes()).padStart(2, '0');
        return `${year}-${month}-${day}T${hours}:${minutes}`;
    }

    init() {
        /*  Initialize the calendar  */
        const today = new Date();
        const self = this;
        const externalEventContainerEl = document.getElementById('external-events');

        if (externalEventContainerEl && !this.isReadOnly) {
            new FullCalendar.Draggable(externalEventContainerEl, {
            itemSelector: '.external-event',
            eventData: function (eventEl) {
                return {
                    title: eventEl.innerText,
                    classNames: eventEl.getAttribute('data-class'),
                    extendedProps: {
                        tipo: eventEl.getAttribute('data-tipo')
                    }
                };
            }
        });
        }

        const defaultEvents = Array.isArray(window.calendarEvents)
            ? window.calendarEvents
            : [];

        // cal - init
        self.calendarObj = new FullCalendar.Calendar(self.calendar, {

            plugins: [],
            slotDuration: '00:30:00', /* If we want to split day time each 15minutes */
            slotMinTime: '07:00:00',
            slotMaxTime: '19:00:00',
            themeSystem: 'bootstrap',
            bootstrapFontAwesome: false,
            locale: window.calendarLocale || 'es',
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista',
                prev: 'Anterior',
                next: 'Siguiente'
            },
            initialView: 'dayGridMonth',
            handleWindowResize: true,
            height: window.innerHeight - 240,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            initialEvents: defaultEvents,
            editable: !self.isReadOnly,
            droppable: !self.isReadOnly, // this allows things to be dropped onto the calendar !!!
            // dayMaxEventRows: false, // allow "more" link when too many events
            selectable: !self.isReadOnly,
            dateClick: self.isReadOnly ? null : function (info) {
                self.onSelect(info);
            },
            eventClick: self.isReadOnly ? null : function (info) {
                self.onEventClick(info);
            }
        });

        self.calendarObj.render();

        // on new event button click
        if (!self.isReadOnly) {
            self.btnNewEvent.forEach(function (btn) {
                btn.addEventListener('click', function (e) {
                    self.onSelect({
                        date: new Date(),
                        allDay: true
                    });
                });
            });
        }

        // save event
        self.formEvent?.addEventListener('submit', function (e) {
            if (self.isReadOnly) {
                return;
            }
            if (self.formEvent?.dataset.submit === 'server') {
                return;
            }
            e.preventDefault();
            const form = self.formEvent;

            // validation
            if (form.checkValidity()) {
                if (self.selectedEvent) {
                    self.selectedEvent.setProp('title', document.getElementById('event-title').value);
                    self.selectedEvent.setProp('classNames', document.getElementById('event-category').value)

                } else {
                    const eventData = {
                        title: document.getElementById('event-title').value,
                        start: self.newEventData.date,
                        allDay: self.newEventData.allDay,
                        className: document.getElementById('event-category').value
                    };
                    self.calendarObj.addEvent(eventData);
                }
                self.modal.hide();
            } else {
                e.stopPropagation();
                form.classList.add('was-validated');
            }
        });

        // delete event
        if (self.btnDeleteEvent) {
        self.btnDeleteEvent.addEventListener('click', function (e) {
            if (self.formEvent?.dataset.submit === 'server') {
                const actionInput = document.getElementById('event-action');
                if (actionInput) {
                    actionInput.value = 'delete';
                }
                self.formEvent?.submit();
                return;
            }

            if (self.selectedEvent) {
                self.selectedEvent.remove();
                self.selectedEvent = null;
                self.modal?.hide();
            }
        });
        }
    }

}

document.addEventListener('DOMContentLoaded', function (e) {
    new CalendarSchedule().init();
});
