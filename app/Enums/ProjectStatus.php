<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class ProjectStatus extends Enum
{
    const MESSAGE = [
        'Mở',
        'Đang Thực Hiện',
        'Đã Đóng',
    ];

    const Open = 0;
    const Progressing = 1;
    const Closed = 2;

    public static function MESSAGE($status): string
    {
        return self::MESSAGE[$status];
    }
}
