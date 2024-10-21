<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

final class TaskHistoryType extends Enum
{
    const MESSAGE = [
        'Tạo công việc',
        'Thay đổi nội dung công việc',
        'Thêm bình luận',
        'Cập nhật trạng thái công việc',
    ];

    const Create = 0;
    const Update = 1;
    const Comment = 2;
    const UpdateStatus = 3;

    public static function MESSAGE($status): string
    {
        return self::MESSAGE[$status];
    }
}
