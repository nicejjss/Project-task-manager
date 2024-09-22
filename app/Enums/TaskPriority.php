<?php declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class TaskPriority extends Enum
{
    const MESSAGE = [
        'Cao',
        'Trung Bình',
        'Thấp',
    ];

    const High = 0;
    const Mid = 1;
    const Low = 2;
}
