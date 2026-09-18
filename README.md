# Sistema de Solicitudes de Servicio

Aplicación web desarrollada con Laravel 12 para registrar y administrar solicitudes de servicio.

La solución fue preparada como prueba técnica y cubre el flujo completo de creación, consulta, validación y actualización controlada del estado de las solicitudes. El proyecto también incorpora autenticación, control de acceso por roles y permisos, perfiles de usuario y un centro de administración reutilizable.

## Alcance principal

El módulo evaluado permite:

- Registrar una nueva solicitud de servicio.
- Consultar el listado de solicitudes registradas.
- Buscar solicitudes por nombre, correo electrónico o descripción.
- Filtrar solicitudes por tipo y estado.
- Consultar el detalle de una solicitud.
- Actualizar el estado de una solicitud.
- Restringir el cambio de estado a usuarios administradores.
- Validar los datos recibidos mediante Form Requests.
- Presentar mensajes de validación en español.
- Persistir la información en SQLite.
- Manejar accesos no autorizados y recursos inexistentes.

La ruta principal del módulo es:

```text
/service-requests
```

## Flujo de una solicitud

Cada solicitud puede encontrarse en uno de los siguientes estados:

| Valor interno | Etiqueta visible |
| ------------- | ---------------- |
| `new`         | Nueva            |
| `in_progress` | En proceso       |
| `completed`   | Finalizada       |

Toda solicitud se crea inicialmente con el estado `new`.

El estado inicial se establece en el servidor. Aunque un cliente intente enviar otro estado durante la creación, la aplicación lo reemplaza por `new`.

## Tipos de solicitud

El módulo incluye los siguientes tipos:

| Valor interno              | Etiqueta visible         |
| -------------------------- | ------------------------ |
| `technical_support`        | Soporte técnico          |
| `information`              | Solicitud de información |
| `administrative_procedure` | Trámite administrativo   |
| `other`                    | Otra                     |

Los tipos y estados permitidos se encuentran centralizados como constantes en el modelo `ServiceRequest`.

## Autenticación

El proyecto utiliza Laravel Breeze para proporcionar:

- Registro de usuarios.
- Inicio y cierre de sesión.
- Recuperación de contraseña.
- Confirmación de contraseña.
- Verificación de correo electrónico.
- Gestión del perfil.
- Actualización de contraseña.

Las rutas del módulo de solicitudes requieren una sesión autenticada.

Un visitante sin autenticación es redirigido al formulario de inicio de sesión.

## Roles y permisos

El proyecto utiliza Spatie Laravel Permission.

Los roles principales son:

- `admin`
- `user`

### Usuario autenticado

Un usuario autenticado puede:

- Registrar solicitudes.
- Consultar el listado.
- Buscar y filtrar solicitudes.
- Consultar el detalle.
- Consultar el estado actual.

Un usuario normal no puede modificar el estado.

### Administrador

Un administrador puede realizar las acciones anteriores y, adicionalmente:

- Cambiar el estado de las solicitudes.
- Acceder al centro de administración.
- Administrar los recursos habilitados en el CRUD dinámico.

La actualización de estados requiere el permiso:

```text
manage service requests
```

La autorización se aplica en tres niveles:

1. Middleware de la ruta.
2. Método `authorize()` del Form Request.
3. Visibilidad condicional del formulario en Blade.

Ocultar el formulario no constituye la única protección. Si un usuario sin permiso intenta enviar manualmente la petición de actualización, la aplicación responde con `403 Forbidden`.

## CRUD administrativo dinámico

El proyecto incluye un centro de administración basado en un CRUD dinámico y reutilizable.

El CRUD permite gestionar modelos habilitados mediante metadatos y convenciones, evitando construir un controlador y vistas independientes para cada catálogo simple.

Entre sus capacidades se encuentran:

- Descubrimiento de recursos administrativos habilitados.
- Listado paginado.
- Búsqueda.
- Creación y actualización de registros.
- Eliminación cuando el recurso lo permite.
- Etiquetas de campos configurables.
- Reglas de validación por modelo.
- Selectores para catálogos y relaciones.
- Manejo de claves foráneas.
- Respuestas y errores controlados.

El sistema de solicitudes de servicio no utiliza el CRUD dinámico como implementación principal. Se desarrolló de forma explícita con su propio controlador, Form Requests y vistas porque tiene un flujo específico y debe ser sencillo de explicar durante la sustentación.

El centro de administración se conserva como una capacidad complementaria del proyecto.

## Tecnologías utilizadas

- PHP 8.2 o superior
- Laravel 12
- SQLite
- Laravel Breeze
- Laravel Sanctum
- Spatie Laravel Permission
- Eloquent ORM
- Blade
- Tailwind CSS
- Alpine.js
- Vite
- PHPUnit
- Laravel Pint

## Requisitos

Antes de instalar el proyecto se requiere:

- PHP 8.2 o superior
- Composer
- Node.js
- npm
- Extensión PDO SQLite habilitada
- Git

## Instalación

### 1. Clonar el repositorio

```bash
git clone https://github.com/GARP001100/prueba-solicitudes-servicio.git
cd prueba-solicitudes-servicio
```

### 2. Instalar dependencias de PHP

```bash
composer install
```

### 3. Instalar dependencias del frontend

```bash
npm install
```

### 4. Crear la configuración local

En Windows CMD:

```bat
copy .env.example .env
```

En Linux o macOS:

```bash
cp .env.example .env
```

### 5. Generar la clave de la aplicación

```bash
php artisan key:generate
```

### 6. Configurar SQLite

Confirma que el archivo `.env` contenga:

```env
DB_CONNECTION=sqlite
```

Crea el archivo de base de datos.

En Windows CMD:

```bat
type nul > database\database.sqlite
```

En PowerShell:

```powershell
New-Item database/database.sqlite -ItemType File -Force
```

En Linux o macOS:

```bash
touch database/database.sqlite
```

### 7. Ejecutar las migraciones

```bash
php artisan migrate
```

### 8. Crear roles, permisos y usuarios de demostración

```bash
php artisan db:seed --class=LocalDevelopmentSeeder
```

Si únicamente se desean crear o actualizar roles y permisos:

```bash
php artisan db:seed --class=RolePermissionSeeder
```

### 9. Cargar solicitudes de demostración

Este paso es opcional:

```bash
php artisan db:seed --class=ServiceRequestSeeder
```

### 10. Limpiar cachés

```bash
php artisan optimize:clear
php artisan permission:cache-reset
```

### 11. Compilar los recursos frontend

```bash
npm run build
```

## Ejecución en desarrollo

La aplicación utiliza una terminal para Laravel y otra para Vite.

### Terminal 1

```bash
php artisan serve
```

### Terminal 2

```bash
npm run dev
```

Abre en el navegador:

```text
http://127.0.0.1:8000
```

Luego inicia sesión y accede al módulo:

```text
http://127.0.0.1:8000/service-requests
```

## Credenciales de demostración

Al ejecutar `LocalDevelopmentSeeder`, el entorno local queda preparado con las siguientes cuentas de demostración.

### Administrador

```text
Correo: admin@example.com
Contraseña: Admin123*
Rol: admin
```

### Usuario

```text
Correo: usuario@example.com
Contraseña: Usuario123*
Rol: user
```

Estas credenciales son exclusivamente para desarrollo y demostración local. No deben utilizarse en producción.

## Rutas del módulo

| Método  | Ruta                                        | Propósito               | Acceso                   |
| ------- | ------------------------------------------- | ----------------------- | ------------------------ |
| `GET`   | `/service-requests`                         | Listar solicitudes      | Usuario autenticado      |
| `GET`   | `/service-requests/create`                  | Mostrar formulario      | Usuario autenticado      |
| `POST`  | `/service-requests`                         | Registrar una solicitud | Usuario autenticado      |
| `GET`   | `/service-requests/{serviceRequest}`        | Consultar detalle       | Usuario autenticado      |
| `PATCH` | `/service-requests/{serviceRequest}/status` | Cambiar estado          | Administrador autorizado |

## Estructura del módulo

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── ServiceRequestController.php
│   └── Requests/
│       ├── StoreServiceRequestRequest.php
│       └── UpdateServiceRequestStatusRequest.php
└── Models/
    └── ServiceRequest.php

database/
├── factories/
│   └── ServiceRequestFactory.php
├── migrations/
│   └── 2026_09_18_090000_create_service_requests_table.php
└── seeders/
    ├── LocalDevelopmentSeeder.php
    ├── RolePermissionSeeder.php
    └── ServiceRequestSeeder.php

resources/
└── views/
    └── service-requests/
        ├── create.blade.php
        ├── index.blade.php
        └── show.blade.php

tests/
└── Feature/
    └── ServiceRequestTest.php

docs/
└── PRUEBA_SOLICITUDES_SERVICIO.md
```

## Modelo de datos

La tabla `service_requests` contiene:

| Columna           | Descripción                   |
| ----------------- | ----------------------------- |
| `id`              | Identificador de la solicitud |
| `requester_name`  | Nombre del solicitante        |
| `requester_email` | Correo electrónico            |
| `request_type`    | Tipo de solicitud             |
| `description`     | Descripción de la solicitud   |
| `status`          | Estado actual                 |
| `created_at`      | Fecha de creación             |
| `updated_at`      | Fecha de actualización        |

La migración incluye índices para:

- `status`
- `request_type`
- `created_at`

Estos índices apoyan los filtros y el ordenamiento frecuente del listado.

## Validaciones

### Creación

El formulario de creación valida:

- Nombre obligatorio.
- Nombre entre 3 y 150 caracteres.
- Correo obligatorio.
- Formato de correo válido.
- Tipo obligatorio.
- Tipo incluido en el catálogo permitido.
- Descripción obligatoria.
- Descripción entre 10 y 3000 caracteres.

### Cambio de estado

La actualización valida que:

- El estado sea obligatorio.
- El estado pertenezca a la lista permitida.
- El usuario tenga el permiso `manage service requests`.

El endpoint actualiza únicamente la columna `status`.

No permite modificar:

- Nombre.
- Correo electrónico.
- Tipo.
- Descripción.

## Seguridad

### Protección contra inyección SQL

Se utilizan Eloquent y Query Builder. Los valores se envían como parámetros y no se construyen sentencias SQL completas mediante concatenación de entradas del usuario.

### Validación de entradas

Los datos se validan con Form Requests antes de las operaciones de persistencia.

La aplicación usa:

```php
$request->validated()
```

No utiliza `$request->all()` para persistir directamente toda la entrada.

### Protección CSRF

Los formularios Blade incluyen `@csrf`.

### Escape de salida

Los datos ingresados por usuarios se muestran mediante expresiones Blade escapadas:

```blade
{{ $value }}
```

No se utiliza salida sin escape para presentar contenido suministrado por usuarios.

### Asignación masiva

El modelo define explícitamente los atributos permitidos mediante `$fillable`.

### Autorización

Solamente los usuarios con el permiso `manage service requests` pueden cambiar estados. Los demás reciben una respuesta HTTP `403`.

### Credenciales y configuración

La configuración se administra mediante `.env`.

El archivo `.env` está excluido del repositorio. El proyecto publica únicamente `.env.example`, sin secretos de ejecución.

## Manejo de errores

La solución utiliza el comportamiento estándar de Laravel:

- Los errores de validación regresan al formulario.
- Los valores diligenciados se conservan mediante `old()`.
- Los mensajes se muestran junto a los campos.
- Los recursos inexistentes producen `404 Not Found`.
- Los accesos no autorizados producen `403 Forbidden`.
- Las operaciones exitosas utilizan redirecciones y mensajes flash.
- Los errores no controlados quedan registrados en los logs de Laravel.

No se utilizan bloques `try/catch` genéricos que oculten fallos sin aplicar una recuperación real.

## Búsqueda, filtros y paginación

El listado permite:

- Buscar por nombre.
- Buscar por correo electrónico.
- Buscar dentro de la descripción.
- Filtrar por tipo.
- Filtrar por estado.
- Combinar búsqueda y filtros.
- Mantener los filtros durante la paginación.

Las solicitudes se ordenan desde la más reciente y se paginan de 10 en 10.

## Pruebas

Para ejecutar exclusivamente las pruebas del módulo:

```bash
php artisan test --filter=ServiceRequestTest
```

Para ejecutar toda la suite:

```bash
php artisan test
```

Para validar el estilo en Windows:

```bat
vendor\bin\pint --test
```

En Linux o macOS:

```bash
./vendor/bin/pint --test
```

Para validar el frontend:

```bash
npm run build
```

## Escenarios probados

Las pruebas del módulo cubren:

- Redirección de visitantes al inicio de sesión.
- Acceso de usuarios autenticados.
- Creación de solicitudes.
- Estado inicial impuesto por el servidor.
- Campos obligatorios.
- Formato del correo.
- Tipos permitidos.
- Longitud mínima de la descripción.
- Consulta del detalle.
- Respuesta `404` para identificadores inexistentes.
- Actualización administrativa del estado.
- Respuesta `403` para usuarios normales.
- Visibilidad del formulario según permisos.
- Rechazo de estados inválidos.
- Protección de los campos diferentes del estado.
- Filtro por estado.
- Filtro por tipo.
- Búsqueda por nombre.
- Búsqueda por correo.

## Decisiones técnicas

### Entidad independiente

Se implementó `ServiceRequest` como entidad independiente.

No se reutilizó el modelo de citas porque una solicitud de servicio no requiere profesional, calendario, disponibilidad ni reglas de solapamiento.

### Formularios Blade tradicionales

Se utilizaron formularios Blade con redirecciones y mensajes flash.

Esta decisión reduce complejidad, facilita el manejo de errores y permite explicar el flujo completo dentro del tiempo disponible para la prueba.

### Catálogos mediante constantes

Los estados y tipos se definieron como constantes del modelo.

No se creó una tabla adicional porque el alcance incluye un conjunto pequeño y fijo de valores.

### Actualización especializada

No se implementó una edición general.

Existe un endpoint específico que actualiza únicamente el estado, conforme al requerimiento funcional.

### Autorización por capacidad

La actualización de estados se controla mediante un permiso y no mediante una comparación directa con el nombre del rol.

Esto permite asignar la misma capacidad a un rol futuro sin modificar rutas ni controladores.

### CRUD dinámico para catálogos simples

El CRUD dinámico se conserva para recursos administrativos simples y reutilizables.

El flujo principal de solicitudes utiliza una implementación explícita para mantener claras sus reglas, validaciones y decisiones de seguridad.

## Componentes adicionales

El repositorio parte de una base institucional que incluye:

- Autenticación completa.
- Gestión de perfiles.
- Roles y permisos.
- Centro de administración.
- CRUD dinámico para recursos configurados.
- Componentes reutilizables de interfaz.
- Páginas de error personalizadas.
- Pruebas automatizadas de regresión.

También existen componentes experimentales de catálogo y agendamiento que no forman parte del alcance principal evaluado.

Las opciones del módulo experimental de agendamiento se encuentran ocultas en la navegación de la entrega actual. El código se conserva únicamente como trabajo complementario fuera del flujo principal.

## Uso de inteligencia artificial

Se utilizó inteligencia artificial como herramienta de apoyo en:

- Generación inicial de estructuras.
- Revisión de validaciones.
- Elaboración de escenarios de prueba.
- Organización de documentación.

El código fue verificado mediante:

- Revisión manual.
- Ejecución de migraciones.
- Inspección de rutas.
- Laravel Pint.
- Pruebas Feature.
- Suite completa de PHPUnit.
- Compilación con Vite.
- Pruebas manuales del flujo funcional.

La inteligencia artificial no reemplazó la comprensión ni la validación del código.

## Diagnóstico de rendimiento

Si una petición comienza a tardar aproximadamente 20 segundos o presenta timeout, deben revisarse:

1. Logs de Laravel.
2. Consulta SQL ejecutada.
3. Plan de ejecución.
4. Índices disponibles.
5. Volumen de registros.
6. Paginación.
7. Consultas N+1.
8. Bloqueos de base de datos.
9. Servicios externos.
10. Consumo de CPU y memoria.
11. Conectividad.
12. Cambios recientes de código o configuración.

Este módulo utiliza paginación e índices en campos frecuentes para evitar cargar todos los registros en una sola petición.

## Mejoras futuras

Con tiempo adicional podrían implementarse:

- Historial de cambios de estado.
- Usuario responsable de cada solicitud.
- Prioridades.
- Acuerdos de nivel de servicio.
- Comentarios internos.
- Archivos adjuntos.
- Notificaciones.
- Auditoría de acciones.
- Permisos adicionales por área.
- API versionada para solicitudes.
- Métricas y reportes.

## Documentación adicional

La documentación específica del módulo se encuentra en:

```text
docs/PRUEBA_SOLICITUDES_SERVICIO.md
```

El repositorio también contiene documentación de componentes complementarios dentro de `docs/`.

## Licencia

Este repositorio se entrega con fines de evaluación técnica.

No se concede una licencia de uso adicional mientras no exista un archivo `LICENSE` que la defina expresamente.
