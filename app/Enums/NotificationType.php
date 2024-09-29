<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationType extends Enum
{
    const MESSAGE = [
        'Mời',
        'Có Comment',
        'Cập Nhật',
    ];

    const Invite = 0;
    const Comment = 1;
    const ChangeContent = 2;

    public static function MESSAGE($status): string
    {
        return self::MESSAGE[$status];
    }
}
