<?php

namespace App\Enums;

enum RecurrenceType: string
{
    case Daily = 'daily';
    case Weekly = 'weekly';
    case Monthly = 'monthly';
    case Yearly = 'yearly';

    public function label(): string
    {
        return match ($this) {
            self::Daily => 'Diaria',
            self::Weekly => 'Semanal',
            self::Monthly => 'Mensual',
            self::Yearly => 'Anual',
        };
    }
}
