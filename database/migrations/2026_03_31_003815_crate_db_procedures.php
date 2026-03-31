<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Crea todos los Stored Procedures del proyecto dfmSecure.
     *
     * Categorías:
     *  - Diccionario de datos
     *  - Autenticación (Auth)
     *  - Dashboards por rol
     *  - Siniestros
     *  - Perfil de usuario
     *  - Planes
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // SP 1: sp_generate_data_dictionary
        //  → Genera el diccionario de datos completo de las tablas del proyecto.
        //  → Uso: CALL sp_generate_data_dictionary();
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_generate_data_dictionary");
        DB::unprepared("
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
            END
        ");

        // ═════════════════════════════════════════════════════════════════════
        //  AUTENTICACIÓN
        // ═════════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────────────────────────────
        // SP 2: sp_find_user_by_email
        //  → Busca un usuario por email para el proceso de login.
        //  → Retorna todos los campos necesarios para Auth::login() en PHP.
        //  → El controlador valida el hash con Hash::check() en PHP.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_find_user_by_email");
        DB::unprepared("
            CREATE PROCEDURE sp_find_user_by_email(IN p_email VARCHAR(80))
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
                    u.role_id,
                    u.address_id,
                    u.email_verified_at,
                    u.remember_token,
                    u.created_at,
                    u.updated_at,
                    u.deleted_at
                FROM users u
                WHERE u.email      = p_email
                  AND u.deleted_at IS NULL
                LIMIT 1;
            END
        ");

        // ─────────────────────────────────────────────────────────────────────
        // SP 3: sp_register_user
        //  → Registra un nuevo usuario con el rol indicado.
        //  → El hash de la contraseña es generado en PHP (Hash::make) antes de llamar el SP.
        //  → Retorna el id y email del usuario creado.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_register_user");
        DB::unprepared("
            CREATE PROCEDURE sp_register_user(
                IN p_name           VARCHAR(80),
                IN p_father_last    VARCHAR(50),
                IN p_mother_last    VARCHAR(50),
                IN p_username       VARCHAR(30),
                IN p_email          VARCHAR(80),
                IN p_phone          VARCHAR(20),
                IN p_birth_date     DATE,
                IN p_password_hash  VARCHAR(255),
                IN p_gender         VARCHAR(20),
                IN p_role_id        BIGINT UNSIGNED
            )
            BEGIN
                INSERT INTO users (
                    name, father_lastname, mother_lastname,
                    username, email, phone,
                    birth_date, password, gender,
                    role_id,
                    created_at, updated_at
                ) VALUES (
                    p_name, p_father_last, p_mother_last,
                    p_username, p_email, p_phone,
                    p_birth_date, p_password_hash, p_gender,
                    p_role_id,
                    NOW(), NOW()
                );

                SELECT LAST_INSERT_ID() AS id, p_email AS email;
            END
        ");

        // ─────────────────────────────────────────────────────────────────────
        // SP 4: sp_send_reset_token
        //  → Inserta o actualiza el token de recuperación de contraseña.
        //  → Primero verifica que el email existe en users.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_send_reset_token");
        DB::unprepared("
            CREATE PROCEDURE sp_send_reset_token(
                IN p_email  VARCHAR(80),
                IN p_token  VARCHAR(255)
            )
            BEGIN
                DECLARE v_user_exists INT DEFAULT 0;

                SELECT COUNT(*) INTO v_user_exists
                FROM users
                WHERE email      = p_email
                  AND deleted_at IS NULL;

                IF v_user_exists = 0 THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'El correo no esta registrado en el sistema.';
                END IF;

                INSERT INTO password_reset_tokens (email, token, created_at)
                VALUES (p_email, p_token, NOW())
                ON DUPLICATE KEY UPDATE
                    token      = p_token,
                    created_at = NOW();
            END
        ");

        // ─────────────────────────────────────────────────────────────────────
        // SP 5: sp_verify_reset_token
        //  → Valida que el token sea correcto para el email dado.
        //  → Retorna el registro si es válido, vacío si no.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_verify_reset_token");
        DB::unprepared("
            CREATE PROCEDURE sp_verify_reset_token(
                IN p_email  VARCHAR(80),
                IN p_token  VARCHAR(255)
            )
            BEGIN
                SELECT email, token, created_at
                FROM password_reset_tokens
                WHERE email = p_email
                  AND token = p_token
                LIMIT 1;
            END
        ");

        // ─────────────────────────────────────────────────────────────────────
        // SP 6: sp_change_password
        //  → Actualiza la contraseña de un usuario y elimina su reset token.
        //  → El nuevo hash es generado en PHP (Hash::make) antes de llamar el SP.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_change_password");
        DB::unprepared("
            CREATE PROCEDURE sp_change_password(
                IN p_email          VARCHAR(80),
                IN p_password_hash  VARCHAR(255)
            )
            BEGIN
                UPDATE users
                SET    password   = p_password_hash,
                       updated_at = NOW()
                WHERE  email      = p_email
                  AND  deleted_at IS NULL;

                DELETE FROM password_reset_tokens
                WHERE email = p_email;

                SELECT ROW_COUNT() AS affected_rows;
            END
        ");

        // ═════════════════════════════════════════════════════════════════════
        //  DASHBOARDS POR ROL
        // ═════════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────────────────────────────
        // SP 7: sp_get_insured_dashboard
        //  → Retorna el resumen del dashboard para el asegurado.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_insured_dashboard");
        DB::unprepared("
            CREATE PROCEDURE sp_get_insured_dashboard(IN p_user_id BIGINT UNSIGNED)
            BEGIN
                SELECT * FROM vw_insured_dashboard
                WHERE user_id = p_user_id;
            END
        ");

        // ─────────────────────────────────────────────────────────────────────
        // SP 8: sp_get_adjuster_dashboard
        //  → Retorna los siniestros asignados al ajustador.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_adjuster_dashboard");
        DB::unprepared("
            CREATE PROCEDURE sp_get_adjuster_dashboard(IN p_user_id BIGINT UNSIGNED)
            BEGIN
                SELECT * FROM vw_adjuster_dashboard
                WHERE user_id = p_user_id
                ORDER BY sinister_id DESC;
            END
        ");

        // ─────────────────────────────────────────────────────────────────────
        // SP 9: sp_get_supervisor_dashboard
        //  → Retorna los siniestros bajo supervisión del supervisor.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_supervisor_dashboard");
        DB::unprepared("
            CREATE PROCEDURE sp_get_supervisor_dashboard(IN p_user_id BIGINT UNSIGNED)
            BEGIN
                SELECT * FROM vw_supervisor_dashboard
                WHERE user_id = p_user_id
                ORDER BY sinister_id DESC;
            END
        ");

        // ─────────────────────────────────────────────────────────────────────
        // SP 10: sp_get_admin_dashboard
        //  → Retorna los conteos globales para el administrador.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_admin_dashboard");
        DB::unprepared("
            CREATE PROCEDURE sp_get_admin_dashboard()
            BEGIN
                SELECT * FROM vw_admin_dashboard;
            END
        ");

        // ═════════════════════════════════════════════════════════════════════
        //  SINIESTROS
        // ═════════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────────────────────────────
        // SP 11: sp_get_sinister_detail
        //  → Retorna el detalle completo de un siniestro.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_sinister_detail");
        DB::unprepared("
            CREATE PROCEDURE sp_get_sinister_detail(IN p_sinister_id BIGINT UNSIGNED)
            BEGIN
                SELECT * FROM vw_sinister_detail
                WHERE sinister_id = p_sinister_id;
            END
        ");

        // ─────────────────────────────────────────────────────────────────────
        // SP 12: sp_update_sinister_status
        //  → Actualiza el estado de un siniestro.
        //  → El trigger trg_auto_set_close_date sella close_date automáticamente.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_update_sinister_status");
        DB::unprepared("
            CREATE PROCEDURE sp_update_sinister_status(
                IN p_sinister_id BIGINT UNSIGNED,
                IN p_status      VARCHAR(255)
            )
            BEGIN
                UPDATE sinisters
                SET    status     = p_status,
                       updated_at = NOW()
                WHERE  id         = p_sinister_id
                  AND  deleted_at IS NULL;

                SELECT ROW_COUNT() AS affected_rows;
            END
        ");

        // ═════════════════════════════════════════════════════════════════════
        //  PERFIL DE USUARIO
        // ═════════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────────────────────────────
        // SP 13: sp_get_user_profile
        //  → Retorna el perfil completo del usuario desde vw_user_profile.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_user_profile");
        DB::unprepared("
            CREATE PROCEDURE sp_get_user_profile(IN p_user_id BIGINT UNSIGNED)
            BEGIN
                SELECT * FROM vw_user_profile
                WHERE id = p_user_id;
            END
        ");

        // ═════════════════════════════════════════════════════════════════════
        //  PLANES
        // ═════════════════════════════════════════════════════════════════════

        // ─────────────────────────────────────────────────────────────────────
        // SP 14: sp_get_active_plans
        //  → Retorna todos los planes activos desde vw_active_plans.
        //  → Usada en welcome.blade.php
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP PROCEDURE IF EXISTS sp_get_active_plans");
        DB::unprepared("
            CREATE PROCEDURE sp_get_active_plans()
            BEGIN
                SELECT * FROM vw_active_plans;
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $procedures = [
            'sp_generate_data_dictionary',
            'sp_find_user_by_email',
            'sp_register_user',
            'sp_send_reset_token',
            'sp_verify_reset_token',
            'sp_change_password',
            'sp_get_insured_dashboard',
            'sp_get_adjuster_dashboard',
            'sp_get_supervisor_dashboard',
            'sp_get_admin_dashboard',
            'sp_get_sinister_detail',
            'sp_update_sinister_status',
            'sp_get_user_profile',
            'sp_get_active_plans',
        ];

        foreach ($procedures as $proc) {
            DB::unprepared("DROP PROCEDURE IF EXISTS {$proc}");
        }
    }
};
