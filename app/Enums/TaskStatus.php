<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaskStatus extends Enum
{
    const MESSAGE = [
        'Cần Thực Hiện',
        'Đang Thực Hiện',
        'Chờ Duyệt',
        'Hoàn Thành',
    ];

    const Open = 0;
    const Progressing = 1;
    const AcceptedTime = 2;
    const Done = 3;

    public function getContext(int $status): string
    {
        return self::MESSAGE[$status];
    }
}
