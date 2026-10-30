<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        $hashedPassword = Hash::make('password123');
        $now = now()->toDateTimeString();

        // Direct SQL insert for root superadmin
        DB::statement("
            INSERT INTO users (name, email, password, role, company_id, created_at, updated_at)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ", [
            'Super Administrator',
            'superadmin@example.com',
            $hashedPassword,
            'SuperAdmin',
            null,
            $now,
            $now,
        ]);
    }
}
