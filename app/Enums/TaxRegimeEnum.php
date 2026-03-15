<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum TaxRegimeEnum: string
{
    use EnumHelper;

    case GENERAL_PM = '601';
    case SIMPLIFICADO_PM = '602';
    case NO_LUCRATIVOS = '603';
    case REPECO = '604';
    case SUELDOS_Y_SALARIOS = '605';
    case ARRENDAMIENTO = '606';
    case ENAJENACION_BIENES = '607';
    case DEMAS_INGRESOS = '608';
    case CONSOLIDACION = '609';
    case RESIDENTES_EXTRANJERO = '610';
    case DIVIDENDOS = '611';
    case ACTIVIDAD_EMPRESARIAL = '612';
    case INTERMEDIO = '613';
    case INTERESES = '614';
    case PREMIOS = '615';
    case SIN_OBLIGACIONES = '616';
    case PEMEX = '617';
    case SIMPLIFICADO_PF = '618';
    case PRESTAMOS = '619';
    case COOPERATIVAS = '620';
    case RIF = '621';
    case AGRICOLA_PM = '622';
    case GRUPOS_SOCIEDADES = '623';
    case COORDINADOS = '624';
    case PLATAFORMAS_TECNOLOGICAS = '625';
    case RESICO = '626';

    public function label(): string
    {
        return match ($this) {

            self::GENERAL_PM => 'Régimen general de ley personas morales',
            self::SIMPLIFICADO_PM => 'Régimen simplificado de ley personas morales',
            self::NO_LUCRATIVOS => 'Personas morales con fines no lucrativos',
            self::REPECO => 'Régimen de pequeños contribuyentes',
            self::SUELDOS_Y_SALARIOS => 'Sueldos y salarios',
            self::ARRENDAMIENTO => 'Arrendamiento',
            self::ENAJENACION_BIENES => 'Enajenación o adquisición de bienes',
            self::DEMAS_INGRESOS => 'Régimen de los demás ingresos',
            self::CONSOLIDACION => 'Régimen de consolidación',
            self::RESIDENTES_EXTRANJERO => 'Residentes en el extranjero sin establecimiento permanente',
            self::DIVIDENDOS => 'Ingresos por dividendos (socios y accionistas)',
            self::ACTIVIDAD_EMPRESARIAL => 'Personas físicas con actividades empresariales y profesionales',
            self::INTERMEDIO => 'Régimen intermedio personas físicas con actividades empresariales',
            self::INTERESES => 'Ingresos por intereses',
            self::PREMIOS => 'Ingresos por obtención de premios',
            self::SIN_OBLIGACIONES => 'Sin obligaciones fiscales',
            self::PEMEX => 'PEMEX',
            self::SIMPLIFICADO_PF => 'Régimen simplificado de ley personas físicas',
            self::PRESTAMOS => 'Ingresos por obtención de préstamos',
            self::COOPERATIVAS => 'Sociedades cooperativas de producción',
            self::RIF => 'Régimen de incorporación fiscal',
            self::AGRICOLA_PM => 'Actividades agrícolas, ganaderas, silvícolas y pesqueras (PM)',
            self::GRUPOS_SOCIEDADES => 'Régimen opcional para grupos de sociedades',
            self::COORDINADOS => 'Régimen de los coordinados',
            self::PLATAFORMAS_TECNOLOGICAS => 'Actividades empresariales a través de plataformas tecnológicas',
            self::RESICO => 'Régimen simplificado de confianza',
        };
    }
}
