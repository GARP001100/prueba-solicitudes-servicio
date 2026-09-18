# API de catálogo institucional

## Propósito

La API v1 expone el catálogo de servicios institucionales para ser consumido por módulos de agendamiento, una SPA o una aplicación móvil. Los endpoints públicos muestran solo registros activos y usan API Resources para serializar las respuestas JSON.

## Base URL

- /api/v1

## Endpoints públicos

### GET /api/v1/catalog/categories

Obtiene todas las categorías activas.

Parámetros: ninguno.

Respuesta esperada:

```json
{
    "data": [
        {
            "id": 1,
            "name": "Asesoría académica",
            "description": "Acompañamiento académico institucional",
            "services_count": 2
        }
    ]
}
```

### GET /api/v1/catalog/categories/{serviceCategory}

Obtiene una categoría activa por id.

### GET /api/v1/catalog/services

Obtiene los servicios activos con filtros opcionales.

Parámetros:

- search: texto libre para buscar por nombre o descripción
- category: id de la categoría
- requires_approval: true/false
- duration_min: duración mínima en minutos
- duration_max: duración máxima en minutos
- page: número de página
- per_page: elementos por página, rango permitido 1-50

Ejemplo:

```http
GET /api/v1/catalog/services?search=matr&category=1&duration_min=20&duration_max=45&per_page=10
```

### GET /api/v1/catalog/services/{institutionalService}

Obtiene un servicio activo por id.

Respuesta esperada:

```json
{
    "data": {
        "id": 7,
        "name": "Orientación de matrícula",
        "description": "Acompañamiento para la inscripción de asignaturas",
        "duration_minutes": 30,
        "requires_approval": true,
        "category": {
            "id": 1,
            "name": "Asesoría académica"
        }
    }
}
```

## Endpoint autenticado

### GET /api/v1/me

Requires:

- auth:sanctum

Respuesta esperada:

```json
{
    "data": {
        "id": 1,
        "name": "Usuario test",
        "email": "usuario@ejemplo.com",
        "roles": ["admin"],
        "permissions": ["view dashboard", "manage users"],
        "email_verified_at": null
    }
}
```

## Autenticación Sanctum

Se utiliza el middleware `auth:sanctum` para proteger `/api/v1/me`.

Ejemplo con token Bearer:

```bash
curl -H "Authorization: Bearer <token>" http://localhost:8000/api/v1/me
```

## Códigos HTTP

- 200 OK: consulta exitosa
- 401 Unauthorized: falta autenticación para /api/v1/me
- 404 Not Found: categoría o servicio inexistente o inactivo
- 422 Unprocessable Content: validación de datos si se usa en futuras operaciones de escritura

## Ejemplos curl

```bash
curl http://localhost:8000/api/v1/catalog/categories
curl http://localhost:8000/api/v1/catalog/services?search=matr
curl http://localhost:8000/api/v1/catalog/services?category=1&requires_approval=1&per_page=10
curl -H "Authorization: Bearer <token>" http://localhost:8000/api/v1/me
```

## Ejecución de pruebas

```bash
php artisan test --filter=CatalogApiTest
```

## Limitaciones

- Los endpoints de catálogo son solo de lectura.
- Los servicios inactivos y las categorías inactivas no aparecen en la API pública.
- El límite de `per_page` está restringido entre 1 y 50.
- Los nombres visibles se mantienen en español y los nombres internos en inglés.
