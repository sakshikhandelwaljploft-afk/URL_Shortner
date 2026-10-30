<?php

namespace App\Enums;

enum Role: string
{
    case SuperAdmin = 'SuperAdmin';
    case Admin = 'Admin';
    case Member = 'Member';
    case Sales = 'Sales';
    case Manager = 'Manager';

    public function isSuperAdmin(): bool
    {
        return $this === self::SuperAdmin;
    }

    public function isAdmin(): bool
    {
        return $this === self::Admin;
    }

    public function isMember(): bool
    {
        return $this === self::Member;
    }

    public function isSales(): bool
    {
        return $this === self::Sales;
    }

    public function isManager(): bool
    {
        return $this === self::Manager;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}

