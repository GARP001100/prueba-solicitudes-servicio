# Agendamiento odontológico

Este módulo expone un flujo simple para visualizar profesionales, consultar disponibilidad y agendar citas en la clínica.

## Rutas principales

- GET /doctors: directorio de profesionales activos.
- GET /doctors/{doctor}: detalle del profesional con calendario.
- GET /doctors/{doctor}/availability: eventos de disponibilidad para FullCalendar.
- GET /appointments: historial del paciente.
- POST /appointments: creación de solicitud de cita.
- PATCH /appointments/{appointment}/cancel: cancelación del usuario.
- GET /admin/appointments: tablero administrativo.
- PATCH /admin/appointments/{appointment}/approve: aprobación con rechazo automático de conflictos.
- PATCH /admin/appointments/{appointment}/reject: rechazo manual con motivo requerido.

## Reglas de negocio

- Solo se muestran doctores activos.
- La disponibilidad se basa en turnos semanales por día.
- La duración de la cita se calcula con la duración del servicio.
- Las citas `approved` bloquean nuevas solicitudes en el mismo rango.
- Las citas `pending` colisionantes se quedan pendientes hasta revisión.
- La aprobación administrativa rechaza automáticamente citas pendientes superpuestas del mismo profesional.

## Permisos

Los permisos agregados al seeder permanecen idempotentes y no eliminan los existentes:

- view appointments
- create appointments
- cancel own appointments
- view all appointments
- approve appointments
- manage doctors
- manage availability

## Notas de implementación

El módulo usa FullCalendar con importación dinámica del módulo de agendamiento para evitar cargar el calendario en cada vista innecesariamente.
