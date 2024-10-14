<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaskStatus extends Enum
{
    const MESSAGE = [
        'Cần Thực Hiện',
        'Đang Thực Hiện',
        'Chờ Phê Duyệt',
        'Hoàn Thành',
        'Đã Đóng',
    ];

    const Open = 0;
    const Progressing = 1;
    const WaitedTime = 2;
    const Done = 3;
    const Closed = 4;

    public static function MESSAGE(int $status): string
    {
        return self::MESSAGE[$status];
    }
}
