# Guía Técnica de Base de Datos - Proyecto dfmSecure

Esta guía proporciona una descripción detallada de la estructura de la base de datos para facilitar su creación y mantenimiento. Se incluyen todas las tablas del sistema, sus tipos de datos en MySQL y la función de cada campo.

---

## 🛠️ Estructura Completa de Tablas (20 Tablas)

### 1. Usuarios y Seguridad

#### Tabla: `roles` (Roles de Usuario)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único (Auto-incremental). (PK) |
| `name` | VARCHAR(255) | Nombre del rol: `admin`, `insured`, `adjuster`, `supervisor`. |
| `created_at`, `updated_at`, `deleted_at` | TIMESTAMP | Registros de auditoría y eliminación lógica. |

#### Tabla: `users` (Usuarios del Sistema)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único. (PK) |
| `name`, `father_lastname`, `mother_lastname` | VARCHAR(255) | Nombre completo del usuario. |
| `username` | VARCHAR(30) | Nombre de usuario único para inicio de sesión. |
| `email` | VARCHAR(255) | Correo electrónico único. |
| `password` | VARCHAR(255) | Contraseña encriptada. |
| `phone` | VARCHAR(20) | Número telefónico único. |
| `birth_date` | DATE | Fecha de nacimiento. |
| `gender` | VARCHAR(255) | Género: `male`, `female`, `other`. |
| `role_id` | BIGINT UNSIGNED | Relación con la tabla `roles`. (FK) |
| `address_id` | BIGINT UNSIGNED | Relación con la tabla `addresses`. (FK) |

### 2. Información Fiscal y Localización

#### Tabla: `addresses` (Direcciones)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único. (PK) |
| `type` | VARCHAR(255) | Tipo: `home`, `office`, `fiscal`. |
| `country`, `state`, `city`, `neighborhood`, `street` | VARCHAR(255) | Detalles de ubicación geográfica. |
| `external_number`, `internal_number` | VARCHAR(10) | Números de domicilio. |
| `zip_code` | VARCHAR(10) | Código postal. |

#### Tabla: `fiscals` (Datos de Facturación)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único. (PK) |
| `rfc` | VARCHAR(13) | RFC único (México). |
| `fiscal_type` | VARCHAR(255) | `natural_person` o `legal_person`. |
| `company_name` | VARCHAR(255) | Razón social (para personas morales). |
| `tax_regime` | VARCHAR(5) | Código del régimen fiscal del SAT. |
| `user_id` | BIGINT UNSIGNED | Dueño de los datos fiscales. (FK) |

### 3. Gestión de Vehículos

#### Tabla: `vehicle_models` (Catálogo de Modelos)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único. (PK) |
| `year` | YEAR | Año del vehículo. |
| `brand`, `sub_brand`, `version`, `color` | VARCHAR(255) | Detalles técnicos del vehículo. |

#### Tabla: `insured_vehicles` (Vehículos Asegurados)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único. (PK) |
| `vin` | VARCHAR(17) | Número de Identificación Vehicular único. |
| `plate` | VARCHAR(10) | Placa única del vehículo. |
| `vehicle_model_id` | BIGINT UNSIGNED | Enlace al catálogo de modelos. (FK) |
| `user_id` | BIGINT UNSIGNED | Propietario del vehículo. (FK) |

### 4. Seguros y Pólizas

#### Tabla: `plans` (Planes de Seguro)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único. (PK) |
| `name` | VARCHAR(255) | Nombre del plan (ej. Básico, Amplia). |
| `status` | VARCHAR(255) | Estado: `active`, `inactive`. |
| `info` | JSON | Coberturas y detalles en formato JSON. |
| `price` | DECIMAL(10,2) | Costo del plan. |

#### Tabla: `policies` (Pólizas de Seguro)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único. (PK) |
| `folio` | CHAR(36) | Folio único UUID identificador del contrato. |
| `status` | VARCHAR(255) | Estado: `pending`, `active`, `cancelled`, `expired`. |
| `begin_validity`, `end_validity` | DATE | Periodo de protección. |
| `vehicle_id` | BIGINT UNSIGNED | Vehículo asegurado. (FK) |
| `insured_id` | BIGINT UNSIGNED | Usuario que contrata. (FK) |
| `plan_id` | BIGINT UNSIGNED | Plan seleccionado. (FK) |

### 5. Siniestros y Seguimiento

#### Tabla: `sinisters` (Reportes de Accidente)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único. (PK) |
| `occur_date`, `report_date`, `close_date` | DATE | Fechas clave del incidente. |
| `description` | TEXT | Qué sucedió en el siniestro. |
| `location` | VARCHAR(255) | Lugar del accidente. |
| `status` | VARCHAR(255) | Estado según `SinisterStatusEnum`. |
| `adjuster_id` | BIGINT UNSIGNED | Ajustador asignado. (FK) |
| `policy_id` | BIGINT UNSIGNED | Póliza bajo la cual se reporta. (FK) |

#### Tabla: `sinister_multimedia` (Evidencia del Accidente)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único. (PK) |
| `type` | VARCHAR(255) | `photo`, `video`, `document`, `audio`. |
| `blob_file` | LONGBLOB | Archivo binario guardado en la BD. |
| `path_file` | LONGTEXT | Ruta del archivo si se guarda en disco. |
| `sinister_id` | BIGINT UNSIGNED | Siniestro al que pertenece. (FK) |

#### Tabla: `sinister_comments` (Comentarios y Bitácora)
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `id` | BIGINT UNSIGNED | Identificador único. (PK) |
| `comment` | LONGTEXT | Texto de la observación o nota. |
| `sinister_id` | BIGINT UNSIGNED | Siniestro relacionado. (FK) |
| `user_id` | BIGINT UNSIGNED | Usuario que escribió la nota. (FK) |

### 6. Tablas del Sistema (Laravel default)

#### Tabla: `cache` y `cache_locks`
| Campo | Tipo MySQL | Descripción |
| :--- | :--- | :--- |
| `key` | VARCHAR(255) | Clave única de caché. (PK) |
| `value` | MEDIUMTEXT | Valor almacenado. |
| `expiration` | INT | Tiempo de expiración. |

#### Tabla: `jobs`, `job_batches`, `failed_jobs`
Utilizadas para procesos en segundo plano (colas). Guardan el estado, los intentos y los errores de tareas automáticas.

#### Tabla: `sessions`
Gestiona la conexión activa de los usuarios para que no tengan que loguearse en cada clic.

#### Tabla: `migrations` y `password_reset_tokens`
Control de versiones de la BD y gestión de tokens para cambio de contraseña.

---

## 🔗 Cómo obtener el Diagrama Relacional en phpMyAdmin

1. Selecciona la BD `dfmsecure`.
2. Haz clic en la pestaña **"Más"** → **"Diseñador"**.
3. Organiza las tablas para visualizar las líneas que representan las Llaves Foráneas (FK).
