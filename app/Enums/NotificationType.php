<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationType extends Enum
{
    const MESSAGE = [
        'Chờ',
        'Đang Thực Hiện',
        'Đã Đóng',
    ];

    const Invite = 0;
    const Comment = 1;
    const ChangeContent = 2;
}
