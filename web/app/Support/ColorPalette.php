<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Collection;

final class ColorPalette
{
    public const GENERIC = '#64748b';

    public const COMPLETED_BACKGROUND = '#e7f7ef';

    public const COMPLETED_TEXT = '#2f775d';

    public const CANCELLED_BACKGROUND = '#fdebed';

    public const CANCELLED_TEXT = '#a34b57';

    private const USER_COLORS = [
        '#3157d5',
        '#00796b',
        '#7c3aed',
        '#a4510b',
        '#b51d5b',
        '#0e7490',
        '#4338ca',
        '#28753d',
    ];

    public static function forUser(User $user): string
    {
        $index = max(0, ((int) $user->getKey()) - 1) % count(self::USER_COLORS);

        return self::USER_COLORS[$index];
    }

    /** @param Collection<int, User> $assignees */
    public static function forAssignees(Collection $assignees): string
    {
        return $assignees->count() === 1
            ? self::forUser($assignees->first())
            : self::GENERIC;
    }
}
