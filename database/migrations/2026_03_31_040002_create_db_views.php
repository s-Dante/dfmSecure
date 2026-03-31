<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     * Crea las 8 Views del proyecto dfmSecure.
     */
    public function up(): void
    {
        // ─────────────────────────────────────────────────────────────────────
        // VIEW 1: vw_active_plans
        //  → Planes activos con su información JSON desempaquetada.
        //  → Usada en: welcome.blade.php (sección de planes de seguro)
        // ─────────────────────────────────────────────────────────────────────
        DB::statement("DROP VIEW IF EXISTS vw_active_plans");
        DB::statement("
            CREATE VIEW vw_active_plans AS
            SELECT
                id,
                name,
                status,
                price,
                info,
                JSON_UNQUOTE(JSON_EXTRACT(info, '$.description'))    AS description,
                JSON_UNQUOTE(JSON_EXTRACT(info, '$.coverages'))       AS coverages,
                JSON_UNQUOTE(JSON_EXTRACT(info, '$.deductible'))      AS deductible,
                created_at
            FROM plans
            WHERE status  = 'active'
              AND deleted_at IS NULL
            ORDER BY price ASC
        ");

        // ─────────────────────────────────────────────────────────────────────
        // VIEW 2: vw_insured_dashboard
        //  → Resumen del asegurado: pólizas activas, vehículos, siniestros abiertos.
        //  → Usada en: dashboard.blade.php (rol: insured)
        // ─────────────────────────────────────────────────────────────────────
        DB::statement("DROP VIEW IF EXISTS vw_insured_dashboard");
        DB::statement("
            CREATE VIEW vw_insured_dashboard AS
            SELECT
                u.id                                            AS user_id,
                CONCAT(u.name, ' ', u.father_lastname)          AS full_name,
                u.email,
                COUNT(DISTINCT po.id)                           AS total_policies,
                SUM(CASE WHEN po.status = 'active'
                     AND po.end_validity >= CURDATE()
                     THEN 1 ELSE 0 END)                         AS active_policies,
                COUNT(DISTINCT iv.id)                           AS total_vehicles,
                COUNT(DISTINCT s.id)                            AS total_sinisters,
                SUM(CASE WHEN s.status NOT IN ('closed','resolved')
                     AND s.deleted_at IS NULL
                     THEN 1 ELSE 0 END)                         AS open_sinisters
            FROM users u
            LEFT JOIN policies       po ON po.insured_id = u.id AND po.deleted_at IS NULL
            LEFT JOIN insured_vehicles iv ON iv.user_id  = u.id AND iv.deleted_at IS NULL
            LEFT JOIN sinisters        s  ON s.policy_id = po.id AND s.deleted_at IS NULL
            WHERE u.deleted_at IS NULL
            GROUP BY u.id, u.name, u.father_lastname, u.email
        ");

        // ─────────────────────────────────────────────────────────────────────
        // VIEW 3: vw_adjuster_dashboard
        //  → Siniestros asignados al ajustador, con estado y datos de la póliza.
        //  → Usada en: dashboard.blade.php (rol: adjuster)
        // ─────────────────────────────────────────────────────────────────────
        DB::statement("DROP VIEW IF EXISTS vw_adjuster_dashboard");
        DB::statement("
            CREATE VIEW vw_adjuster_dashboard AS
            SELECT
                s.id                                                        AS sinister_id,
                s.adjuster_id                                               AS user_id,
                s.occur_date,
                s.report_date,
                s.close_date,
                s.status                                                    AS sinister_status,
                s.description,
                s.location,
                po.folio                                                    AS policy_folio,
                po.status                                                   AS policy_status,
                po.end_validity                                             AS policy_end,
                CONCAT(u.name, ' ', u.father_lastname)                      AS insured_name,
                CONCAT(vm.brand, ' ', vm.sub_brand, ' ', vm.year)          AS vehicle_info,
                iv.plate
            FROM sinisters s
            JOIN policies          po ON po.id = s.policy_id
            JOIN users              u ON u.id  = po.insured_id
            JOIN insured_vehicles   iv ON iv.id = po.vehicle_id
            JOIN vehicle_models    vm ON vm.id = iv.vehicle_model_id
            WHERE s.deleted_at IS NULL
        ");

        // ─────────────────────────────────────────────────────────────────────
        // VIEW 4: vw_supervisor_dashboard
        //  → Siniestros bajo supervisión con ajustador asignado y estado.
        //  → Usada en: dashboard.blade.php (rol: supervisor)
        // ─────────────────────────────────────────────────────────────────────
        DB::statement("DROP VIEW IF EXISTS vw_supervisor_dashboard");
        DB::statement("
            CREATE VIEW vw_supervisor_dashboard AS
            SELECT
                s.id                                                        AS sinister_id,
                s.supervisor_id                                             AS user_id,
                s.occur_date,
                s.report_date,
                s.close_date,
                s.status,
                s.description,
                s.location,
                CONCAT(adj.name, ' ', adj.father_lastname)                  AS adjuster_name,
                adj.email                                                   AS adjuster_email,
                po.folio                                                    AS policy_folio,
                CONCAT(u.name, ' ', u.father_lastname)                      AS insured_name,
                CONCAT(vm.brand, ' ', vm.sub_brand, ' ', vm.year)          AS vehicle_info
            FROM sinisters s
            JOIN users              adj ON adj.id = s.adjuster_id
            JOIN policies             po ON po.id = s.policy_id
            JOIN users                 u ON u.id  = po.insured_id
            JOIN insured_vehicles     iv ON iv.id = po.vehicle_id
            JOIN vehicle_models       vm ON vm.id = iv.vehicle_model_id
            WHERE s.deleted_at IS NULL
              AND s.supervisor_id IS NOT NULL
        ");

        // ─────────────────────────────────────────────────────────────────────
        // VIEW 5: vw_admin_dashboard
        //  → Conteos globales para el panel del administrador.
        //  → Usada en: dashboard.blade.php (rol: admin)
        // ─────────────────────────────────────────────────────────────────────
        DB::statement("DROP VIEW IF EXISTS vw_admin_dashboard");
        DB::statement("
            CREATE VIEW vw_admin_dashboard AS
            SELECT
                (SELECT COUNT(*) FROM users         WHERE deleted_at IS NULL)   AS total_users,
                (SELECT COUNT(*) FROM policies      WHERE deleted_at IS NULL)   AS total_policies,
                (SELECT COUNT(*) FROM sinisters     WHERE deleted_at IS NULL)   AS total_sinisters,
                (SELECT COUNT(*) FROM sinisters     WHERE status = 'reported'
                                                      AND deleted_at IS NULL)   AS reported_sinisters,
                (SELECT COUNT(*) FROM sinisters     WHERE status = 'in_review'
                                                      AND deleted_at IS NULL)   AS in_review_sinisters,
                (SELECT COUNT(*) FROM sinisters     WHERE status IN ('closed','resolved')
                                                      AND deleted_at IS NULL)   AS closed_sinisters,
                (SELECT COUNT(*) FROM insured_vehicles WHERE deleted_at IS NULL) AS total_vehicles,
                (SELECT COUNT(*) FROM plans         WHERE status = 'active'
                                                      AND deleted_at IS NULL)   AS active_plans
        ");

        // ─────────────────────────────────────────────────────────────────────
        // VIEW 6: vw_sinister_detail
        //  → Vista completa del siniestro: póliza, vehículo, asegurado, ajustador.
        //  → Usada en: sinister-detail.blade.php
        // ─────────────────────────────────────────────────────────────────────
        DB::statement("DROP VIEW IF EXISTS vw_sinister_detail");
        DB::statement("
            CREATE VIEW vw_sinister_detail AS
            SELECT
                s.id                                                            AS sinister_id,
                s.occur_date,
                s.report_date,
                s.close_date,
                s.description,
                s.location,
                s.status                                                        AS sinister_status,
                -- Póliza
                po.folio                                                        AS policy_folio,
                po.status                                                        AS policy_status,
                po.begin_validity,
                po.end_validity,
                -- Plan
                pl.name                                                         AS plan_name,
                pl.price                                                        AS plan_price,
                -- Vehículo
                iv.vin,
                iv.plate,
                vm.brand,
                vm.sub_brand,
                vm.year                                                         AS vehicle_year,
                vm.color,
                -- Asegurado
                u.id                                                            AS insured_id,
                CONCAT(u.name, ' ', u.father_lastname)                          AS insured_name,
                u.email                                                         AS insured_email,
                u.phone                                                         AS insured_phone,
                -- Ajustador
                adj.id                                                          AS adjuster_id,
                CONCAT(adj.name, ' ', adj.father_lastname)                      AS adjuster_name,
                adj.email                                                       AS adjuster_email,
                -- Supervisor
                sup.id                                                          AS supervisor_id,
                CONCAT(sup.name, ' ', sup.father_lastname)                      AS supervisor_name
            FROM sinisters s
            JOIN policies           po  ON po.id  = s.policy_id
            JOIN plans              pl  ON pl.id  = po.plan_id
            JOIN insured_vehicles   iv  ON iv.id  = po.vehicle_id
            JOIN vehicle_models     vm  ON vm.id  = iv.vehicle_model_id
            JOIN users              u   ON u.id   = po.insured_id
            JOIN users              adj ON adj.id = s.adjuster_id
            LEFT JOIN users         sup ON sup.id = s.supervisor_id
            WHERE s.deleted_at IS NULL
        ");

        // ─────────────────────────────────────────────────────────────────────
        // VIEW 7: vw_user_profile
        //  → Datos completos del perfil: usuario + dirección + datos fiscales.
        //  → Usada en: profile.blade.php
        // ─────────────────────────────────────────────────────────────────────
        DB::statement("DROP VIEW IF EXISTS vw_user_profile");
        DB::statement("
            CREATE VIEW vw_user_profile AS
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
                u.profile_picture_url,
                u.email_verified_at,
                u.created_at                    AS member_since,
                -- Rol
                r.name                          AS role_name,
                -- Dirección
                a.type                          AS address_type,
                a.country,
                a.state,
                a.city,
                a.neighborhood,
                a.street,
                a.external_number,
                a.internal_number,
                a.zip_code,
                -- Datos fiscales
                f.rfc,
                f.fiscal_type,
                f.company_name,
                f.tax_regime
            FROM users u
            JOIN roles              r   ON r.id = u.role_id
            LEFT JOIN addresses     a   ON a.id = u.address_id
            LEFT JOIN fiscals       f   ON f.user_id = u.id
            WHERE u.deleted_at IS NULL
        ");

        // ─────────────────────────────────────────────────────────────────────
        // VIEW 8: vw_policy_coverage
        //  → Pólizas con cobertura detallada del plan.
        //  → Usada en: consultation.blade.php
        // ─────────────────────────────────────────────────────────────────────
        DB::statement("DROP VIEW IF EXISTS vw_policy_coverage");
        DB::statement("
            CREATE VIEW vw_policy_coverage AS
            SELECT
                po.id                                                           AS policy_id,
                po.folio,
                po.status                                                       AS policy_status,
                po.begin_validity,
                po.end_validity,
                DATEDIFF(po.end_validity, CURDATE())                            AS days_remaining,
                -- Asegurado
                u.id                                                            AS insured_id,
                CONCAT(u.name, ' ', u.father_lastname)                          AS insured_name,
                u.email                                                         AS insured_email,
                -- Vehículo
                iv.vin,
                iv.plate,
                CONCAT(vm.brand, ' ', vm.sub_brand, ' ', vm.year)              AS vehicle_info,
                vm.color,
                -- Plan y cobertura
                pl.name                                                         AS plan_name,
                pl.price                                                        AS plan_price,
                pl.info                                                         AS plan_details
            FROM policies po
            JOIN users              u   ON u.id  = po.insured_id
            JOIN insured_vehicles   iv  ON iv.id = po.vehicle_id
            JOIN vehicle_models     vm  ON vm.id = iv.vehicle_model_id
            JOIN plans              pl  ON pl.id = po.plan_id
            WHERE po.deleted_at IS NULL
              AND u.deleted_at  IS NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP VIEW IF EXISTS vw_policy_coverage");
        DB::statement("DROP VIEW IF EXISTS vw_user_profile");
        DB::statement("DROP VIEW IF EXISTS vw_sinister_detail");
        DB::statement("DROP VIEW IF EXISTS vw_admin_dashboard");
        DB::statement("DROP VIEW IF EXISTS vw_supervisor_dashboard");
        DB::statement("DROP VIEW IF EXISTS vw_adjuster_dashboard");
        DB::statement("DROP VIEW IF EXISTS vw_insured_dashboard");
        DB::statement("DROP VIEW IF EXISTS vw_active_plans");
    }
};
