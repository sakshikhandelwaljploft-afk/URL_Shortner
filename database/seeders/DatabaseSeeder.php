<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Seed initial superadmin
        $this->call(SuperAdminSeeder::class);

        // Test companies
        $acme = Company::create(['name' => 'Acme Corporation']);
        $beta = Company::create(['name' => 'Beta Industries']);

        $commonPassword = Hash::make('password123');

        // Acme members
        $acmeAdmin = User::create([
            'company_id' => $acme->id,
            'name' => 'Acme Admin',
            'email' => 'admin.acme@example.com',
            'password' => $commonPassword,
            'role' => Role::Admin,
        ]);

        $acmeMember1 = User::create([
            'company_id' => $acme->id,
            'name' => 'Acme Member One',
            'email' => 'member1.acme@example.com',
            'password' => $commonPassword,
            'role' => Role::Member,
        ]);

        $acmeMember2 = User::create([
            'company_id' => $acme->id,
            'name' => 'Acme Member Two',
            'email' => 'member2.acme@example.com',
            'password' => $commonPassword,
            'role' => Role::Member,
        ]);

        $acmeSales = User::create([
            'company_id' => $acme->id,
            'name' => 'Acme Sales Rep',
            'email' => 'sales.acme@example.com',
            'password' => $commonPassword,
            'role' => Role::Sales,
        ]);

        $acmeManager = User::create([
            'company_id' => $acme->id,
            'name' => 'Acme Manager',
            'email' => 'manager.acme@example.com',
            'password' => $commonPassword,
            'role' => Role::Manager,
        ]);

        // Beta team
        $betaAdmin = User::create([
            'company_id' => $beta->id,
            'name' => 'Beta Admin',
            'email' => 'admin.beta@example.com',
            'password' => $commonPassword,
            'role' => Role::Admin,
        ]);

        $betaSales = User::create([
            'company_id' => $beta->id,
            'name' => 'Beta Sales Rep',
            'email' => 'sales.beta@example.com',
            'password' => $commonPassword,
            'role' => Role::Sales,
        ]);

        // Demo seed links
        ShortUrl::create([
            'company_id' => $acme->id,
            'user_id' => $acmeMember1->id,
            'original_url' => 'https://laravel.com/docs/11.x',
            'code' => 'laraveldocs',
            'clicks' => 5,
        ]);

        ShortUrl::create([
            'company_id' => $acme->id,
            'user_id' => $acmeMember2->id,
            'original_url' => 'https://php.net/manual/en/',
            'code' => 'phpdocs',
            'clicks' => 12,
        ]);

        ShortUrl::create([
            'company_id' => $acme->id,
            'user_id' => $acmeSales->id,
            'original_url' => 'https://github.com',
            'code' => 'ghublink',
            'clicks' => 20,
        ]);

        ShortUrl::create([
            'company_id' => $beta->id,
            'user_id' => $betaSales->id,
            'original_url' => 'https://google.com',
            'code' => 'googlebeta',
            'clicks' => 42,
        ]);
    }
}
