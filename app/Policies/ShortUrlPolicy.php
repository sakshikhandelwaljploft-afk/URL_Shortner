<?php

namespace App\Policies;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ShortUrlPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): bool
    {
        $mode = config('shortener.mode', 'literal');

        if ($mode === 'literal') {
            // superadmin is restricted from tenant links
            if ($user->isSuperAdmin()) {
                return false;
            }
            return true;
        }

        return true;
    }

    public function view(User $user, ShortUrl $shortUrl): bool
    {
        $mode = config('shortener.mode', 'literal');

        if ($mode === 'literal') {
            if ($user->isSuperAdmin()) {
                return false;
            }

            if ($user->isAdmin()) {
                return $shortUrl->company_id !== $user->company_id;
            }

            if ($user->isMember()) {
                return $shortUrl->user_id !== $user->id;
            }

            return true;
        }

        // Standard mode
        if ($user->isSuperAdmin()) {
            return true;
        }

        if ($user->isAdmin()) {
            return $shortUrl->company_id === $user->company_id;
        }

        return $shortUrl->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        $mode = config('shortener.mode', 'literal');

        if ($mode === 'literal') {
            // In this setup, URL creation is reserved for Sales and Managers
            if ($user->isSuperAdmin() || $user->isAdmin() || $user->isMember()) {
                return false;
            }

            return $user->isSales() || $user->isManager();
        }

        if ($user->isSuperAdmin()) {
            return false;
        }

        return $user->isAdmin() || $user->isMember() || $user->isSales() || $user->isManager();
    }
}
