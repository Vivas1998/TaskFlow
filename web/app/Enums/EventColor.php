<?php

namespace App\Enums;

enum EventColor: string
{
    case Blue = 'blue';
    case Green = 'green';
    case Violet = 'violet';
    case Orange = 'orange';
    case Pink = 'pink';
    case Turquoise = 'turquoise';
    case Red = 'red';
    case Slate = 'slate';

    public function label(): string
    {
        return match ($this) {
            self::Blue => 'Azul',
            self::Green => 'Verde',
            self::Violet => 'Violeta',
            self::Orange => 'Naranja',
            self::Pink => 'Rosa',
            self::Turquoise => 'Turquesa',
            self::Red => 'Rojo',
            self::Slate => 'Gris',
        };
    }

    public function hex(): string
    {
        return match ($this) {
            self::Blue => '#3157d5',
            self::Green => '#168563',
            self::Violet => '#7c3aed',
            self::Orange => '#a4510b',
            self::Pink => '#b51d5b',
            self::Turquoise => '#0e7490',
            self::Red => '#ba3a49',
            self::Slate => '#64748b',
        };
    }
}
