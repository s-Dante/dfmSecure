SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- 0-. Crear la BD y usarla
CREATE DATABASE IF NOT EXISTS dfmsecure;
USE dfmsecure;


-- 1-. Usuarios y seguridad
--  ---> Roles
CREATE TABLE IF NOT EXISTS roles (
    id          BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'Identificador principal de la tabla de roles',
    name        VARCHAR(255)    NOT NULL                                COMMENT 'Nombre del rol',
    created_at  TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at  TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at  TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro',
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  ---> Direcciones
CREATE TABLE IF NOT EXISTS addresses (
    id              BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'Identificador de la tabla de direcciones',
    type            VARCHAR(255)    NOT NULL                 DEFAULT 'home' COMMENT 'Tipo de direccion que se registra',
    country         VARCHAR(255)    NOT NULL                                COMMENT 'Pais asociado a la direccion',
    state           VARCHAR(255)    NOT NULL                                COMMENT 'Estado asociado a la direccion', 
    city            VARCHAR(255)    NOT NULL                                COMMENT 'Ciudad asociada a la direccion',
    neighborhood    VARCHAR(255)    NOT NULL                                COMMENT 'Colonia asociada a la direccion',
    street          VARCHAR(255)    NOT NULL                                COMMENT 'Calle asociada a la direccion',
    external_number VARCHAR(10)     NOT NULL                                COMMENT 'Numero exterior de la direccion',
    internal_number VARCHAR(10)     NULL                                    COMMENT 'Numero interior de la direccion',
    zip_code        VARCHAR (10)    NOT NULL                                COMMENT 'Codigo postal asociado a la direccion',
    created_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  ---> Usuarios
CREATE TABLE IF NOT EXISTS users (
    id                      BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'identificador principal de la tabla de usuarios',
    name                    VARCHAR(80)     NOT NULL                                COMMENT 'Nombre(s) del usuario',
    father_lastname         VARCHAR(50)     NOT NULL                                COMMENT 'Apellido paterno del usuario',
    mother_lastname         VARCHAR(50)     NULL                                    COMMENT 'Apellido materno del usuario',
    username                VARCHAR(30)     UNIQUE  NOT NULL                        COMMENT 'Alias o identificador unico del usuario',
    profile_picture_url     LONGTEXT        NULL                                    COMMENT 'URL de la foto de perfil',
    profile_picture_blob    LONGBLOB        NULL                                    COMMENT 'Imagen de perfil en formato binario',
    email                   VARCHAR(80)     UNIQUE  NOT NULL                        COMMENT 'Correo electronico del usuario',
    password                VARCHAR(255)    NOT NULL                                COMMENT 'Contraseña del usuario',
    phone                   VARCHAR(20)     UNIQUE  NOT NULL                        COMMENT 'Telefono del usuario',
    birth_date              DATE            NULL                                    COMMENT 'Fecha de nacimiento del usuario',
    gender                  VARCHAR(20)     NOT NULL DEFAULT 'other'                COMMENT 'Genero del usuario',
    email_verified_at       TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se verifico el correo electronico',
    role_id                 BIGINT          UNSIGNED NOT NULL                       COMMENT 'Identificador del rol del usuario',
    address_id              BIGINT          UNSIGNED NULL                           COMMENT 'Identificador de la direccion del usuario',
    remember_token          VARCHAR(100)    NULL                                    COMMENT 'Token de recordatorio',
    created_at              TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at              TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at              TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro',

    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE RESTRICT,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  ---> Fiscals
CREATE TABLE IF NOT EXISTS fiscals (
    id              BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'Identificador principal de la tabla de datos fiscales',
    rfc             VARCHAR(13)     UNIQUE  NOT NULL                        COMMENT 'Registro Federal de Contribuyentes',
    fiscal_type     VARCHAR(255)    NOT NULl DEFAULT 'natural_person'       COMMENT 'Tipo de entidad ante la ley',
    company_name    VARCHAR(255)    NULL                                    COMMENT 'Nombre de la empresa',
    tax_regime      VARCHAR(5)      NOT NULL                                COMMENT 'Regimen fiscal',
    user_id         BIGINT          UNSIGNED NOT NULL UNIQUE                COMMENT 'Identificador del usuario',
    created_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro',

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 2-. Gestion de Vehiculos
--  ---> Modelos de Vehiculos
CREATE TABLE IF NOT EXISTS vehicle_models (
    id              BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'Identificador principal de la tabla de modelos de vehiculos',
    year            YEAR            NOT NULL                                COMMENT 'Año del vehiculo',
    brand           VARCHAR(255)    NOT NULL                                COMMENT 'Marca del vehiculo',
    sub_brand       VARCHAR(255)    NOT NULL                                COMMENT 'Submarca del vehiculo',
    version         VARCHAR(255)    NOT NULL                                COMMENT 'Version del vehiculo',
    color           VARCHAR(255)    NOT NULL                                COMMENT 'Color del vehiculo',
    created_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro',

    UNIQUE KEY unq_vehicle_model (year, brand, sub_brand, version, color)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  ---> Vehiculos Asegurados
CREATE TABLE IF NOT EXISTS insured_vehicles (
    id                  BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'Identificador principal de la tabla de vehiculos asegurados',
    vin                 VARCHAR(17)     UNIQUE  NOT NULL                        COMMENT 'Numero de identificacion vehicular',
    plate               VARCHAR(10)     UNIQUE  NOT NULL                        COMMENT 'Placa del vehiculo',
    vehicle_model_id    BIGINT          NOT NULL                                COMMENT 'Identificador del modelo del vehiculo',
    user_id             BIGINT          NOT NULL                                COMMENT 'Identificador del usuario',
    created_at          TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at          TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at          TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro',

    FOREIGN KEY (vehicle_model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX insured_vehicles_vin_index ON insured_vehicles (vin);
CREATE INDEX insured_vehicles_plate_index ON insured_vehicles (plate);
CREATE INDEX insured_vehicles_vehicle_model_id_index ON insured_vehicles (vehicle_model_id);

-- 3-. Seguros y Polizas
--  ---> Planes
CREATE TABLE IF NOT EXISTS plans (
    id              BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'Identificador principal de la tabla de planes',
    name            VARCHAR(255)    UNIQUE  NOT NULL                        COMMENT 'Nombre del plan',
    status          VARCHAR(255)    NOT NULL DEFAULT 'active'               COMMENT 'Estado del plan',
    info            JSON            NOT NULL                                COMMENT 'Informacion del plan',
    price           DECIMAL(10,2)   NOT NULL                                COMMENT 'Precio del plan',
    created_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro',
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  ---> Polizas
CREATE TABLE IF NOT EXISTS policies (
    id              BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'Identificador principal de la tabla de polizas',
    folio           CHAR(36)        UNIQUE  NOT NULL                        COMMENT 'Folio de la poliza',
    status          VARCHAR(255)    NOT NULL DEFAULT 'pending'              COMMENT 'Estado de la poliza',
    begin_validity  DATE            NOT NULL                                COMMENT 'Fecha de inicio de vigencia de la poliza',
    end_validity    DATE            NOT NULL                                COMMENT 'Fecha de fin de vigencia de la poliza',
    vehicle_id      BIGINT          UNSIGNED NOT NULL                       COMMENT 'Identificador del vehiculo',
    insured_id      BIGINT          UNSIGNED NOT NULL                       COMMENT 'Identificador del asegurado',
    plan_id         BIGINT          UNSIGNED NOT NULL                       COMMENT 'Identificador del plan',
    created_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro',

    FOREIGN KEY (vehicle_id) REFERENCES insured_vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (insured_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX policies_vehicle_id_index ON policies (vehicle_id);
CREATE INDEX policies_insuered_id_index ON policies (insured_id);
CREATE INDEX policies_plan_id_index ON policies (plan_id);

-- 4-. Siniestros
--  ---> Siniestros
CREATE TABLE IF NOT EXISTS sinisters (
    id              BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'Identificador principal de la tabla de siniestros',
    occur_date      DATE            NOT NULL                                COMMENT 'Fecha en la que ocurrio el siniestro',
    report_date     DATE            NOT NULL                                COMMENT 'Fecha en la que se reporto el siniestro',
    close_date      DATE            NULL                                    COMMENT 'Fecha en la que se cerro el siniestro',
    description     TEXT            NOT NULL                                COMMENT 'Descripcion del siniestro',
    location        VARCHAR(255)    NOT NULL                                COMMENT 'Ubicacion del siniestro',
    status          VARCHAR(255)    NOT NULL DEFAULT 'reported'             COMMENT 'Estado del siniestro',
    adjuster_id     BIGINT          UNSIGNED NULL                           COMMENT 'Identificador del ajustador',
    supervisor_id   BIGINT          UNSIGNED NULL                           COMMENT 'Identificador del supervisor',
    policy_id       BIGINT          UNSIGNED NULL                           COMMENT 'Identificador de la poliza',
    created_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro',
    
    FOREIGN KEY (adjuster_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (supervisor_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (policy_id) REFERENCES policies(id) ON DELETE CASCADE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX sinisters_adjuster_id_index ON sinisters (adjuster_id);
CREATE INDEX sinisters_supervisor_id_index ON sinisters (supervisor_id);
CREATE INDEX sinisters_policy_id_index ON sinisters (policy_id);

--  ---> Multimedia de Siniestros
CREATE TABLE IF NOT EXISTS sinister_multimedia (
    id              BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'Identificador principal de la tabla de multimedia de siniestros',
    type            VARCHAR(255)    NOT NULL                                COMMENT 'Tipo de multimedia',
    blob_file       LONGBLOB        NULL                                    COMMENT 'Archivo en formato blob',
    path_file       LONGTEXT        NULL                                    COMMENT 'Ruta del archivo',
    description     TEXT            NULL                                    COMMENT 'Descripcion de la multimedia',
    mime            VARCHAR(255)    NULL                                    COMMENT 'Tipo de archivo',
    size            INT             NULL                                    COMMENT 'Tamaño del archivo',
    thumbnail       VARCHAR(255)    NULL                                    COMMENT 'Ruta de la miniatura',
    sinister_id     BIGINT          UNSIGNED NOT NULL                       COMMENT 'Identificador del siniestro',
    created_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro',

    FOREIGN KEY (sinister_id) REFERENCES sinisters(id) ON DELETE CASCADE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--  ---> Comentarios de Siniestros
CREATE TABLE IF NOT EXISTS sinister_comments (
    id              BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY    COMMENT 'Identificador principal de la tabla de comentarios de siniestros',
    comment         LONGTEXT        NOT NULL                                COMMENT 'Comentario del siniestro',
    sinister_id     BIGINT          UNSIGNED NOT NULL                       COMMENT 'Identificador del siniestro',
    user_id         BIGINT          UNSIGNED NOT NULL                       COMMENT 'Identificador del usuario',
    created_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se creo el registro',
    updated_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se actualizo el registro',
    deleted_at      TIMESTAMP       NULL                                    COMMENT 'Fecha en la que se elimino el registro',

    FOREIGN KEY (sinsiter_id) REFERENCES sinisters(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- 5-. Auxiliares
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email           VARCHAR(80)     PRIMARY KEY,
    token           VARCHAR(255)    NOT NULL,
    created_at      TIMESTAMP       NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sessions (
    id              VARCHAR(255)    PRIMARY KEY,
    user_id         BIGINT          UNSIGNED NULL,
    ip_address      VARCHAR(45)     NULL,
    playload        LONGTEXT        NOT NULL,
    last_activity   INT             NOT NULL,

    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX sessions_user_id_index ON sessions (user_id);
CREATE INDEX sessions_last_activity_index ON sessions (last_activity);

CREATE TABLE IF NOT EXISTS cache (
    `key`           VARCHAR(255)    PRIMARY KEY,
    `value`         MEDIUMTEXT      NOT NULL,
    `expiration`    INT             NOT NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX cache_expiration_index ON cache (expiration);

CREATE TABLE IF NOT EXISTS cache_locks (
    `key`           VARCHAR(255)    PRIMARY KEY,
    `owner`         VARCHAR(255)    NOT NULL,
    `expiration`    INT             NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX cache_locks_key_index ON cache_locks (key);

CREATE TABLE IF NOT EXISTS jobs (
    id              BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY,
    queue           VARCHAR(255)    NOT NULL,
    payload         LONGTEXT        NOT NULL,
    attempts        TINYINT         UNSIGNED NOT NULL,
    reserved_at     INT             UNSIGNED NULL,
    available_at    INT             UNSIGNED NOT NULL,
    createt_at      INT             UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE INDEX jobs_queue_index ON jobs (queue);

CREATE TABLE IF NOT EXISTS job_batches (
    id                  VARCHAR(255)    PRIMARY KEY,
    name                VARCHAR(255)    NOT NULL,
    total_jobs          INT             NOT NULL,
    pending_jobs        INT             NOT NULL,
    failed_jobs         INT             NOT NULL,
    failed_jobs_ids     LONGTEXT        NOT NULL,
    options             MEDIUMTEXT      NULL,
    cancelled_at        INT             NULL,
    created_at          INT             NOT NULL,
    finished_at         INT             NULL,
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS failed_jobs (
    id                  BIGINT          UNSIGNED AUTO_INCREMENT  PRIMARY KEY,
    uuid                VARCHAR(255)    UNIQUE NOT NULL,
    connection          TEXT            NOT NULL,
    queue               TEXT            NOT NULL,
    payload             LONGTEXT        NOT NULL,
    exception           LONGTEXT        NOT NULL,
    failed_at           TIMESTAMP       DEFAULT CURRENT_TIMESTAMP NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


SET FOREIGN_KEY_CHECKS = 1;