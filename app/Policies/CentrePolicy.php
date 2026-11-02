<?php

namespace App\Policies;

use App\Models\Centre;
use App\Models\User;

class CentrePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isSuperAdmin()) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->isAdmin();
    }

    public function view(User $user, Centre $centre): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        if ($user->isCentre() || $user->isStaff()) {
            $userCentreId = $user->centre_id ?? $user->centre?->id;
            return (int)$centre->id === (int)$userCentreId || (int)$centre->user_id === (int)$user->id;
        }

        if ($user->isStudent()) {
            // Student can view their enrolled centre info
            return (int)$user->student?->centre_id === (int)$centre->id;
        }

        return false;
    }

    public function update(User $user, Centre $centre): bool
    {
        // Franchise profile editing is strictly restricted to Main Administration
        return $user->isAdmin();
    }

    public function delete(User $user, Centre $centre): bool
    {
        return $user->isSuperAdmin();
    }
}
