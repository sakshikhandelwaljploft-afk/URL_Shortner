<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class StandardMultiTenantTest extends TestCase
{
    use RefreshDatabase;

    protected Company $companyA;
    protected Company $companyB;
    protected User $adminCompanyA;
    protected User $memberCompanyA;
    protected User $superAdmin;
    protected ShortUrl $urlCompanyA;

    protected function setUp(): void
    {
        parent::setUp();

        Config::set('shortener.mode', 'standard');

        $this->companyA = Company::create(['name' => 'Acme Corp']);
        $this->companyB = Company::create(['name' => 'Beta Corp']);

        $this->adminCompanyA = User::create([
            'name' => 'Acme Admin',
            'email' => 'admin@acme.com',
            'password' => bcrypt('password123'),
            'role' => Role::Admin,
            'company_id' => $this->companyA->id,
        ]);

        $this->memberCompanyA = User::create([
            'name' => 'Acme Member',
            'email' => 'member@acme.com',
            'password' => bcrypt('password123'),
            'role' => Role::Member,
            'company_id' => $this->companyA->id,
        ]);

        $this->superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'super@acme.com',
            'password' => bcrypt('password123'),
            'role' => Role::SuperAdmin,
            'company_id' => null,
        ]);

        $this->urlCompanyA = ShortUrl::create([
            'company_id' => $this->companyA->id,
            'user_id' => $this->memberCompanyA->id,
            'original_url' => 'https://acme.org',
            'code' => 'acmelink',
            'clicks' => 0,
        ]);
    }

    public function test_standard_mode_admin_can_create_short_urls(): void
    {
        $response = $this->actingAs($this->adminCompanyA)->post('/urls', [
            'original_url' => 'https://standard-admin.com',
        ]);

        $response->assertRedirect(route('urls.index'));
        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://standard-admin.com',
            'company_id' => $this->companyA->id,
        ]);
    }

    public function test_standard_mode_public_resolution_redirects_for_guests(): void
    {
        $response = $this->get('/' . $this->urlCompanyA->code);
        $response->assertRedirect('https://acme.org');
        $this->assertEquals(1, $this->urlCompanyA->fresh()->clicks);
    }
}

