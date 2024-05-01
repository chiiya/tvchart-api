<?php declare(strict_types=1);

namespace App\Domain\Enumerators;

enum Status: int
{
    case UNREVIEWED = 0;
    case WHITELISTED = 1;
    case UNDECIDED = 2;
    case BLACKLISTED = 3;
    case BLACKLISTED_FINAL = 4;

    public static function values(): array
    {
        return [
            Status::UNREVIEWED->value => __('Unreviewed'),
            Status::WHITELISTED->value => __('Whitelisted'),
            Status::UNDECIDED->value => __('Undecided'),
            Status::BLACKLISTED->value => __('Blacklisted'),
            Status::BLACKLISTED_FINAL->value => __('Blacklisted (Final)'),
        ];
    }
}
