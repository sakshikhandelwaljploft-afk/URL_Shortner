<?php

namespace App\Services;

use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ShortUrlService
{
    public function generateUniqueCode(int $length = 6): string
    {
        // 6 chars is plenty for now, retry on unlikely collision
        do {
            $code = Str::lower(Str::random($length));
        } while (ShortUrl::where('code', $code)->exists());

        return $code;
    }

    public function getScopedUrlsForUser(User $user, int $perPage = 15): LengthAwarePaginator
    {
        $mode = config('shortener.mode', 'literal');

        if ($mode === 'literal') {
            // superadmin does not view tenant links directly
            if ($user->isSuperAdmin()) {
                abort(403, 'SuperAdmin is not authorized to view the list of short URLs.');
            }

            // admins only view external links outside their org
            if ($user->isAdmin()) {
                return ShortUrl::with(['company', 'user'])
                    ->where('company_id', '!=', $user->company_id)
                    ->latest()
                    ->paginate($perPage);
            }

            // regular members see shared links created by others
            if ($user->isMember()) {
                return ShortUrl::with(['company', 'user'])
                    ->where('user_id', '!=', $user->id)
                    ->latest()
                    ->paginate($perPage);
            }

            // sales & managers view company-level links
            return ShortUrl::with(['company', 'user'])
                ->where('company_id', $user->company_id)
                ->latest()
                ->paginate($perPage);
        }

        // Standard SaaS mode fallback
        if ($user->isSuperAdmin()) {
            return ShortUrl::with(['company', 'user'])
                ->latest()
                ->paginate($perPage);
        }

        if ($user->isAdmin()) {
            return ShortUrl::with(['company', 'user'])
                ->where('company_id', $user->company_id)
                ->latest()
                ->paginate($perPage);
        }

        return ShortUrl::with(['company', 'user'])
            ->where('user_id', $user->id)
            ->latest()
            ->paginate($perPage);
    }

    public function createShortUrl(User $user, string $originalUrl, ?string $customCode = null): ShortUrl
    {
        $code = $customCode ?: $this->generateUniqueCode();

        return ShortUrl::create([
            'company_id' => $user->company_id,
            'user_id' => $user->id,
            'original_url' => $originalUrl,
            'code' => $code,
            'clicks' => 0,
        ]);
    }
}
