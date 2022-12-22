<?php declare(strict_types=1);

namespace App\Domain\Enumerators;

enum ImportType: string
{
    case COMPANIES = 'companies';
    case NETWORKS = 'networks';
}
