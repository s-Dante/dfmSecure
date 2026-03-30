-- SQL Script de Inicialización Profesional para dfmSecure
-- Estructura Completa (20 Tablas) + Generador de Diccionario Automatizado

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- =========================================================
-- PARTE 1: CREACIÓN DE ESTRUCTURAS (DDL LIMPIO)
-- =========================================================

-- 1. Usuarios y Seguridad
CREATE TABLE IF NOT EXISTS roles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS addresses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(255) NOT NULL DEFAULT 'home',
    country VARCHAR(255) NOT NULL,
    state VARCHAR(255) NOT NULL,
    city VARCHAR(255) NOT NULL,
    neighborhood VARCHAR(255) NOT NULL,
    street VARCHAR(255) NOT NULL,
    external_number VARCHAR(10) NOT NULL,
    internal_number VARCHAR(10) NULL,
    zip_code VARCHAR(10) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    father_lastname VARCHAR(255) NOT NULL,
    mother_lastname VARCHAR(255) NULL,
    username VARCHAR(30) NOT NULL UNIQUE,
    profile_picture_url LONGTEXT NULL,
    profile_picture_blob LONGBLOB NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20) NOT NULL UNIQUE,
    birth_date DATE NULL,
    gender VARCHAR(255) NOT NULL DEFAULT 'other',
    email_verified_at TIMESTAMP NULL,
    remember_token VARCHAR(100) NULL,
    role_id BIGINT UNSIGNED NOT NULL,
    address_id BIGINT UNSIGNED NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    soft_deletes TIMESTAMP NULL,
    FOREIGN KEY (role_id) REFERENCES roles(id) ON DELETE RESTRICT,
    FOREIGN KEY (address_id) REFERENCES addresses(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS fiscals (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rfc VARCHAR(13) NOT NULL UNIQUE,
    fiscal_type VARCHAR(255) NOT NULL DEFAULT 'natural_person',
    company_name VARCHAR(255) NULL,
    tax_regime VARCHAR(5) NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL UNIQUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Gestión de Vehículos
CREATE TABLE IF NOT EXISTS vehicle_models (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    year YEAR NOT NULL,
    brand VARCHAR(255) NOT NULL,
    sub_brand VARCHAR(255) NOT NULL,
    version VARCHAR(255) NOT NULL,
    color VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    UNIQUE KEY unq_vehicle_model (year, brand, sub_brand, version, color)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS insured_vehicles (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vin VARCHAR(17) NOT NULL UNIQUE,
    plate VARCHAR(10) NOT NULL UNIQUE,
    vehicle_model_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (vehicle_model_id) REFERENCES vehicle_models(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Seguros y Pólizas
CREATE TABLE IF NOT EXISTS plans (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL UNIQUE,
    status VARCHAR(255) NOT NULL DEFAULT 'active',
    info JSON NOT NULL,
    price DECIMAL(10, 2) NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS policies (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    folio CHAR(36) NOT NULL UNIQUE,
    status VARCHAR(255) NOT NULL DEFAULT 'pending',
    begin_validity DATE NOT NULL,
    end_validity DATE NOT NULL,
    vehicle_id BIGINT UNSIGNED NOT NULL,
    insured_id BIGINT UNSIGNED NOT NULL,
    plan_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (vehicle_id) REFERENCES insured_vehicles(id) ON DELETE CASCADE,
    FOREIGN KEY (insured_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES plans(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sinisters (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    occur_date DATE NOT NULL,
    report_date DATE NOT NULL,
    close_date DATE NULL,
    description TEXT NOT NULL,
    location VARCHAR(255) NOT NULL,
    status VARCHAR(255) NOT NULL DEFAULT 'reported',
    adjuster_id BIGINT UNSIGNED NOT NULL,
    supervisor_id BIGINT UNSIGNED NULL,
    policy_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (adjuster_id) REFERENCES users(id) ON DELETE RESTRICT,
    FOREIGN KEY (supervisor_id) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (policy_id) REFERENCES policies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sinister_multimedia (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    type VARCHAR(255) NOT NULL,
    blob_file LONGBLOB NULL,
    path_file LONGTEXT NULL,
    description TEXT NULL,
    mime VARCHAR(255) NULL,
    size INT NULL,
    thumbnail VARCHAR(255) NULL,
    sinister_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (sinister_id) REFERENCES sinisters(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sinister_comments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    comment LONGTEXT NOT NULL,
    sinister_id BIGINT UNSIGNED NOT NULL,
    user_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    deleted_at TIMESTAMP NULL,
    FOREIGN KEY (sinister_comments.sinister_id) REFERENCES sinisters(id) ON DELETE CASCADE,
    FOREIGN KEY (sinister_comments.user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Tablas Auxiliares y de Laravel
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) PRIMARY KEY,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(191) PRIMARY KEY,
    user_id BIGINT UNSIGNED NULL,
    ip_address VARCHAR(45) NULL,
    user_agent TEXT NULL,
    payload LONGTEXT NOT NULL,
    last_activity INT NOT NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) PRIMARY KEY,
    `value` MEDIUMTEXT NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS cache_locks (
    `key` VARCHAR(255) PRIMARY KEY,
    `owner` VARCHAR(255) NOT NULL,
    `expiration` INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(255) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT NULL,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =========================================================
-- PARTE 2: DOCUMENTACIÓN (DICCIONARIO DE DATOS VÍA COMMENTS)
-- =========================================================

-- Roles
ALTER TABLE roles COMMENT = 'Catálogo de roles de acceso';
ALTER TABLE roles MODIFY name VARCHAR(255) COMMENT 'Nombre del rol (admin, insured, adjuster, supervisor)';

-- Usuarios
ALTER TABLE users COMMENT = 'Información de perfil y acceso de usuarios';
ALTER TABLE users MODIFY username VARCHAR(30) COMMENT 'Nombre de usuario único para login';
ALTER TABLE users MODIFY gender VARCHAR(255) COMMENT 'Género. Dominios: male, female, other';
ALTER TABLE users MODIFY role_id BIGINT UNSIGNED COMMENT 'Referencia al rol asignado';

-- Direcciones
ALTER TABLE addresses COMMENT = 'Registro de domicilios';
ALTER TABLE addresses MODIFY type VARCHAR(255) COMMENT 'Tipo. Dominios: fiscal, home, office';

-- Fiscales
ALTER TABLE fiscals COMMENT = 'Datos de facturación SAT';
ALTER TABLE fiscals MODIFY tax_regime VARCHAR(5) COMMENT 'Código del régimen fiscal (ej. 601)';

-- Vehículos
ALTER TABLE vehicle_models COMMENT = 'Catálogo maestro de modelos';
ALTER TABLE insured_vehicles COMMENT = 'Vehículos específicos asegurados';
ALTER TABLE insured_vehicles MODIFY vin VARCHAR(17) COMMENT 'Número de serie único (17 caracteres)';

-- Otros
ALTER TABLE plans COMMENT = 'Oferta de planes de seguro';
ALTER TABLE policies COMMENT = 'Contratos de seguro activos';
ALTER TABLE policies MODIFY folio CHAR(36) COMMENT 'Identificador UUID único de la póliza';

ALTER TABLE sinisters COMMENT = 'Registro de accidentes reportados';
ALTER TABLE sinister_multimedia COMMENT = 'Archivos de evidencia (fotos/videos)';
ALTER TABLE sinister_comments COMMENT = 'Bitácora de seguimiento por ajustadores';

-- =========================================================
-- PARTE 3: SCRIPT GENERADOR DE DICCIONARIO
-- =========================================================

DELIMITER //

DROP PROCEDURE IF EXISTS generate_data_dictionary //

CREATE PROCEDURE generate_data_dictionary()
BEGIN
    -- Una sola consulta potente en lugar de un bucle
    SELECT 
        C.TABLE_NAME AS 'Tabla',
        C.COLUMN_NAME AS 'Columna',
        C.COLUMN_TYPE AS 'Tipo (MySQL)',
        C.IS_NULLABLE AS 'Nulable',
        
        -- Lógica para clasificar el tipo de llave de forma más legible
        CASE 
            WHEN C.COLUMN_KEY = 'PRI' THEN 'PK (Primaria)'
            WHEN KCU.REFERENCED_TABLE_NAME IS NOT NULL THEN 'FK (Foránea)'
            WHEN C.COLUMN_KEY = 'UNI' THEN 'UK (Única)'
            WHEN C.COLUMN_KEY = 'MUL' THEN 'Índice'
            ELSE 'Atributo normal' 
        END AS 'Tipo de Llave',
        
        -- Extraemos exactamente a dónde apunta la llave foránea
        IFNULL(
            CONCAT('Ref: ', KCU.REFERENCED_TABLE_NAME, ' -> (', KCU.REFERENCED_COLUMN_NAME, ')'), 
            'N/A'
        ) AS 'Relación FK',
        
        IFNULL(C.COLUMN_DEFAULT, 'Ninguno') AS 'Default',
        C.COLUMN_COMMENT AS 'Descripción / Dominio'
        
    FROM 
        information_schema.COLUMNS C
    -- Hacemos join para obtener los datos de las relaciones (Foreign Keys)
    LEFT JOIN 
        information_schema.KEY_COLUMN_USAGE KCU 
        ON C.TABLE_SCHEMA = KCU.TABLE_SCHEMA 
        AND C.TABLE_NAME = KCU.TABLE_NAME 
        AND C.COLUMN_NAME = KCU.COLUMN_NAME 
        AND KCU.REFERENCED_TABLE_NAME IS NOT NULL -- Solo nos interesan las foráneas reales
    WHERE 
        C.TABLE_SCHEMA = DATABASE() 
        AND C.TABLE_NAME IN (
            'users', 'roles', 'addresses', 'fiscals', 
            'vehicle_models', 'insured_vehicles', 'plans', 'policies', 
            'sinisters', 'sinister_multimedia', 'sinister_comments',
            'cache', 'cache_locks', 'jobs', 'job_batches', 
            'failed_jobs', 'migrations', 'password_reset_tokens', 'sessions'
        )
    ORDER BY 
        -- Ordenamos por nombre de tabla y luego por el orden original de las columnas
        C.TABLE_NAME ASC,
        C.ORDINAL_POSITION ASC;

END //

DELIMITER ;

-- Instrucción de uso:
-- Para generar el diccionario detallado, ejecuta: CALL generate_data_dictionary();

SET FOREIGN_KEY_CHECKS = 1;
