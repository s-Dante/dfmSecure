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
        // TRIGGERS PARA POLIZAS
        // ─────────────────────────────────────────────────────────────────────
        /**
         * trg_generate_policy_number
         * Genera el numero de poliza en formato POL-YYYYMMDD-XXXX
         * usando el timestamp y un numero aleatorio para que sea unico.
         */
        $trgGeneratePolicyNumber = <<<'SQL'
            DROP TRIGGER IF EXISTS trg_generate_policy_number;
            CREATE TRIGGER trg_generate_policy_number
            BEFORE INSERT ON policies
            FOR EACH ROW
            BEGIN
                IF NEW.policy_number IS NULL THEN
                    SET NEW.policy_number = CONCAT(
                        'POL-',
                        DATE_FORMAT(NOW(), '%Y%m%d'),
                        '-',
                        LPAD(FLOOR(RAND() * 9000) + 1000, 4, '0')
                    );
                END IF;
            END
        SQL;
        DB::unprepared($trgGeneratePolicyNumber);

        /**
         * Trigger: trg_validate_policy_in_sinister
         * Valida que la poliza exista, este activa y no haya vencido
         * antes de permitir insertar un siniestro.
         */
        $trgValidatePolicyInSinister = <<<'SQL'
            DROP TRIGGER IF EXISTS trg_validate_policy_in_sinister;
            CREATE TRIGGER trg_validate_policy_in_sinister
            BEFORE INSERT ON sinisters
            FOR EACH ROW
            BEGIN
                DECLARE v_status       VARCHAR(255);
                DECLARE v_end_validity DATE;

                SELECT status, end_validity
                INTO v_status, v_end_validity
                FROM policies
                WHERE id = NEW.policy_id
                LIMIT 1;

                IF v_status IS NULL THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Error: La poliza referenciada no existe.';
                END IF;

                IF v_status <> 'active' THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Error: No se puede registrar un siniestro. La poliza no esta activa.';
                END IF;

                IF v_end_validity < CURDATE() THEN
                    SIGNAL SQLSTATE '45000'
                        SET MESSAGE_TEXT = 'Error: No se puede registrar un siniestro. La poliza esta vencida.';
                END IF;
            END
        SQL;
        DB::unprepared($trgValidatePolicyInSinister);

        // ─────────────────────────────────────────────────────────────────────
        // TRIGGERS PARA SINIESTROS
        // ─────────────────────────────────────────────────────────────────────
        /**
         * trg_generate_sinister_number
         * Genera el numero de siniestro en formato SIN-YYYYMMDD-XXXX
         */
        $trgGenerateSinisterNumber = <<<'SQL'
            DROP TRIGGER IF EXISTS trg_generate_sinister_number;
            CREATE TRIGGER trg_generate_sinister_number
            BEFORE INSERT ON sinisters
            FOR EACH ROW
            BEGIN
                IF NEW.sinister_number IS NULL THEN
                    SET NEW.sinister_number = CONCAT(
                        'SIN-',
                        DATE_FORMAT(NOW(), '%Y%m%d'),
                        '-',
                        LPAD(FLOOR(RAND() * 9000) + 1000, 4, '0')
                    );
                END IF;
            END
        SQL;
        DB::unprepared($trgGenerateSinisterNumber);

        /**
         * Trigger: trg_auto_set_close_date
         * Sella automaticamente la fecha de cierre cuando el siniestro
         * pasa a un estado de resolucion final y aun no tiene close_date.
         */
        $trgAutoSetCloseDate = <<<'SQL'
            DROP TRIGGER IF EXISTS trg_auto_set_close_date;
            CREATE TRIGGER trg_auto_set_close_date
            BEFORE UPDATE ON sinisters
            FOR EACH ROW
            BEGIN
                IF NEW.status IN (
                        'closed',
                        'approved',
                        'approved_with_deductible',
                        'approved_without_deductible',
                        'applies_payment_for_repairs',
                        'total_loss'
                    )
                    AND OLD.status NOT IN (
                        'closed',
                        'approved',
                        'approved_with_deductible',
                        'approved_without_deductible',
                        'applies_payment_for_repairs',
                        'total_loss'
                    )
                    AND NEW.close_date IS NULL
                THEN
                    SET NEW.close_date = CURDATE();
                END IF;
            END
        SQL;
        DB::unprepared($trgAutoSetCloseDate);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $triggers = [
            'trg_generate_policy_number',
            'trg_validate_policy_in_sinister',
            'trg_generate_sinister_number',
            'trg_auto_set_close_date',
        ];

        foreach ($triggers as $trigger) {
            DB::unprepared("DROP TRIGGER IF EXISTS {$trigger}");
        }
    }
};
