import axios from 'axios';
import { Calendar } from '@fullcalendar/core';
import dayGridPlugin from '@fullcalendar/daygrid';
import interactionPlugin from '@fullcalendar/interaction';
import timeGridPlugin from '@fullcalendar/timegrid';
import Swal from 'sweetalert2';
import { Notyf } from 'notyf';
import TomSelect from 'tom-select';

const notyf = new Notyf({ duration: 3000, dismissible: true, position: { x: 'right', y: 'top' } });

function buildCalendarOptions() {
    return {
        plugins: [dayGridPlugin, timeGridPlugin, interactionPlugin],
        locale: 'es',
        initialView: 'timeGridWeek',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'timeGridWeek,timeGridDay',
        },
        selectable: true,
        selectMirror: true,
        nowIndicator: true,
        allDaySlot: false,
        slotDuration: '00:15:00',
        slotMinTime: '07:00:00',
        slotMaxTime: '19:00:00',
        scrollTime: '08:00:00',
        height: 'auto',
        stickyHeaderDates: true,
        expandRows: true,
        selectAllow: (info) => {
            const diffMinutes = (info.end.getTime() - info.start.getTime()) / (1000 * 60);
            return diffMinutes <= 60 && info.start >= new Date(new Date().setHours(0, 0, 0, 0));
        },
    };
}

function bindDirectorySearch() {
    const root = document.querySelector('[data-doctor-directory]');
    const input = document.getElementById('doctor-search');
    if (!root || !input) {
        return;
    }

    input.addEventListener('input', (event) => {
        const term = event.target.value.trim().toLowerCase();
        root.querySelectorAll('.doctor-card').forEach((card) => {
            const text = `${card.dataset.doctorName ?? ''} ${card.dataset.doctorSpecialty ?? ''}`;
            card.style.display = text.includes(term) ? '' : 'none';
        });
    });
}

function bindDoctorCalendar() {
    const root = document.querySelector('[data-doctor-calendar]');
    if (!root) {
        return;
    }

    const doctorId = root.dataset.doctorId;
    const calendarRoot = root.querySelector('[data-doctor-calendar]') ?? root;
    const calendar = new Calendar(calendarRoot, {
        ...buildCalendarOptions(),
        events: `/doctors/${doctorId}/availability`,
        eventClick: (info) => {
            const event = info.event;
            if (event?.extendedProps?.status === 'available') {
                const start = event.start;
                const end = event.end;
                const form = document.getElementById('appointment-drawer');
                const trigger = document.getElementById('appointment-starts_at');
                const inputDoctor = document.getElementById('doctor_profile_id');
                if (form && trigger && inputDoctor) {
                    trigger.value = start.toISOString();
                    inputDoctor.value = doctorId;
                    form.classList.remove('hidden');
                }
            }
        },
        select: (info) => {
            const form = document.getElementById('appointment-drawer');
            const trigger = document.getElementById('appointment-starts_at');
            const inputDoctor = document.getElementById('doctor_profile_id');
            if (form && trigger && inputDoctor) {
                trigger.value = info.start.toISOString();
                inputDoctor.value = doctorId;
                form.classList.remove('hidden');
            }
        },
    });
    calendar.render();

    const closeButton = document.querySelector('[data-close-drawer]');
    if (closeButton) {
        closeButton.addEventListener('click', () => {
            const form = document.getElementById('appointment-drawer');
            if (form) {
                form.classList.add('hidden');
            }
        });
    }

    const form = document.getElementById('appointment-form');
    if (form) {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const payload = Object.fromEntries(new FormData(form).entries());
            const csrf = document.querySelector('meta[name="csrf-token"]')?.content ?? '';

            try {
                const response = await axios.post('/appointments', payload, {
                    headers: {
                        'X-CSRF-TOKEN': csrf,
                        Accept: 'application/json',
                    },
                });

                notyf.success(response.data.message || 'Cita creada con éxito.');
                form.reset();
                document.getElementById('appointment-drawer')?.classList.add('hidden');
                calendar.refetchEvents();
            } catch (error) {
                const message = error.response?.data?.message || 'No se pudo guardar la cita.';
                notyf.error(message);
            }
        });
    }
}

function bindUserAppointments() {
    const root = document.querySelector('[data-user-appointments]');
    const calendarRoot = document.querySelector('[data-appointment-calendar]');
    if (!root || !calendarRoot) {
        return;
    }

    const calendar = new Calendar(calendarRoot, {
        ...buildCalendarOptions(),
        events: '/appointments/events',
        eventClick: async (info) => {
            const appointmentId = info.event.id;
            const { data } = await axios.get(`/appointments/${appointmentId}`);
            const appointment = data.appointment;
            await Swal.fire({
                title: 'Detalle de la cita',
                html: `
                    <div class='text-left'>
                        <p><strong>Profesional:</strong> ${appointment.doctor_profile?.full_name ?? '—'}</p>
                        <p><strong>Servicio:</strong> ${appointment.institutional_service?.name ?? '—'}</p>
                        <p><strong>Inicio:</strong> ${appointment.starts_at ?? '—'}</p>
                        <p><strong>Fin:</strong> ${appointment.ends_at ?? '—'}</p>
                        <p><strong>Estado:</strong> ${appointment.status ?? '—'}</p>
                    </div>
                `,
                icon: 'info',
                confirmButtonText: 'Cerrar',
            });
        },
    });

    calendar.render();
}

function bindAdminAppointments() {
    const root = document.querySelector('[data-admin-appointments]');
    const calendarRoot = document.querySelector('[data-admin-appointment-calendar]');
    if (!root || !calendarRoot) {
        return;
    }

    const calendar = new Calendar(calendarRoot, {
        ...buildCalendarOptions(),
        events: '/admin/appointments/events',
        eventClick: async (info) => {
            const appointmentId = info.event.id;
            const { data } = await axios.get(`/admin/appointments/${appointmentId}`);
            const appointment = data.appointment;
            const html = `
                <div class='text-left space-y-1'>
                    <p><strong>Paciente:</strong> ${appointment.user?.name ?? '—'}</p>
                    <p><strong>Profesional:</strong> ${appointment.doctor_profile?.full_name ?? '—'}</p>
                    <p><strong>Servicio:</strong> ${appointment.institutional_service?.name ?? '—'}</p>
                    <p><strong>Estado:</strong> ${appointment.status ?? '—'}</p>
                    <p><strong>Inicio:</strong> ${appointment.starts_at ?? '—'}</p>
                    <p><strong>Fin:</strong> ${appointment.ends_at ?? '—'}</p>
                </div>
            `;

            const result = await Swal.fire({
                title: 'Solicitud',
                html,
                showCancelButton: true,
                confirmButtonText: 'Aprobar',
                cancelButtonText: 'Rechazar',
                showDenyButton: true,
                denyButtonText: 'Cerrar',
            });

            if (result.isConfirmed) {
                try {
                    const response = await axios.patch(`/admin/appointments/${appointmentId}/approve`);
                    notyf.success(response.data.message || 'Solicitud aprobada.');
                    calendar.refetchEvents();
                } catch (error) {
                    notyf.error(error.response?.data?.message || 'No fue posible aprobar.');
                }
            }

            if (result.isDismissed && result.dismiss === Swal.DismissReason.cancel) {
                const { value: reason } = await Swal.fire({
                    title: 'Motivo del rechazo',
                    input: 'textarea',
                    inputPlaceholder: 'Escribe el motivo',
                    inputValidator: (value) => !value || value.trim().length < 5 ? 'El motivo debe tener al menos 5 caracteres.' : null,
                    showCancelButton: true,
                    confirmButtonText: 'Rechazar',
                });

                if (reason) {
                    try {
                        const response = await axios.patch(`/admin/appointments/${appointmentId}/reject`, { reason });
                        notyf.success(response.data.message || 'Solicitud rechazada.');
                        calendar.refetchEvents();
                    } catch (error) {
                        notyf.error(error.response?.data?.message || 'No fue posible rechazar.');
                    }
                }
            }
        },
    });

    calendar.render();
}

function initializeDoctorScheduling(pageElement) {
    if (!pageElement) {
        return;
    }

    bindDirectorySearch();
    bindDoctorCalendar();
    bindUserAppointments();
    bindAdminAppointments();

    document.querySelectorAll('[data-tom-select]').forEach((element) => {
        if (!element.tomselect) {
            new TomSelect(element, { create: false, allowEmptyOption: true });
        }
    });
}

export { initializeDoctorScheduling };
