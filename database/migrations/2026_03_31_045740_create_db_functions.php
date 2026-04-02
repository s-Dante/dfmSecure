<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // FUNCTIONS
        // ─────────────────────────────────────────────────────────────────────
        /**
         * fn_policy_is_active
         * Determina si una poliza esta vigente y activa.
         * Retorna 1 si esta activa, 0 si no.
         */
        $fnPolicyIsActive = <<<'SQL'
            DROP FUNCTION IF EXISTS fn_policy_is_active;
            CREATE FUNCTION fn_policy_is_active(
                p_policy_id BIGINT UNSIGNED
            )
            RETURNS TINYINT(1)
            NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_count INT DEFAULT 0;

                SELECT COUNT(*)
                INTO v_count
                FROM policies
                WHERE id = p_policy_id
                    AND status = 'active'
                    AND end_validity >= CURDATE()
                    AND deleted_at IS NULL;

                RETURN IF(v_count > 0, 1, 0);
            END
        SQL;
        DB::unprepared($fnPolicyIsActive);

        /**
         * fn_policy_deadline
         * Determina los dias restantes para que una poliza expire.
         * Retorna numero positivo si aun vigente, negativo si ya vencio.
         */
        $fnPolicyDeadline = <<<'SQL'
            DROP FUNCTION IF EXISTS fn_policy_deadline;
            CREATE FUNCTION fn_policy_deadline(
                p_policy_id BIGINT UNSIGNED
            )
            RETURNS INT
            NOT DETERMINISTIC
            READS SQL DATA
            BEGIN
                DECLARE v_end_validity DATE;
                DECLARE v_days_left    INT;

                SELECT end_validity
                INTO v_end_validity
                FROM policies
                WHERE id = p_policy_id
                    AND deleted_at IS NULL;

                SET v_days_left = DATEDIFF(v_end_validity, CURDATE());

                RETURN v_days_left;
            END
        SQL;
        DB::unprepared($fnPolicyDeadline);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $functions = [
            'fn_policy_is_active',
            'fn_policy_deadline'
        ];

        foreach ($functions as $function) {
            DB::unprepared("DROP FUNCTION IF EXISTS {$function}");
        }
    }
};
