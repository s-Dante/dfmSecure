<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Nuevos Stored Procedures para cubrir la funcionalidad de los controladores:
     *   - AdminController:              sp_get_employees, sp_get_employee_counts,
     *                                   sp_create_employee, sp_update_employee,
     *                                   sp_soft_delete_employee, sp_restore_employee
     *   - AdjusterSinisterController:   sp_get_active_policies_for_sinister,
     *                                   sp_create_sinister, sp_update_sinister
     *   - SupervisorSinisterController: sp_update_sinister_status
     *   - SinisterController:           sp_add_sinister_comment, sp_get_sinister_comments,
     *                                   sp_get_sinister_multimedia
     *   - ProfileController:            sp_update_user_basic, sp_upsert_user_address,
     *                                   sp_upsert_user_fiscal
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // GESTION DE EMPLEADOS (Admin)
        // ─────────────────────────────────────────────────────────────────────

        /**
         * sp_get_employees
         * Lista empleados con filtros de rol, busqueda de texto y paginacion.
         * Parametros: p_role_filter ('all' | nombre del rol), p_search, p_limit, p_offset
         */
        $spGetEmployees = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_get_employees;
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
                        OR u.name            LIKE CONCAT('%', p_search, '%')
                        OR u.father_lastname  LIKE CONCAT('%', p_search, '%')
                        OR u.email            LIKE CONCAT('%', p_search, '%')
                    )
                ORDER BY u.created_at DESC
                LIMIT p_limit OFFSET p_offset;
            END
        SQL;
        DB::unprepared($spGetEmployees);

        /**
         * sp_get_employee_counts
         * Contadores de empleados por rol para las tabs de la vista de gestion.
         */
        $spGetEmployeeCounts = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_get_employee_counts;
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
            END
        SQL;
        DB::unprepared($spGetEmployeeCounts);

        /**
         * sp_create_employee
         * Crea un nuevo empleado registrado por el administrador.
         * email_verified_at se establece en NOW() (registro directo por admin).
         */
        $spCreateEmployee = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_create_employee;
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
            END
        SQL;
        DB::unprepared($spCreateEmployee);

        /**
         * sp_update_employee
         * Actualiza los datos de un empleado.
         * Si p_password_hashed es NULL o vacio, no modifica la contrasena.
         */
        $spUpdateEmployee = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_update_employee;
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
            END
        SQL;
        DB::unprepared($spUpdateEmployee);

        /**
         * sp_soft_delete_employee
         * Realiza el soft-delete de un empleado (establece deleted_at = NOW()).
         */
        $spSoftDeleteEmployee = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_soft_delete_employee;
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
            END
        SQL;
        DB::unprepared($spSoftDeleteEmployee);

        /**
         * sp_restore_employee
         * Restaura un empleado dado de baja (limpia deleted_at).
         */
        $spRestoreEmployee = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_restore_employee;
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
            END
        SQL;
        DB::unprepared($spRestoreEmployee);


        // ─────────────────────────────────────────────────────────────────────
        // SINIESTROS
        // ─────────────────────────────────────────────────────────────────────

        /**
         * sp_get_active_policies_for_sinister
         * Devuelve polizas activas con datos de vehiculo e insured,
         * para el formulario de registro de siniestro del ajustador.
         */
        $spGetActivePoliciesForSinister = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_get_active_policies_for_sinister;
            CREATE PROCEDURE sp_get_active_policies_for_sinister()
            BEGIN
                SELECT
                    po.id                                                   AS policy_id,
                    po.folio                                                AS policy_folio,
                    po.policy_number,
                    po.status                                               AS policy_status,
                    po.begin_validity,
                    po.end_validity,
                    iv.id                                                   AS vehicle_id,
                    iv.plate,
                    iv.vin,
                    CONCAT(vm.brand, ' ', vm.sub_brand, ' ', vm.year)      AS vehicle_data,
                    vm.color                                                AS vehicle_color,
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
            END
        SQL;
        DB::unprepared($spGetActivePoliciesForSinister);

        /**
         * sp_create_sinister
         * Registra un nuevo siniestro.
         * Los triggers trg_validate_policy_in_sinister y trg_generate_sinister_number
         * actuan automaticamente en el INSERT.
         */
        $spCreateSinister = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_create_sinister;
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
            END
        SQL;
        DB::unprepared($spCreateSinister);

        /**
         * sp_update_sinister
         * Actualiza los datos editables de un siniestro (sin modificar estado).
         */
        $spUpdateSinister = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_update_sinister;
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
            END
        SQL;
        DB::unprepared($spUpdateSinister);

        /**
         * sp_update_sinister_status
         * Actualiza el estado del siniestro y registra un comentario de auditoria.
         * Gestiona close_date para estados finales.
         */
        $spUpdateSinisterStatus = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_update_sinister_status;
            CREATE PROCEDURE sp_update_sinister_status(
                IN p_sinister_id BIGINT UNSIGNED,
                IN p_new_status  VARCHAR(50),
                IN p_user_id     BIGINT UNSIGNED,
                IN p_comment     TEXT
            )
            BEGIN
                DECLARE v_auto_comment    TEXT;
                DECLARE v_is_final_status TINYINT DEFAULT 0;

                IF p_new_status IN (
                    'closed', 'approved', 'approved_with_deductible',
                    'approved_without_deductible', 'applies_payment_for_repairs', 'total_loss'
                ) THEN
                    SET v_is_final_status = 1;
                END IF;

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

                IF p_comment IS NOT NULL AND p_comment <> '' THEN
                    SET v_auto_comment = CONCAT('Actualizacion de Estatus a ', p_new_status, ': ', p_comment);
                ELSE
                    SET v_auto_comment = CONCAT('Actualizacion de Estatus a ', p_new_status);
                END IF;

                INSERT INTO sinister_comments (sinister_id, user_id, comment, created_at, updated_at)
                VALUES (p_sinister_id, p_user_id, v_auto_comment, NOW(), NOW());

                SELECT ROW_COUNT() AS affected_rows, LAST_INSERT_ID() AS comment_id;
            END
        SQL;
        DB::unprepared($spUpdateSinisterStatus);

        /**
         * sp_add_sinister_comment
         * Agrega un comentario a un siniestro.
         */
        $spAddSinisterComment = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_add_sinister_comment;
            CREATE PROCEDURE sp_add_sinister_comment(
                IN p_sinister_id BIGINT UNSIGNED,
                IN p_user_id     BIGINT UNSIGNED,
                IN p_comment     TEXT
            )
            BEGIN
                INSERT INTO sinister_comments (sinister_id, user_id, comment, created_at, updated_at)
                VALUES (p_sinister_id, p_user_id, p_comment, NOW(), NOW());

                SELECT LAST_INSERT_ID() AS comment_id;
            END
        SQL;
        DB::unprepared($spAddSinisterComment);

        /**
         * sp_get_sinister_comments
         * Obtiene los comentarios de un siniestro con datos del autor.
         */
        $spGetSinisterComments = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_get_sinister_comments;
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
            END
        SQL;
        DB::unprepared($spGetSinisterComments);

        /**
         * sp_get_sinister_multimedia
         * Obtiene los archivos multimedia de un siniestro.
         */
        $spGetSinisterMultimedia = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_get_sinister_multimedia;
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
            END
        SQL;
        DB::unprepared($spGetSinisterMultimedia);


        // ─────────────────────────────────────────────────────────────────────
        // PERFIL DE USUARIO
        // ─────────────────────────────────────────────────────────────────────

        /**
         * sp_update_user_basic
         * Actualiza los datos basicos del perfil (todos los roles).
         * La foto de perfil se gestiona desde el controlador.
         */
        $spUpdateUserBasic = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_update_user_basic;
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
                    phone           = COALESCE(p_phone,           phone),
                    birth_date      = COALESCE(p_birth_date,      birth_date),
                    gender          = COALESCE(p_gender,          gender),
                    updated_at      = NOW()
                WHERE id = p_user_id
                    AND deleted_at IS NULL;

                SELECT ROW_COUNT() AS affected_rows;
            END
        SQL;
        DB::unprepared($spUpdateUserBasic);

        /**
         * sp_upsert_user_address
         * Crea o actualiza la direccion del usuario asegurado.
         * Vincula la nueva direccion al usuario si no tenia una.
         */
        $spUpsertUserAddress = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_upsert_user_address;
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

                SELECT address_id INTO v_address_id
                FROM users
                WHERE id = p_user_id
                    AND deleted_at IS NULL
                LIMIT 1;

                IF v_address_id IS NOT NULL THEN
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
            END
        SQL;
        DB::unprepared($spUpsertUserAddress);

        /**
         * sp_upsert_user_fiscal
         * Crea o actualiza los datos fiscales del usuario asegurado.
         */
        $spUpsertUserFiscal = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_upsert_user_fiscal;
            CREATE PROCEDURE sp_upsert_user_fiscal(
                IN p_user_id      BIGINT UNSIGNED,
                IN p_rfc          VARCHAR(13),
                IN p_company_name VARCHAR(200),
                IN p_tax_regime   VARCHAR(100)
            )
            BEGIN
                DECLARE v_fiscal_id BIGINT UNSIGNED DEFAULT NULL;

                SELECT id INTO v_fiscal_id
                FROM fiscals
                WHERE user_id = p_user_id
                    AND deleted_at IS NULL
                LIMIT 1;

                IF v_fiscal_id IS NOT NULL THEN
                    UPDATE fiscals
                    SET rfc          = COALESCE(p_rfc,          rfc),
                        company_name = COALESCE(p_company_name, company_name),
                        tax_regime   = COALESCE(p_tax_regime,   tax_regime),
                        updated_at   = NOW()
                    WHERE id = v_fiscal_id
                        AND deleted_at IS NULL;
                ELSE
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
            END
        SQL;
        DB::unprepared($spUpsertUserFiscal);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $procedures = [
            // Gestión de empleados
            'sp_get_employees',
            'sp_get_employee_counts',
            'sp_create_employee',
            'sp_update_employee',
            'sp_soft_delete_employee',
            'sp_restore_employee',
            // Siniestros
            'sp_get_active_policies_for_sinister',
            'sp_create_sinister',
            'sp_update_sinister',
            'sp_update_sinister_status',
            'sp_add_sinister_comment',
            'sp_get_sinister_comments',
            'sp_get_sinister_multimedia',
            // Perfil de usuario
            'sp_update_user_basic',
            'sp_upsert_user_address',
            'sp_upsert_user_fiscal',
        ];

        foreach ($procedures as $procedure) {
            DB::unprepared("DROP PROCEDURE IF EXISTS {$procedure}");
        }
    }
};
