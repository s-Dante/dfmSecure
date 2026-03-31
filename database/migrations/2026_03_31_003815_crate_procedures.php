<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //SP para obtener el diccionario de datos
        $procedure = <<<SQL
            DROP PROCEDURE IF EXISTS sp_generate_data_dictionary;
            CREATE PROCEDURE sp_generate_data_dictionary()
            BEGIN
                SELECT
                    C.TABLE_NAME AS 'Tabla',
                    C.COLUMN_NAME AS 'Columna',
                    C.COLUMN_TYPE AS 'Tipo',
                    C.IS_NULLABLE AS 'Nulable',

                    CASE
                        WHEN C.COLUMN_KEY = 'PRI' THEN 'PK (Llave Primaria)'
                        WHEN KCU.REFERENCED_TABLE_NAME IS NOT NULL THEN 'FK (Llave Foranea)'
                        WHEN C.COLUMN_KEY = 'UNI' THEN 'UK (Llave única)'
                        WHEN C.COLUMN_KEY = 'MUL' THEN 'Indice'
                        ELSE 'Atributo normal'
                    END AS 'Tipo de llave',

                    IFNULL(
                        CONCAT('Ref: ', KCU.REFERENCED_TABLE_NAME, ' -> (', KCU.REFERENCED_COLUMN_NAME, ')'),
                        'N/A'
                    ) AS 'Relación FK',

                    IFNULL(C.COLUMN_DEFAULT, 'Ninguno') AS 'Default',
                    C.COLUMN_COMMENT AS 'Descripcion'
                FROM
                    information_schema.COLUMNS C
                LEFT JOIN
                    information_schema.KEY_COLUMN_USAGE KCU
                    ON C.TABLE_SCHEMA = KCU.TABLE_SCHEMA
                    AND C.TABLE_NAME = KCU.TABLE_NAME
                    AND C.COLUMN_NAME = KCU.COLUMN_NAME
                WHERE
                    C.TABLE_SCHEMA = DATABASE()
                    AND C.TABLE_NAME IN (
                        'roles', 'addresses', 'users', 'fiscals', 'vehicle_models', 'insured_vehicles',
                        'plans', 'policies', 'sinisters', 'sinister_multimedia', 'sinister_comments',
                        'password_reset_tokens', 'sessions', 'cache', 'cache_locks', 'jobs', 'jobs_batches',
                        'failed_jobs'
                    )
                ORDER BY
                    C.TABLE_NAME ASC,
                    C.ORDINAL_POSITION ASC;
            END
        SQL;
        DB::unprepared($procedure);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
