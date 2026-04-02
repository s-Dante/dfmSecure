<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // Procedure: sp_generate_data_dictionary
        // ─────────────────────────────────────────────────────────────────────
        $spGenerateDataDictionary = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_generate_data_dictionary;
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
        SQL;
        DB::unprepared($spGenerateDataDictionary);


        // ─────────────────────────────────────────────────────────────────────
        // Procedures para autenticacion
        // ─────────────────────────────────────────────────────────────────────
        /**
         * Encontrar al usuario por su email
         */
        $spFindUserByEmail = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_find_user_by_email;
            CREATE PROCEDURE sp_find_user_by_email (
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
            END
        SQL;
        DB::unprepared($spFindUserByEmail);

        /**
         * Registrar al usuario
         */
        $spRegisterUser = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_register_user;
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
            END
        SQL;
        DB::unprepared($spRegisterUser);

        /**
         * Mandar token de reseteo
         */
        $spSendResetToken = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_send_reset_token;
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
            END
        SQL;
        DB::unprepared($spSendResetToken);

        /**
         * Validar el token de reseteo
         */
        $spVerifyResetToken = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_verify_reset_token;
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
            END
        SQL;
        DB::unprepared($spVerifyResetToken);

        /**
         * Cambiar contraseña
         */
        $spChangePassword = <<<'SQL'
            DROP PROCEDURE IF EXISTS sp_change_password;
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
            END
        SQL;
        DB::unprepared($spChangePassword);
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
            'sp_change_password'
        ];

        foreach ($procedures as $procedure) {
            DB::unprepared("DROP PROCEDURE IF EXISTS {$procedure}");
        }
    }
};
