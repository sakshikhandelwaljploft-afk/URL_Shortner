<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class InvitationPolicy
{
    use HandlesAuthorization;

    public function invite(User $user, string $targetRole, ?int $targetCompanyId = null): bool
    {
        $mode = config('shortener.mode', 'literal');

        if ($mode === 'literal') {
            // superadmin cannot onboard new admins directly
            if ($user->isSuperAdmin() && $targetRole === Role::Admin->value) {
                return false;
            }

            // company admins can only invite sales & managers
            if ($user->isAdmin() && in_array($targetRole, [Role::Admin->value, Role::Member->value])) {
                return false;
            }

            // members have read-only access to invitations
            if ($user->isMember()) {
                return false;
            }

            return true;
        }

        // Standard SaaS setup
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin()) {
            return in_array($targetRole, [Role::Admin->value, Role::Member->value]);
        }

        return false;
    }
}
