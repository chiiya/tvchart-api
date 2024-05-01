<?php declare(strict_types=1);

namespace App\Domain\Policies;

use Chiiya\FilamentAccessControl\Models\FilamentUser;

class TvShowPolicy
{
    public function viewAny(FilamentUser $user): bool
    {
        return $user->can('shows.view');
    }

    public function view(FilamentUser $user): bool
    {
        return $user->can('shows.view');
    }

    public function create(): bool
    {
        return false;
    }

    public function update(): bool
    {
        return false;
    }

    public function delete(): bool
    {
        return false;
    }

    public function deleteAny(): bool
    {
        return false;
    }
}
