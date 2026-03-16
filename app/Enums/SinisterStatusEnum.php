<?php

namespace App\Enums;

use App\Traits\EnumHelper;

enum SinisterStatusEnum: string
{
    use EnumHelper;

    case REPORTED = 'reported';
    case REJECTED = 'rejected';
    case IN_REVIEW = 'in_review';
    case APPROVED = 'approved';
    case APPROVED_WITH_DEDUCTIBLE = 'approved_with_deductible';
    case APPROVED_WITHOUT_DEDUCTIBLE = 'approved_without_deductible';
    case APPLIES_PAYMENT_FOR_REPAIRS = 'applies_payment_for_repairs';
    case TOTAL_LOSS = 'total_loss';
    case CLOSED = 'closed';

    public function label(): string
    {
        return match ($this) {
            self::REPORTED => 'Reportado',
            self::REJECTED => 'Rechazado',
            self::IN_REVIEW => 'En Revision',
            self::APPROVED => 'Aprobado',
            self::APPROVED_WITH_DEDUCTIBLE => 'Aprobado con Pago Dedubile',
            self::APPROVED_WITHOUT_DEDUCTIBLE => 'Aprobado sin Pago Deducible',
            self::APPLIES_PAYMENT_FOR_REPAIRS => 'Aplica pago para Reparacion de la Unidad',
            self::TOTAL_LOSS => 'Perdida Total',
            self::CLOSED => 'Cerrado'
        };
    }

    public function transitions(): array
    {
        return match ($this) {

            self::REPORTED => [
                self::IN_REVIEW,
                self::REJECTED
            ],

            self::IN_REVIEW => [
                self::APPROVED,
                self::REJECTED,
                self::TOTAL_LOSS
            ],

            self::APPROVED => [
                self::APPROVED_WITH_DEDUCTIBLE,
                self::APPROVED_WITHOUT_DEDUCTIBLE
            ],

            self::APPROVED_WITH_DEDUCTIBLE,
            self::APPROVED_WITHOUT_DEDUCTIBLE => [
                self::APPLIES_PAYMENT_FOR_REPAIRS,
                self::TOTAL_LOSS,
                self::CLOSED
            ],

            self::APPLIES_PAYMENT_FOR_REPAIRS => [
                self::CLOSED
            ],

            self::TOTAL_LOSS => [
                self::CLOSED
            ],

            self::REJECTED => [
                self::CLOSED
            ],

            self::CLOSED => [],
        };
    }

    public function canTransitionTo(self $newStatus): bool
    {
        return in_array($newStatus, $this->transitions(), true);
    }

    public function nextStatuses(): array
    {
        return collect($this->transitions())
            ->mapWithKeys(fn($status) => [
                $status->value => $status->label()
            ])
            ->toArray();
    }
}
