<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationType extends Enum
{
    const MESSAGE = [
        'Được Mời Vào Project',
        'Được Giao Task',
        'Có Comment Mới',
        'Cập Nhật Mới',
    ];

    const Invite = 0;

    const Assign = 1;
    const Comment = 2;
    const ChangeContent = 3;

    public static function MESSAGE($status): string
    {
        return self::MESSAGE[$status];
    }
}
