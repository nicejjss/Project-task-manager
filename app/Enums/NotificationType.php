<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class NotificationType extends Enum
{
    const MESSAGE = [
        'Mời vào dự án',
        'Có công việc cần làm',
        'Được nhắc đến trong bình luận',
        'Công việc cần phê duyệt',
        'Công việc được phê duyệt',
        'Có cuộc họp trực tuyến',
    ];

    const Invite = 0;
    const Assign = 1;
    const Comment = 2;
    const Approve = 4;
    const Approved = 5;
    const Meeting = 6;

    public static function MESSAGE($status): string
    {
        return self::MESSAGE[$status];
    }
}
