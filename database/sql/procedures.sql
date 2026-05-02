-- =============================================================================
-- STORED PROCEDURES - dfmSecure
-- =============================================================================
-- Descripcion: Todos los stored procedures del sistema de seguros.
-- Fuente:      database/migrations/2026_03_31_003815_create_db_procedures.php
--              database/migrations/2026_05_01_000000_create_additional_db_procedures.php
-- Uso:         Ejecutar directamente en MySQL Workbench o cliente compatible.
-- =============================================================================

DELIMITER $$

-- =============================================================================
-- SECCION 1: DICCIONARIO DE DATOS
-- =============================================================================

/**
 * sp_generate_data_dictionary
 * Genera el diccionario de datos completo con informacion de todas las tablas
 * del sistema: tipo de columna, llaves, relaciones FK, defaults y descripcion.
 *
 * Uso: CALL sp_generate_data_dictionary();
 */
DROP PROCEDURE IF EXISTS sp_generate_data_dictionary $$
CREATE PROCEDURE sp_generate_data_dictionary()
BEGIN
    SELECT
        C.TABLE_NAME        AS 'Tabla',
        C.COLUMN_NAME       AS 'Columna',
        C.COLUMN_TYPE       AS 'Tipo',
        C.IS_NULLABLE       AS 'Nulable',
        CASE
            WHEN C.COLUMN_KEY = 'PRI' THEN 'PK (Llave Primaria)'
            WHEN KCU.REFERENCED_TABLE_NAME IS NOT NULL THEN 'FK (Llave Foranea)'
            WHEN C.COLUMN_KEY = 'UNI' THEN 'UK (Llave Unica)'
            WHEN C.COLUMN_KEY = 'MUL' THEN 'Indice'
            ELSE 'Atributo normal'
        END                 AS 'Tipo de Llave',
        IFNULL(
            CONCAT('Ref: ', KCU.REFERENCED_TABLE_NAME, ' -> (', KCU.REFERENCED_COLUMN_NAME, ')'),
            'N/A'
        )                   AS 'Relacion FK',
        IFNULL(C.COLUMN_DEFAULT, 'Ninguno') AS 'Default',
        C.COLUMN_COMMENT    AS 'Descripcion'
    FROM information_schema.COLUMNS C
    LEFT JOIN information_schema.KEY_COLUMN_USAGE KCU
        ON C.TABLE_SCHEMA = KCU.TABLE_SCHEMA
        AND C.TABLE_NAME  = KCU.TABLE_NAME
        AND C.COLUMN_NAME = KCU.COLUMN_NAME
        AND KCU.REFERENCED_TABLE_NAME IS NOT NULL
    WHERE C.TABLE_SCHEMA = DATABASE()
      AND C.TABLE_NAME IN (
        'roles', 'addresses', 'users', 'fiscals',
        'vehicle_models', 'insured_vehicles',
        'plans', 'policies',
        'sinisters', 'sinister_multimedia', 'sinister_comments',
        'password_reset_tokens', 'sessions',
        'cache', 'cache_locks', 'jobs', 'job_batches', 'failed_jobs'
      )
    ORDER BY C.TABLE_NAME ASC, C.ORDINAL_POSITION ASC;
END $$


-- =============================================================================
-- SECCION 2: AUTENTICACION DE USUARIOS
-- =============================================================================

/**
 * sp_find_user_by_email
 * Encuentra un usuario por email (excluye soft-deleted).
 * Retorna todos los campos del usuario.
 *
 * Uso: CALL sp_find_user_by_email('usuario@example.com');
 */
DROP PROCEDURE IF EXISTS sp_find_user_by_email $$
CREATE PROCEDURE sp_find_user_by_email(
    IN p_email VARCHAR(255)
)
BEGIN
    SELECT
        u.id,
        u.name,
        u.father_lastname,
        u.mother_lastname,
        u.username,
        u.email,
        u.password,
        u.phone,
        u.gender,
        u.birth_date,
        u.profile_picture_url,
        u.profile_picture_blob,
        u.role_id,
        u.address_id,
        u.email_verified_at,
        u.remember_token,
        u.created_at,
        u.updated_at,
        u.deleted_at
    FROM users u
    WHERE u.email = p_email
        AND u.deleted_at IS NULL
    LIMIT 1;
END $$

/**
 * sp_register_user
 * Registra un nuevo usuario asegurado en el sistema.
 * Retorna el id generado y el email del nuevo usuario.
 *
 * Uso: CALL sp_register_user('Juan', 'Perez', 'Lopez', 'jperez01', ...);
 */
DROP PROCEDURE IF EXISTS sp_register_user $$
CREATE PROCEDURE sp_register_user(
    IN p_name            VARCHAR(255),
    IN p_father_lastname VARCHAR(255),
    IN p_mother_lastname VARCHAR(255),
    IN p_username        VARCHAR(30),
    IN p_email           VARCHAR(255),
    IN p_phone           VARCHAR(20),
    IN p_birth_date      DATE,
    IN p_password_hashed VARCHAR(255),
    IN p_gender          VARCHAR(255),
    IN p_role_id         BIGINT UNSIGNED
)
BEGIN
    INSERT INTO users (
        name, father_lastname, mother_lastname,
        username, email, phone, birth_date,
        password, gender, role_id,
        created_at, updated_at
    ) VALUES (
        p_name, p_father_lastname, p_mother_lastname,
        p_username, p_email, p_phone, p_birth_date,
        p_password_hashed, p_gender, p_role_id,
        NOW(), NOW()
    );

    SELECT LAST_INSERT_ID() AS id, p_email AS email;
END $$

/**
 * sp_send_reset_token
 * Inserta o actualiza el token de recuperacion de contrasena.
 * Valida que el email este registrado antes de proceder.
 *
 * Uso: CALL sp_send_reset_token('usuario@example.com', 'token_generado');
 */
DROP PROCEDURE IF EXISTS sp_send_reset_token $$
CREATE PROCEDURE sp_send_reset_token(
    IN p_email VARCHAR(255),
    IN p_token VARCHAR(255)
)
BEGIN
    DECLARE v_user_exists INT DEFAULT 0;

    SELECT COUNT(*) INTO v_user_exists
    FROM users
    WHERE email = p_email
        AND deleted_at IS NULL;

    IF v_user_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'El correo no esta registrado en el sistema';
    END IF;

    INSERT INTO password_reset_tokens (email, token, created_at)
    VALUES (p_email, p_token, NOW())
    ON DUPLICATE KEY UPDATE
        token = p_token,
        created_at = NOW();
END $$

/**
 * sp_verify_reset_token
 * Verifica si el token de recuperacion es valido para el email dado.
 * Retorna el registro si existe, vacio si no.
 *
 * Uso: CALL sp_verify_reset_token('usuario@example.com', 'token');
 */
DROP PROCEDURE IF EXISTS sp_verify_reset_token $$
CREATE PROCEDURE sp_verify_reset_token(
    IN p_email VARCHAR(255),
    IN p_token VARCHAR(255)
)
BEGIN
    SELECT email, token, created_at
    FROM password_reset_tokens
    WHERE email = p_email
        AND token = p_token
    LIMIT 1;
END $$

/**
 * sp_change_password
 * Actualiza la contrasena de un usuario y elimina su token de reset.
 * Retorna las filas afectadas (1 si exito, 0 si el email no existe).
 *
 * Uso: CALL sp_change_password('usuario@example.com', 'nueva_password_hash');
 */
DROP PROCEDURE IF EXISTS sp_change_password $$
CREATE PROCEDURE sp_change_password(
    IN p_email         VARCHAR(255),
    IN p_password_hash VARCHAR(255)
)
BEGIN
    UPDATE users
    SET password   = p_password_hash,
        updated_at = NOW()
    WHERE email      = p_email
        AND deleted_at IS NULL;

    DELETE FROM password_reset_tokens
    WHERE email = p_email;

    SELECT ROW_COUNT() AS affected_rows;
END $$


-- =============================================================================
-- SECCION 3: GESTION DE EMPLEADOS (Admin)
-- =============================================================================

/**
 * sp_get_employees
 * Lista empleados (ajustadores, supervisores, admins) con filtros opcionales
 * de rol y busqueda por nombre/apellido/email, con paginacion.
 *
 * Parametros:
 *   p_role_filter  - Nombre del rol ('adjuster', 'supervisor', 'admin') o 'all'
 *   p_search       - Texto libre para busqueda (nombre, apellido o email), o ''
 *   p_limit        - Cantidad de registros por pagina
 *   p_offset       - Desde que registro empezar (para paginacion)
 *
 * Uso: CALL sp_get_employees('all', '', 10, 0);
 *      CALL sp_get_employees('adjuster', 'Lopez', 10, 0);
 */
DROP PROCEDURE IF EXISTS sp_get_employees $$
CREATE PROCEDURE sp_get_employees(
    IN p_role_filter VARCHAR(50),
    IN p_search      VARCHAR(255),
    IN p_limit       INT,
    IN p_offset      INT
)
BEGIN
    SELECT
        u.id,
        u.name,
        u.father_lastname,
        u.mother_lastname,
        u.username,
        u.email,
        u.phone,
        u.birth_date,
        u.gender,
        u.created_at,
        u.deleted_at,
        r.name  AS role_name,
        r.id    AS role_id
    FROM users u
    INNER JOIN roles r ON r.id = u.role_id AND r.deleted_at IS NULL
    WHERE r.name IN ('adjuster', 'supervisor', 'admin')
        AND (
            p_role_filter = 'all'
            OR r.name = p_role_filter
        )
        AND (
            p_search = ''
            OR u.name           LIKE CONCAT('%', p_search, '%')
            OR u.father_lastname LIKE CONCAT('%', p_search, '%')
            OR u.email           LIKE CONCAT('%', p_search, '%')
        )
    ORDER BY u.created_at DESC
    LIMIT p_limit OFFSET p_offset;
END $$

/**
 * sp_get_employee_counts
 * Devuelve la cantidad de empleados por cada rol gestionable,
 * incluyendo los dados de baja (soft-deleted).
 * Usado para los contadores de las tabs en la vista de gestion.
 *
 * Uso: CALL sp_get_employee_counts();
 */
DROP PROCEDURE IF EXISTS sp_get_employee_counts $$
CREATE PROCEDURE sp_get_employee_counts()
BEGIN
    SELECT
        SUM(CASE WHEN r.name IN ('adjuster', 'supervisor', 'admin') THEN 1 ELSE 0 END) AS total_all,
        SUM(CASE WHEN r.name = 'adjuster'   THEN 1 ELSE 0 END)                         AS total_adjusters,
        SUM(CASE WHEN r.name = 'supervisor' THEN 1 ELSE 0 END)                         AS total_supervisors,
        SUM(CASE WHEN r.name = 'admin'      THEN 1 ELSE 0 END)                         AS total_admins
    FROM users u
    INNER JOIN roles r ON r.id = u.role_id AND r.deleted_at IS NULL
    WHERE r.name IN ('adjuster', 'supervisor', 'admin');
END $$

/**
 * sp_create_employee
 * Crea un nuevo empleado (ajustador, supervisor o admin) en el sistema.
 * El email_verified_at se establece en NOW() ya que el admin los registra directamente.
 * Retorna el id generado y el email.
 *
 * Uso: CALL sp_create_employee('Juan', 'Perez', 'Lopez', 'jperez01', ...);
 */
DROP PROCEDURE IF EXISTS sp_create_employee $$
CREATE PROCEDURE sp_create_employee(
    IN p_name            VARCHAR(255),
    IN p_father_lastname VARCHAR(255),
    IN p_mother_lastname VARCHAR(255),
    IN p_username        VARCHAR(30),
    IN p_email           VARCHAR(255),
    IN p_phone           VARCHAR(20),
    IN p_birth_date      DATE,
    IN p_role_id         BIGINT UNSIGNED,
    IN p_password_hashed VARCHAR(255)
)
BEGIN
    INSERT INTO users (
        name, father_lastname, mother_lastname,
        username, email, phone, birth_date,
        role_id, password,
        email_verified_at,
        created_at, updated_at
    ) VALUES (
        p_name, p_father_lastname, p_mother_lastname,
        p_username, p_email, p_phone, p_birth_date,
        p_role_id, p_password_hashed,
        NOW(),
        NOW(), NOW()
    );

    SELECT LAST_INSERT_ID() AS id, p_email AS email;
END $$

/**
 * sp_update_employee
 * Actualiza los datos de un empleado existente.
 * Si p_password_hashed es NULL o cadena vacia, no se modifica la contrasena.
 * Retorna las filas afectadas.
 *
 * Uso: CALL sp_update_employee(5, 'Juan', 'Perez', 'Lopez', 'jperez@mail.com',
 *                               '5512341234', '1990-01-15', 2, NULL);
 */
DROP PROCEDURE IF EXISTS sp_update_employee $$
CREATE PROCEDURE sp_update_employee(
    IN p_id              BIGINT UNSIGNED,
    IN p_name            VARCHAR(255),
    IN p_father_lastname VARCHAR(255),
    IN p_mother_lastname VARCHAR(255),
    IN p_email           VARCHAR(255),
    IN p_phone           VARCHAR(20),
    IN p_birth_date      DATE,
    IN p_role_id         BIGINT UNSIGNED,
    IN p_password_hashed VARCHAR(255)
)
BEGIN
    IF p_password_hashed IS NOT NULL AND p_password_hashed <> '' THEN
        UPDATE users
        SET name            = p_name,
            father_lastname = p_father_lastname,
            mother_lastname = p_mother_lastname,
            email           = p_email,
            phone           = p_phone,
            birth_date      = p_birth_date,
            role_id         = p_role_id,
            password        = p_password_hashed,
            updated_at      = NOW()
        WHERE id = p_id
            AND deleted_at IS NULL;
    ELSE
        UPDATE users
        SET name            = p_name,
            father_lastname = p_father_lastname,
            mother_lastname = p_mother_lastname,
            email           = p_email,
            phone           = p_phone,
            birth_date      = p_birth_date,
            role_id         = p_role_id,
            updated_at      = NOW()
        WHERE id = p_id
            AND deleted_at IS NULL;
    END IF;

    SELECT ROW_COUNT() AS affected_rows;
END $$

/**
 * sp_soft_delete_employee
 * Realiza un soft-delete del empleado (establece deleted_at).
 * No afecta a usuarios ya dados de baja.
 * Retorna las filas afectadas.
 *
 * Uso: CALL sp_soft_delete_employee(5);
 */
DROP PROCEDURE IF EXISTS sp_soft_delete_employee $$
CREATE PROCEDURE sp_soft_delete_employee(
    IN p_id BIGINT UNSIGNED
)
BEGIN
    UPDATE users
    SET deleted_at = NOW(),
        updated_at = NOW()
    WHERE id = p_id
        AND deleted_at IS NULL;

    SELECT ROW_COUNT() AS affected_rows;
END $$

/**
 * sp_restore_employee
 * Restaura un empleado dado de baja (limpia deleted_at).
 * Solo aplica si el empleado esta actualmente dado de baja.
 * Retorna las filas afectadas.
 *
 * Uso: CALL sp_restore_employee(5);
 */
DROP PROCEDURE IF EXISTS sp_restore_employee $$
CREATE PROCEDURE sp_restore_employee(
    IN p_id BIGINT UNSIGNED
)
BEGIN
    UPDATE users
    SET deleted_at = NULL,
        updated_at = NOW()
    WHERE id = p_id
        AND deleted_at IS NOT NULL;

    SELECT ROW_COUNT() AS affected_rows;
END $$


-- =============================================================================
-- SECCION 4: SINIESTROS
-- =============================================================================

/**
 * sp_get_active_policies_for_sinister
 * Obtiene todas las polizas activas con datos del vehiculo e insured,
 * para poblar el formulario de registro de siniestro del ajustador.
 *
 * Uso: CALL sp_get_active_policies_for_sinister();
 */
DROP PROCEDURE IF EXISTS sp_get_active_policies_for_sinister $$
CREATE PROCEDURE sp_get_active_policies_for_sinister()
BEGIN
    SELECT
        po.id                                                   AS policy_id,
        po.folio                                                AS policy_folio,
        po.policy_number,
        po.status                                               AS policy_status,
        po.begin_validity,
        po.end_validity,
        -- Vehiculo
        iv.id                                                   AS vehicle_id,
        iv.plate,
        iv.vin,
        CONCAT(vm.brand, ' ', vm.sub_brand, ' ', vm.year)      AS vehicle_data,
        vm.color                                                AS vehicle_color,
        -- Asegurado
        CONCAT(u.name, ' ', u.father_lastname)                 AS insured_name,
        u.email                                                 AS insured_email,
        u.phone                                                 AS insured_phone
    FROM policies po
    INNER JOIN insured_vehicles iv ON iv.id = po.vehicle_id       AND iv.deleted_at IS NULL
    INNER JOIN vehicle_models vm   ON vm.id = iv.vehicle_model_id AND vm.deleted_at IS NULL
    INNER JOIN users u             ON u.id  = po.insured_id       AND u.deleted_at  IS NULL
    WHERE po.status = 'active'
        AND po.end_validity >= CURDATE()
        AND po.deleted_at IS NULL
    ORDER BY u.father_lastname ASC, u.name ASC;
END $$

/**
 * sp_create_sinister
 * Registra un nuevo siniestro en el sistema.
 * El trigger trg_generate_sinister_number asignara automaticamente
 * el sinister_number, y trg_validate_policy_in_sinister validara la poliza.
 * Retorna el id y folio del siniestro creado.
 *
 * Uso: CALL sp_create_sinister('uuid-folio', '2026-05-01', NOW(),
 *          'Descripcion del siniestro', 'Calle 123', 'reported', 3, 7);
 */
DROP PROCEDURE IF EXISTS sp_create_sinister $$
CREATE PROCEDURE sp_create_sinister(
    IN p_folio       VARCHAR(36),
    IN p_occur_date  DATE,
    IN p_report_date DATETIME,
    IN p_description TEXT,
    IN p_location    VARCHAR(255),
    IN p_status      VARCHAR(50),
    IN p_adjuster_id BIGINT UNSIGNED,
    IN p_policy_id   BIGINT UNSIGNED
)
BEGIN
    INSERT INTO sinisters (
        folio, occur_date, report_date,
        description, location, status,
        adjuster_id, policy_id,
        created_at, updated_at
    ) VALUES (
        p_folio, p_occur_date, p_report_date,
        p_description, p_location, p_status,
        p_adjuster_id, p_policy_id,
        NOW(), NOW()
    );

    SELECT LAST_INSERT_ID() AS sinister_id, p_folio AS folio;
END $$

/**
 * sp_update_sinister
 * Actualiza los datos editables de un siniestro (sin modificar estado ni adjudicaciones).
 * Retorna las filas afectadas.
 *
 * Uso: CALL sp_update_sinister(10, '2026-04-28', '2026-04-29', 'Calle 456', 'Nueva descripcion');
 */
DROP PROCEDURE IF EXISTS sp_update_sinister $$
CREATE PROCEDURE sp_update_sinister(
    IN p_id          BIGINT UNSIGNED,
    IN p_occur_date  DATE,
    IN p_report_date DATE,
    IN p_location    VARCHAR(255),
    IN p_description TEXT
)
BEGIN
    UPDATE sinisters
    SET occur_date   = p_occur_date,
        report_date  = p_report_date,
        location     = p_location,
        description  = p_description,
        updated_at   = NOW()
    WHERE id = p_id
        AND deleted_at IS NULL;

    SELECT ROW_COUNT() AS affected_rows;
END $$

/**
 * sp_update_sinister_status
 * Actualiza el estado de un siniestro y registra automaticamente un comentario
 * de auditoria. Tambien establece close_date si el nuevo estado es final.
 * (El trigger trg_auto_set_close_date tambien actua, este SP lo gestiona explicitamente
 * para cuando se pasa la fecha desde la aplicacion.)
 *
 * Parametros:
 *   p_sinister_id - ID del siniestro
 *   p_new_status  - Nuevo estado del siniestro
 *   p_user_id     - ID del supervisor que realiza el cambio
 *   p_comment     - Comentario opcional (puede ser NULL)
 *
 * Uso: CALL sp_update_sinister_status(10, 'approved', 4, 'Aprobado con deducible');
 */
DROP PROCEDURE IF EXISTS sp_update_sinister_status $$
CREATE PROCEDURE sp_update_sinister_status(
    IN p_sinister_id BIGINT UNSIGNED,
    IN p_new_status  VARCHAR(50),
    IN p_user_id     BIGINT UNSIGNED,
    IN p_comment     TEXT
)
BEGIN
    DECLARE v_auto_comment TEXT;
    DECLARE v_is_final_status TINYINT DEFAULT 0;

    -- Verificar si el nuevo estado es de resolucion final
    IF p_new_status IN (
        'closed', 'approved', 'approved_with_deductible',
        'approved_without_deductible', 'applies_payment_for_repairs', 'total_loss'
    ) THEN
        SET v_is_final_status = 1;
    END IF;

    -- Actualizar estado del siniestro
    IF v_is_final_status = 1 THEN
        UPDATE sinisters
        SET status     = p_new_status,
            close_date = CURDATE(),
            updated_at = NOW()
        WHERE id = p_sinister_id
            AND deleted_at IS NULL;
    ELSE
        UPDATE sinisters
        SET status     = p_new_status,
            updated_at = NOW()
        WHERE id = p_sinister_id
            AND deleted_at IS NULL;
    END IF;

    -- Construir comentario de auditoria
    IF p_comment IS NOT NULL AND p_comment <> '' THEN
        SET v_auto_comment = CONCAT('Actualizacion de Estatus a ', p_new_status, ': ', p_comment);
    ELSE
        SET v_auto_comment = CONCAT('Actualizacion de Estatus a ', p_new_status);
    END IF;

    -- Insertar comentario automatico de auditoria
    INSERT INTO sinister_comments (sinister_id, user_id, comment, created_at, updated_at)
    VALUES (p_sinister_id, p_user_id, v_auto_comment, NOW(), NOW());

    SELECT ROW_COUNT() AS affected_rows, LAST_INSERT_ID() AS comment_id;
END $$

/**
 * sp_add_sinister_comment
 * Agrega un comentario a un siniestro.
 * Retorna el id del comentario creado.
 *
 * Uso: CALL sp_add_sinister_comment(10, 3, 'Se solicita documentacion adicional.');
 */
DROP PROCEDURE IF EXISTS sp_add_sinister_comment $$
CREATE PROCEDURE sp_add_sinister_comment(
    IN p_sinister_id BIGINT UNSIGNED,
    IN p_user_id     BIGINT UNSIGNED,
    IN p_comment     TEXT
)
BEGIN
    INSERT INTO sinister_comments (sinister_id, user_id, comment, created_at, updated_at)
    VALUES (p_sinister_id, p_user_id, p_comment, NOW(), NOW());

    SELECT LAST_INSERT_ID() AS comment_id;
END $$

/**
 * sp_get_sinister_comments
 * Obtiene todos los comentarios de un siniestro ordenados por fecha,
 * incluyendo el nombre completo del autor.
 *
 * Uso: CALL sp_get_sinister_comments(10);
 */
DROP PROCEDURE IF EXISTS sp_get_sinister_comments $$
CREATE PROCEDURE sp_get_sinister_comments(
    IN p_sinister_id BIGINT UNSIGNED
)
BEGIN
    SELECT
        sc.id           AS comment_id,
        sc.comment,
        sc.created_at,
        u.id            AS user_id,
        CONCAT(u.name, ' ', u.father_lastname)  AS author_name,
        r.name          AS author_role
    FROM sinister_comments sc
    INNER JOIN users u ON u.id = sc.user_id AND u.deleted_at IS NULL
    LEFT JOIN  roles r ON r.id = u.role_id  AND r.deleted_at IS NULL
    WHERE sc.sinister_id = p_sinister_id
        AND sc.deleted_at IS NULL
    ORDER BY sc.created_at ASC;
END $$

/**
 * sp_get_sinister_multimedia
 * Obtiene todos los archivos multimedia de un siniestro.
 *
 * Uso: CALL sp_get_sinister_multimedia(10);
 */
DROP PROCEDURE IF EXISTS sp_get_sinister_multimedia $$
CREATE PROCEDURE sp_get_sinister_multimedia(
    IN p_sinister_id BIGINT UNSIGNED
)
BEGIN
    SELECT
        sm.id,
        sm.type,
        sm.path_file,
        sm.blob_file,
        sm.description,
        sm.mime,
        sm.size,
        sm.created_at
    FROM sinister_multimedia sm
    WHERE sm.sinister_id = p_sinister_id
        AND sm.deleted_at IS NULL
    ORDER BY sm.created_at ASC;
END $$


-- =============================================================================
-- SECCION 5: PERFIL DE USUARIO
-- =============================================================================

/**
 * sp_update_user_basic
 * Actualiza los datos basicos del perfil del usuario (aplica a todos los roles).
 * La foto de perfil se gestiona desde el controlador (fuera del SP).
 * Retorna las filas afectadas.
 *
 * Uso: CALL sp_update_user_basic(3, 'Juan', 'Perez', 'Lopez', '5512341234',
 *          '1990-05-15', 'male');
 */
DROP PROCEDURE IF EXISTS sp_update_user_basic $$
CREATE PROCEDURE sp_update_user_basic(
    IN p_user_id         BIGINT UNSIGNED,
    IN p_name            VARCHAR(255),
    IN p_father_lastname VARCHAR(255),
    IN p_mother_lastname VARCHAR(255),
    IN p_phone           VARCHAR(20),
    IN p_birth_date      DATE,
    IN p_gender          VARCHAR(50)
)
BEGIN
    UPDATE users
    SET name            = p_name,
        father_lastname = p_father_lastname,
        mother_lastname = COALESCE(p_mother_lastname, mother_lastname),
        phone           = COALESCE(p_phone, phone),
        birth_date      = COALESCE(p_birth_date, birth_date),
        gender          = COALESCE(p_gender, gender),
        updated_at      = NOW()
    WHERE id = p_user_id
        AND deleted_at IS NULL;

    SELECT ROW_COUNT() AS affected_rows;
END $$

/**
 * sp_upsert_user_address
 * Crea o actualiza la direccion de un usuario asegurado.
 * Si ya tiene address_id la actualiza; si no, la crea y vincula al usuario.
 * Retorna el address_id resultante.
 *
 * Uso: CALL sp_upsert_user_address(3, 'Calle Roble', '12', 'A', 'Centro',
 *          'Monterrey', 'Nuevo Leon', 'Mexico', '64000');
 */
DROP PROCEDURE IF EXISTS sp_upsert_user_address $$
CREATE PROCEDURE sp_upsert_user_address(
    IN p_user_id         BIGINT UNSIGNED,
    IN p_street          VARCHAR(200),
    IN p_external_number VARCHAR(20),
    IN p_internal_number VARCHAR(20),
    IN p_neighborhood    VARCHAR(100),
    IN p_city            VARCHAR(100),
    IN p_state           VARCHAR(100),
    IN p_country         VARCHAR(100),
    IN p_zip_code        VARCHAR(10)
)
BEGIN
    DECLARE v_address_id BIGINT UNSIGNED DEFAULT NULL;

    -- Obtener el address_id actual del usuario
    SELECT address_id INTO v_address_id
    FROM users
    WHERE id = p_user_id
        AND deleted_at IS NULL
    LIMIT 1;

    IF v_address_id IS NOT NULL THEN
        -- Actualizar direccion existente
        UPDATE addresses
        SET street          = COALESCE(p_street,          street),
            external_number = COALESCE(p_external_number, external_number),
            internal_number = COALESCE(p_internal_number, internal_number),
            neighborhood    = COALESCE(p_neighborhood,    neighborhood),
            city            = COALESCE(p_city,            city),
            state           = COALESCE(p_state,           state),
            country         = COALESCE(p_country,         country),
            zip_code        = COALESCE(p_zip_code,        zip_code),
            updated_at      = NOW()
        WHERE id = v_address_id
            AND deleted_at IS NULL;
    ELSE
        -- Crear nueva direccion y vincular al usuario
        INSERT INTO addresses (
            type, street, external_number, internal_number,
            neighborhood, city, state, country, zip_code,
            created_at, updated_at
        ) VALUES (
            'personal',
            p_street, p_external_number, p_internal_number,
            p_neighborhood, p_city, p_state, p_country, p_zip_code,
            NOW(), NOW()
        );

        SET v_address_id = LAST_INSERT_ID();

        UPDATE users
        SET address_id = v_address_id,
            updated_at = NOW()
        WHERE id = p_user_id;
    END IF;

    SELECT v_address_id AS address_id;
END $$

/**
 * sp_upsert_user_fiscal
 * Crea o actualiza los datos fiscales de un usuario asegurado.
 * Si ya tiene datos fiscales los actualiza; si no, los crea.
 * Retorna el fiscal_id resultante.
 *
 * Uso: CALL sp_upsert_user_fiscal(3, 'PERJ900515AB1', 'Mi Empresa SA', 'Regimen General');
 */
DROP PROCEDURE IF EXISTS sp_upsert_user_fiscal $$
CREATE PROCEDURE sp_upsert_user_fiscal(
    IN p_user_id      BIGINT UNSIGNED,
    IN p_rfc          VARCHAR(13),
    IN p_company_name VARCHAR(200),
    IN p_tax_regime   VARCHAR(100)
)
BEGIN
    DECLARE v_fiscal_id   BIGINT UNSIGNED DEFAULT NULL;
    DECLARE v_fiscal_exists INT DEFAULT 0;

    -- Verificar si ya tiene datos fiscales
    SELECT id INTO v_fiscal_id
    FROM fiscals
    WHERE user_id = p_user_id
        AND deleted_at IS NULL
    LIMIT 1;

    IF v_fiscal_id IS NOT NULL THEN
        -- Actualizar datos fiscales existentes
        UPDATE fiscals
        SET rfc          = COALESCE(p_rfc,          rfc),
            company_name = COALESCE(p_company_name, company_name),
            tax_regime   = COALESCE(p_tax_regime,   tax_regime),
            updated_at   = NOW()
        WHERE id = v_fiscal_id
            AND deleted_at IS NULL;
    ELSE
        -- Crear nuevos datos fiscales
        INSERT INTO fiscals (
            user_id, rfc, fiscal_type, company_name, tax_regime,
            created_at, updated_at
        ) VALUES (
            p_user_id, p_rfc, 'fisica',
            p_company_name, p_tax_regime,
            NOW(), NOW()
        );

        SET v_fiscal_id = LAST_INSERT_ID();
    END IF;

    SELECT v_fiscal_id AS fiscal_id;
END $$

DELIMITER ;
