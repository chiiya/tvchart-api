<?php declare(strict_types=1);

namespace App\Domain\Enumerators;

enum BlacklistReason: string
{
    case UNAVAILABLE = 'unavailable';
    case INAPPROPRIATE = 'inappropriate';
    case GENRE = 'genre';
    case NETWORK = 'network';
    case OLD = 'old';

    public static function values(): array
    {
        return [
            BlacklistReason::UNAVAILABLE->value => __('Unavailable'),
            BlacklistReason::INAPPROPRIATE->value => __('Inappropriate'),
            BlacklistReason::GENRE->value => __('Blacklisted Genre'),
            BlacklistReason::NETWORK->value => __('Blacklisted Network'),
            BlacklistReason::OLD->value => __('Old Show)'),
        ];
    }

    public function present(): string
    {
        return BlacklistReason::values()[$this->value];
    }
}
