<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Crea las 2 Functions de lógica de negocio del proyecto dfmSecure.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // FUNCTION 1: fn_sinister_deadline
        //  → Recibe el ID de un siniestro.
        //  → Calcula los días restantes para cerrar el siniestro,
        //    considerando un plazo de 30 días a partir de la fecha de reporte.
        //  → Retorna INT:
        //      > 0  → días restantes antes del vencimiento
        //      = 0  → vence hoy
        //      < 0  → siniestro vencido (días de retraso en negativo)
        //  → Retorna NULL si el siniestro no existe o ya tiene close_date.
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP FUNCTION IF EXISTS fn_sinister_deadline");

        DB::unprepared("
            CREATE FUNCTION fn_sinister_deadline(p_sinister_id BIGINT UNSIGNED)
            RETURNS INT
            READS SQL DATA
            DETERMINISTIC
            BEGIN
                DECLARE v_report_date DATE;
                DECLARE v_close_date  DATE;
                DECLARE v_deadline    DATE;
                DECLARE v_days_left   INT;

                SELECT report_date, close_date
                INTO   v_report_date, v_close_date
                FROM   sinisters
                WHERE  id = p_sinister_id
                LIMIT  1;

                -- Si el siniestro no existe, retornar NULL
                IF v_report_date IS NULL THEN
                    RETURN NULL;
                END IF;

                -- Si ya está cerrado, ya no tiene plazo pendiente
                IF v_close_date IS NOT NULL THEN
                    RETURN NULL;
                END IF;

                -- Calcular la fecha límite (30 días desde el reporte)
                SET v_deadline   = DATE_ADD(v_report_date, INTERVAL 30 DAY);
                SET v_days_left  = DATEDIFF(v_deadline, CURDATE());

                RETURN v_days_left;
            END
        ");

        // ─────────────────────────────────────────────────────────────────────
        // FUNCTION 2: fn_policy_is_active
        //  → Recibe el ID de una póliza.
        //  → Retorna TINYINT(1):
        //      1 → La póliza está activa y vigente (status='active' y end_validity >= hoy)
        //      0 → La póliza no existe, no está activa o está vencida
        //  → Usada por el Trigger trg_validate_policy_on_sinister y el AdjusterController
        // ─────────────────────────────────────────────────────────────────────
        DB::unprepared("DROP FUNCTION IF EXISTS fn_policy_is_active");

        DB::unprepared("
            CREATE FUNCTION fn_policy_is_active(p_policy_id BIGINT UNSIGNED)
            RETURNS TINYINT(1)
            READS SQL DATA
            DETERMINISTIC
            BEGIN
                DECLARE v_count INT DEFAULT 0;

                SELECT COUNT(*)
                INTO   v_count
                FROM   policies
                WHERE  id           = p_policy_id
                  AND  status       = 'active'
                  AND  end_validity >= CURDATE();

                RETURN IF(v_count > 0, 1, 0);
            END
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared("DROP FUNCTION IF EXISTS fn_sinister_deadline");
        DB::unprepared("DROP FUNCTION IF EXISTS fn_policy_is_active");
    }
};
