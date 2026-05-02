-- =============================================================================
-- VIEWS - dfmSecure
-- =============================================================================
-- Descripcion: Vistas de la base de datos del sistema de seguros.
-- Fuente:      database/migrations/2026_03_31_045749_create_db_views.php
-- Uso:         Ejecutar directamente en MySQL Workbench o cliente compatible.
--
-- IMPORTANTE: Las vistas vw_insured_data e vw_insured_vehicles dependen de
--             las funciones fn_policy_is_active y fn_policy_deadline.
--             Asegurarse de ejecutar functions.sql primero.
-- =============================================================================

-- =============================================================================
-- SECCION 1: PLANES
-- =============================================================================

/**
 * vw_active_plans
 * Planes activos con informacion JSON extraida para mostrar en welcome.blade.php
 *
 * Uso: SELECT * FROM vw_active_plans;
 */
DROP VIEW IF EXISTS vw_active_plans;
CREATE VIEW vw_active_plans AS
SELECT
    id,
    name,
    status,
    price,
    info,
    JSON_UNQUOTE(JSON_EXTRACT(info, '$.description')) AS description,
    JSON_UNQUOTE(JSON_EXTRACT(info, '$.coverages'))   AS coverages,
    JSON_UNQUOTE(JSON_EXTRACT(info, '$.deductible'))  AS deductible,
    created_at
FROM plans
WHERE status = 'active'
    AND deleted_at IS NULL
ORDER BY price ASC;


-- =============================================================================
-- SECCION 2: DASHBOARD - DATA (estadisticas numericas)
-- =============================================================================

/**
 * vw_insured_sinister_data
 * Conteos de siniestros por usuario asegurado (agrupado por user_id).
 * El controlador filtra por: WHERE user_id = auth()->id()
 *
 * Uso: SELECT * FROM vw_insured_sinister_data WHERE user_id = ?;
 */
DROP VIEW IF EXISTS vw_insured_sinister_data;
CREATE VIEW vw_insured_sinister_data AS
SELECT
    u.id                                                        AS user_id,
    COUNT(DISTINCT s.id)                                        AS total_sinisters,
    SUM(CASE WHEN s.status = 'in_review'
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS in_review_sinisters,
    SUM(CASE WHEN s.status IN (
            'approved',
            'approved_with_deductible',
            'approved_without_deductible',
            'applies_payment_for_repairs')
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS approved_sinisters,
    SUM(CASE WHEN s.status = 'rejected'
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS rejected_sinisters,
    COUNT(DISTINCT p.id)                                        AS total_policies,
    SUM(CASE WHEN p.status = 'active'
        AND p.end_validity >= CURDATE()
        AND p.deleted_at IS NULL THEN 1 ELSE 0 END)             AS active_policies,
    COUNT(DISTINCT iv.id)                                       AS total_vehicles
FROM users u
LEFT JOIN policies p   ON p.insured_id  = u.id AND p.deleted_at  IS NULL
LEFT JOIN sinisters s  ON s.policy_id   = p.id AND s.deleted_at  IS NULL
LEFT JOIN insured_vehicles iv ON iv.user_id = u.id AND iv.deleted_at IS NULL
WHERE u.deleted_at IS NULL
GROUP BY u.id;

/**
 * vw_adjuster_sinister_data
 * Conteos de siniestros por ajustador (agrupado por user_id).
 * El controlador filtra por: WHERE user_id = auth()->id()
 *
 * Uso: SELECT * FROM vw_adjuster_sinister_data WHERE user_id = ?;
 */
DROP VIEW IF EXISTS vw_adjuster_sinister_data;
CREATE VIEW vw_adjuster_sinister_data AS
SELECT
    u.id                                                        AS user_id,
    COUNT(DISTINCT s.id)                                        AS total_sinisters,
    SUM(CASE WHEN s.status = 'in_review'
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS in_review_sinisters,
    SUM(CASE WHEN s.status IN (
            'approved',
            'approved_with_deductible',
            'approved_without_deductible',
            'applies_payment_for_repairs')
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS approved_sinisters,
    SUM(CASE WHEN s.status = 'rejected'
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS rejected_sinisters
FROM users u
LEFT JOIN sinisters s ON s.adjuster_id = u.id AND s.deleted_at IS NULL
WHERE u.deleted_at IS NULL
GROUP BY u.id;

/**
 * vw_all_sinister_data
 * Conteos globales para Admin y Supervisor.
 *
 * Uso: SELECT * FROM vw_all_sinister_data;
 */
DROP VIEW IF EXISTS vw_all_sinister_data;
CREATE VIEW vw_all_sinister_data AS
SELECT
    COUNT(DISTINCT s.id)                                        AS total_sinisters,
    SUM(CASE WHEN s.status = 'in_review'
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS in_review_sinisters,
    SUM(CASE WHEN s.status IN (
            'approved',
            'approved_with_deductible',
            'approved_without_deductible',
            'applies_payment_for_repairs')
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS approved_sinisters,
    SUM(CASE WHEN s.status = 'rejected'
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS rejected_sinisters,
    SUM(CASE WHEN s.status = 'closed'
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS closed_sinisters,
    -- Siniestros registrados en el mes actual
    SUM(CASE WHEN MONTH(s.report_date) = MONTH(CURDATE())
        AND YEAR(s.report_date) = YEAR(CURDATE())
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS sinisters_this_month,
    -- Datos de usuarios (para el admin)
    (SELECT COUNT(*) FROM users u2
     JOIN roles r2 ON r2.id = u2.role_id
     WHERE r2.name = 'insured' AND u2.deleted_at IS NULL)       AS total_insured,
    (SELECT COUNT(*) FROM users u2
     JOIN roles r2 ON r2.id = u2.role_id
     WHERE r2.name = 'adjuster' AND u2.deleted_at IS NULL)      AS total_adjusters,
    (SELECT COUNT(*) FROM users u2
     JOIN roles r2 ON r2.id = u2.role_id
     WHERE r2.name = 'supervisor' AND u2.deleted_at IS NULL)    AS total_supervisors,
    -- Polizas activas
    (SELECT COUNT(*) FROM policies p2
     WHERE p2.status = 'active'
       AND p2.end_validity >= CURDATE()
       AND p2.deleted_at IS NULL)                                AS active_policies
FROM sinisters s
WHERE s.deleted_at IS NULL;


-- =============================================================================
-- SECCION 3: DASHBOARD - SINISTER CARDS (cards visuales)
-- =============================================================================

/**
 * vw_insured_sinisters
 * Cards de siniestros para el dashboard del asegurado.
 * El controlador filtra por: WHERE insured_id = auth()->id()
 *
 * Uso: SELECT * FROM vw_insured_sinisters WHERE insured_id = ?;
 */
DROP VIEW IF EXISTS vw_insured_sinisters;
CREATE VIEW vw_insured_sinisters AS
SELECT
    s.id                                                        AS sinister_id,
    s.folio                                                     AS sinister_folio,
    s.sinister_number                                           AS sinister_number,
    s.status                                                    AS sinister_status,
    p.insured_id,
    CONCAT(vm.brand, ' ', vm.sub_brand, ' ', vm.year)          AS vehicle_data,
    vm.color                                                    AS vehicle_color,
    (SELECT sm.path_file
     FROM sinister_multimedia sm
     WHERE sm.sinister_id = s.id
         AND sm.type = 'photo'
         AND sm.path_file IS NOT NULL
         AND sm.deleted_at IS NULL
     LIMIT 1)                                                   AS sinister_picture_path,
    (SELECT sm.blob_file
     FROM sinister_multimedia sm
     WHERE sm.sinister_id = s.id
         AND sm.type = 'photo'
         AND sm.blob_file IS NOT NULL
         AND sm.deleted_at IS NULL
     LIMIT 1)                                                   AS sinister_picture_blob,
    s.report_date,
    s.occur_date
FROM sinisters s
LEFT JOIN policies p          ON p.id  = s.policy_id        AND p.deleted_at  IS NULL
LEFT JOIN insured_vehicles iv ON iv.id = p.vehicle_id        AND iv.deleted_at IS NULL
LEFT JOIN vehicle_models vm   ON vm.id = iv.vehicle_model_id AND vm.deleted_at IS NULL
WHERE s.deleted_at IS NULL;

/**
 * vw_adjuster_sinisters
 * Cards de siniestros para el dashboard del ajustador.
 * El controlador filtra por: WHERE adjuster_id = auth()->id()
 *
 * Uso: SELECT * FROM vw_adjuster_sinisters WHERE adjuster_id = ?;
 */
DROP VIEW IF EXISTS vw_adjuster_sinisters;
CREATE VIEW vw_adjuster_sinisters AS
SELECT
    s.id                                                        AS sinister_id,
    s.folio                                                     AS sinister_folio,
    s.status                                                    AS sinister_status,
    s.adjuster_id,
    CONCAT(vm.brand, ' ', vm.sub_brand, ' ', vm.year)          AS vehicle_data,
    vm.color                                                    AS vehicle_color,
    (SELECT sm.path_file
     FROM sinister_multimedia sm
     WHERE sm.sinister_id = s.id
         AND sm.type = 'photo'
         AND sm.path_file IS NOT NULL
         AND sm.deleted_at IS NULL
     LIMIT 1)                                                   AS sinister_picture_path,
    (SELECT sm.blob_file
     FROM sinister_multimedia sm
     WHERE sm.sinister_id = s.id
         AND sm.type = 'photo'
         AND sm.blob_file IS NOT NULL
         AND sm.deleted_at IS NULL
     LIMIT 1)                                                   AS sinister_picture_blob,
    s.report_date,
    s.occur_date
FROM sinisters s
LEFT JOIN policies p          ON p.id  = s.policy_id        AND p.deleted_at  IS NULL
LEFT JOIN insured_vehicles iv ON iv.id = p.vehicle_id        AND iv.deleted_at IS NULL
LEFT JOIN vehicle_models vm   ON vm.id = iv.vehicle_model_id AND vm.deleted_at IS NULL
WHERE s.deleted_at IS NULL;

/**
 * vw_all_sinisters
 * Cards de todos los siniestros para Admin y Supervisor.
 *
 * Uso: SELECT * FROM vw_all_sinisters;
 */
DROP VIEW IF EXISTS vw_all_sinisters;
CREATE VIEW vw_all_sinisters AS
SELECT
    s.id                                                        AS sinister_id,
    s.folio                                                     AS sinister_folio,
    s.status                                                    AS sinister_status,
    p.insured_id,
    s.adjuster_id,
    s.supervisor_id,
    CONCAT(vm.brand, ' ', vm.sub_brand, ' ', vm.year)          AS vehicle_data,
    vm.color                                                    AS vehicle_color,
    CONCAT(ua.name, ' ', ua.father_lastname)                   AS adjuster_name,
    (SELECT sm.path_file
     FROM sinister_multimedia sm
     WHERE sm.sinister_id = s.id
         AND sm.type = 'photo'
         AND sm.path_file IS NOT NULL
         AND sm.deleted_at IS NULL
     LIMIT 1)                                                   AS sinister_picture_path,
    (SELECT sm.blob_file
     FROM sinister_multimedia sm
     WHERE sm.sinister_id = s.id
         AND sm.type = 'photo'
         AND sm.blob_file IS NOT NULL
         AND sm.deleted_at IS NULL
     LIMIT 1)                                                   AS sinister_picture_blob,
    s.report_date,
    s.occur_date
FROM sinisters s
LEFT JOIN policies p          ON p.id  = s.policy_id        AND p.deleted_at  IS NULL
LEFT JOIN insured_vehicles iv ON iv.id = p.vehicle_id        AND iv.deleted_at IS NULL
LEFT JOIN vehicle_models vm   ON vm.id = iv.vehicle_model_id AND vm.deleted_at IS NULL
LEFT JOIN users ua            ON ua.id = s.adjuster_id       AND ua.deleted_at IS NULL
WHERE s.deleted_at IS NULL;


-- =============================================================================
-- SECCION 4: PERFIL - VISTAS POR ROL
-- =============================================================================

/**
 * vw_get_user_info
 * Datos generales del usuario para el perfil (todos los roles).
 * El controlador filtra por: WHERE id = auth()->id()
 *
 * Uso: SELECT * FROM vw_get_user_info WHERE id = ?;
 */
DROP VIEW IF EXISTS vw_get_user_info;
CREATE VIEW vw_get_user_info AS
SELECT
    u.id,
    u.name,
    u.father_lastname,
    u.mother_lastname,
    u.username,
    u.profile_picture_url,
    u.profile_picture_blob,
    u.email,
    u.phone,
    u.birth_date,
    TIMESTAMPDIFF(YEAR, u.birth_date, CURDATE())    AS age,
    u.gender,
    -- Rol
    r.name                                          AS role_name,
    -- Direccion
    a.type                                          AS address_type,
    a.country,
    a.state,
    a.city,
    a.neighborhood,
    a.street,
    a.external_number,
    a.internal_number,
    a.zip_code,
    -- Datos fiscales (aplica principalmente para asegurado)
    f.rfc,
    f.fiscal_type,
    f.company_name,
    f.tax_regime
FROM users u
LEFT JOIN roles     r ON r.id      = u.role_id    AND r.deleted_at IS NULL
LEFT JOIN addresses a ON a.id      = u.address_id AND a.deleted_at IS NULL
LEFT JOIN fiscals   f ON f.user_id = u.id         AND f.deleted_at IS NULL
WHERE u.deleted_at IS NULL;

/**
 * vw_insured_data
 * Polizas y vehiculos del asegurado para la seccion de perfil.
 * El controlador filtra por: WHERE insured_id = auth()->id()
 *
 * NOTA: Depende de fn_policy_deadline (ejecutar functions.sql primero).
 *
 * Uso: SELECT * FROM vw_insured_data WHERE insured_id = ?;
 */
DROP VIEW IF EXISTS vw_insured_data;
CREATE VIEW vw_insured_data AS
SELECT
    po.id                                                       AS policy_id,
    po.insured_id,
    po.folio                                                    AS policy_folio,
    po.policy_number,
    po.status                                                   AS policy_status,
    po.begin_validity,
    po.end_validity,
    fn_policy_deadline(po.id)                                   AS days_left,
    pl.name                                                     AS plan_name,
    pl.price                                                    AS plan_price,
    CONCAT(vm.brand, ' ', vm.sub_brand, ' ', vm.year)          AS vehicle_data,
    vm.color                                                    AS vehicle_color,
    iv.plate
FROM policies po
LEFT JOIN insured_vehicles iv ON iv.id = po.vehicle_id       AND iv.deleted_at IS NULL
LEFT JOIN vehicle_models vm   ON vm.id = iv.vehicle_model_id AND vm.deleted_at IS NULL
LEFT JOIN plans pl            ON pl.id = po.plan_id          AND pl.deleted_at IS NULL
WHERE po.deleted_at IS NULL;

/**
 * vw_adjuster_data
 * Resumen de actividad del ajustador para el perfil.
 * El controlador filtra por: WHERE adjuster_id = auth()->id()
 *
 * Uso: SELECT * FROM vw_adjuster_data WHERE adjuster_id = ?;
 */
DROP VIEW IF EXISTS vw_adjuster_data;
CREATE VIEW vw_adjuster_data AS
SELECT
    s.adjuster_id,
    COUNT(DISTINCT s.id)                                        AS total_registered,
    SUM(CASE WHEN s.status = 'in_review'
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS in_review,
    SUM(CASE WHEN s.status IN (
            'approved',
            'approved_with_deductible',
            'approved_without_deductible',
            'applies_payment_for_repairs')
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS approved,
    SUM(CASE WHEN s.status = 'rejected'
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS rejected,
    SUM(CASE WHEN s.status = 'closed'
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS closed,
    -- Siniestros registrados este mes
    SUM(CASE WHEN MONTH(s.report_date) = MONTH(CURDATE())
        AND YEAR(s.report_date) = YEAR(CURDATE())
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS registered_this_month
FROM sinisters s
WHERE s.deleted_at IS NULL
GROUP BY s.adjuster_id;

/**
 * vw_supervisor_data
 * Resumen de actividad global para el supervisor.
 *
 * Uso: SELECT * FROM vw_supervisor_data WHERE supervisor_id = ?;
 */
DROP VIEW IF EXISTS vw_supervisor_data;
CREATE VIEW vw_supervisor_data AS
SELECT
    s.supervisor_id,
    COUNT(DISTINCT s.id)                                        AS cases_reviewed,
    SUM(CASE WHEN s.status NOT IN ('closed', 'rejected')
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS active_sinisters,
    SUM(CASE WHEN s.status IN ('closed', 'approved',
            'approved_with_deductible',
            'approved_without_deductible',
            'applies_payment_for_repairs',
            'total_loss', 'rejected')
        AND s.deleted_at IS NULL THEN 1 ELSE 0 END)             AS resolved_sinisters,
    -- Global (sin filtro de supervisor)
    (SELECT COUNT(*) FROM sinisters s2 WHERE s2.deleted_at IS NULL) AS all_sinisters_total,
    (SELECT COUNT(*) FROM sinisters s2
     WHERE s2.status NOT IN ('closed', 'rejected', 'total_loss',
         'approved', 'approved_with_deductible',
         'approved_without_deductible', 'applies_payment_for_repairs')
       AND s2.deleted_at IS NULL)                               AS all_sinisters_active
FROM sinisters s
WHERE s.deleted_at IS NULL
GROUP BY s.supervisor_id;

/**
 * vw_admin_data
 * KPIs globales para el dashboard del administrador.
 *
 * Uso: SELECT * FROM vw_admin_data;
 */
DROP VIEW IF EXISTS vw_admin_data;
CREATE VIEW vw_admin_data AS
SELECT
    -- Usuarios por rol
    (SELECT COUNT(*) FROM users u2
     JOIN roles r2 ON r2.id = u2.role_id
     WHERE r2.name = 'insured'
       AND u2.deleted_at IS NULL)                               AS total_insured,
    (SELECT COUNT(*) FROM users u2
     JOIN roles r2 ON r2.id = u2.role_id
     WHERE r2.name = 'adjuster'
       AND u2.deleted_at IS NULL)                               AS total_adjusters,
    (SELECT COUNT(*) FROM users u2
     JOIN roles r2 ON r2.id = u2.role_id
     WHERE r2.name = 'supervisor'
       AND u2.deleted_at IS NULL)                               AS total_supervisors,
    -- Polizas
    (SELECT COUNT(*) FROM policies p2
     WHERE p2.status = 'active'
       AND p2.end_validity >= CURDATE()
       AND p2.deleted_at IS NULL)                               AS active_policies,
    -- Siniestros del mes
    (SELECT COUNT(*) FROM sinisters s2
     WHERE MONTH(s2.report_date) = MONTH(CURDATE())
       AND YEAR(s2.report_date) = YEAR(CURDATE())
       AND s2.deleted_at IS NULL)                               AS sinisters_this_month,
    -- Distribucion de estados (para la grafica)
    (SELECT COUNT(*) FROM sinisters s2
     WHERE s2.status IN ('closed', 'approved',
         'approved_with_deductible',
         'approved_without_deductible',
         'applies_payment_for_repairs', 'total_loss')
       AND s2.deleted_at IS NULL)                               AS sinisters_closed,
    (SELECT COUNT(*) FROM sinisters s2
     WHERE s2.status = 'in_review'
       AND s2.deleted_at IS NULL)                               AS sinisters_in_review,
    (SELECT COUNT(*) FROM sinisters s2
     WHERE s2.status = 'rejected'
       AND s2.deleted_at IS NULL)                               AS sinisters_rejected;


-- =============================================================================
-- SECCION 5: DETALLE DE SINIESTRO
-- =============================================================================

/**
 * vw_sinister_data
 * Todos los datos del siniestro para la vista de detalle.
 * El controlador filtra por: WHERE sinister_id = $sinister->id
 *
 * Uso: SELECT * FROM vw_sinister_data WHERE sinister_id = ?;
 */
DROP VIEW IF EXISTS vw_sinister_data;
CREATE VIEW vw_sinister_data AS
SELECT
    s.id                                                        AS sinister_id,
    s.folio                                                     AS sinister_folio,
    s.sinister_number,
    s.status                                                    AS sinister_status,
    s.occur_date,
    s.report_date,
    s.close_date,
    s.description,
    s.location,
    -- Poliza
    po.folio                                                    AS policy_folio,
    po.policy_number,
    po.status                                                   AS policy_status,
    po.begin_validity,
    po.end_validity,
    -- Plan
    pl.name                                                     AS plan_name,
    -- Vehiculo
    CONCAT(vm.brand, ' ', vm.sub_brand, ' ', vm.year)          AS vehicle_data,
    vm.color                                                    AS vehicle_color,
    iv.plate,
    iv.vin,
    -- Asegurado
    po.insured_id,
    CONCAT(ui.name, ' ', ui.father_lastname)                   AS insured_name,
    ui.email                                                    AS insured_email,
    ui.phone                                                    AS insured_phone,
    -- Ajustador
    s.adjuster_id,
    CONCAT(ua.name, ' ', ua.father_lastname)                   AS adjuster_name,
    ua.email                                                    AS adjuster_email,
    -- Supervisor
    s.supervisor_id,
    CONCAT(us.name, ' ', us.father_lastname)                   AS supervisor_name
FROM sinisters s
LEFT JOIN policies        po ON po.id  = s.policy_id        AND po.deleted_at IS NULL
LEFT JOIN plans           pl ON pl.id  = po.plan_id         AND pl.deleted_at IS NULL
LEFT JOIN insured_vehicles iv ON iv.id = po.vehicle_id      AND iv.deleted_at IS NULL
LEFT JOIN vehicle_models  vm ON vm.id  = iv.vehicle_model_id AND vm.deleted_at IS NULL
LEFT JOIN users           ui ON ui.id  = po.insured_id      AND ui.deleted_at IS NULL
LEFT JOIN users           ua ON ua.id  = s.adjuster_id      AND ua.deleted_at IS NULL
LEFT JOIN users           us ON us.id  = s.supervisor_id    AND us.deleted_at IS NULL
WHERE s.deleted_at IS NULL;


-- =============================================================================
-- SECCION 6: VEHICULOS
-- =============================================================================

/**
 * vw_insured_vehicles
 * Todos los vehiculos de un asegurado con info de poliza activa (si tiene).
 * El controlador filtra por: WHERE user_id = auth()->id()
 *
 * NOTA: Depende de fn_policy_deadline (ejecutar functions.sql primero).
 *
 * Uso: SELECT * FROM vw_insured_vehicles WHERE user_id = ?;
 */
DROP VIEW IF EXISTS vw_insured_vehicles;
CREATE VIEW vw_insured_vehicles AS
SELECT
    iv.id                                                       AS vehicle_id,
    iv.user_id,
    iv.vin,
    iv.plate,
    vm.brand,
    vm.sub_brand,
    vm.year,
    vm.version,
    vm.color,
    -- Poliza activa del vehiculo (si tiene)
    po.id                                                       AS policy_id,
    po.folio                                                    AS policy_folio,
    po.policy_number,
    po.status                                                   AS policy_status,
    po.end_validity,
    fn_policy_deadline(po.id)                                   AS policy_days_left,
    pl.name                                                     AS plan_name
FROM insured_vehicles iv
LEFT JOIN vehicle_models vm ON vm.id  = iv.vehicle_model_id AND vm.deleted_at IS NULL
LEFT JOIN policies po       ON po.vehicle_id = iv.id
    AND po.deleted_at IS NULL
    AND po.status = 'active'
LEFT JOIN plans pl          ON pl.id  = po.plan_id          AND pl.deleted_at IS NULL
WHERE iv.deleted_at IS NULL;
