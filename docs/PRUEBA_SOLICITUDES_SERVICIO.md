# Sistema de solicitudes de servicio

## Objetivo

Módulo Laravel para registrar, listar, consultar y actualizar el estado de solicitudes de servicio.

## Arquitectura

- `ServiceRequest`: entidad, estados y tipos permitidos.
- `StoreServiceRequestRequest`: validación de creación.
- `UpdateServiceRequestStatusRequest`: validación exclusiva del estado.
- `ServiceRequestController`: listado, creación, detalle y cambio de estado.
- Vistas Blade tradicionales con CSRF, mensajes de error y escape automático.
- Pruebas Feature con base SQLite en memoria.

## Modelo de datos

La tabla `service_requests` contiene nombre, correo, tipo, descripción, estado y timestamps. Los estados se almacenan en inglés y se presentan en español.

## Rutas

- `GET /service-requests`
- `GET /service-requests/create`
- `POST /service-requests`
- `GET /service-requests/{serviceRequest}`
- `PATCH /service-requests/{serviceRequest}/status`

## Seguridad

- Eloquent parametriza las consultas.
- Form Requests validan tipos, longitudes, correo y estados.
- Los formularios incluyen CSRF.
- Blade escapa la salida con `{{ }}`.
- La creación fuerza el estado `new` en el servidor.
- El endpoint de estado actualiza únicamente `status`.
- La configuración de base se obtiene desde `.env`, que no debe versionarse.

## Instalación

```bash
php artisan migrate
php artisan optimize:clear
```

Opcional para datos de demostración:

```bash
php artisan db:seed --class=ServiceRequestSeeder
```

## Pruebas

```bash
vendor/bin/pint --test
php artisan test --filter=ServiceRequestTest
php artisan test
npm run build
```

## Decisiones

- No se creó catálogo en otra tabla porque los cuatro tipos son fijos en el alcance de una hora.
- No se implementó eliminación ni edición completa porque el enunciado solo exige cambiar el estado.
- Se usaron formularios Blade para reducir complejidad y facilitar explicación, validación y recuperación.
- Las rutas se protegen con autenticación existente, sin introducir permisos adicionales.

## Mejoras futuras

- Historial de cambios de estado.
- Asignación de responsable.
- SLA, prioridades y notificaciones.
- Permisos por rol.
- API versionada.
- Auditoría y métricas.

## Uso de IA

La IA apoyó la generación inicial de estructura y pruebas. La validación debe realizarse mediante revisión de código, Pint, PHPUnit, migraciones, build de Vite y prueba manual del flujo completo.
