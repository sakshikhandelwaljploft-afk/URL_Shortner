<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\Company;
use App\Models\ShortUrl;
use App\Models\User;
use Database\Seeders\SuperAdminSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UrlShortenerSpecificationTest extends TestCase
{
    use RefreshDatabase;

    protected Company $companyA;
    protected Company $companyB;

    protected User $superAdmin;
    protected User $adminCompanyA;
    protected User $memberCompanyA;
    protected User $salesCompanyA;
    protected User $managerCompanyA;

    protected User $adminCompanyB;
    protected User $memberCompanyB;

    protected ShortUrl $urlCompanyAByUser1;
    protected ShortUrl $urlCompanyAByUser2;
    protected ShortUrl $urlCompanyBByUser1;

    protected function setUp(): void
    {
        parent::setUp();

        $this->companyA = Company::create(['name' => 'Alpha Corp']);
        $this->companyB = Company::create(['name' => 'Beta Corp']);

        $this->superAdmin = User::create([
            'name' => 'Super Administrator',
            'email' => 'superadmin@example.com',
            'password' => bcrypt('password123'),
            'role' => Role::SuperAdmin,
            'company_id' => null,
        ]);

        $this->adminCompanyA = User::create([
            'name' => 'Admin Alpha',
            'email' => 'admin@alpha.com',
            'password' => bcrypt('password123'),
            'role' => Role::Admin,
            'company_id' => $this->companyA->id,
        ]);

        $this->memberCompanyA = User::create([
            'name' => 'Member Alpha One',
            'email' => 'member1@alpha.com',
            'password' => bcrypt('password123'),
            'role' => Role::Member,
            'company_id' => $this->companyA->id,
        ]);

        $this->salesCompanyA = User::create([
            'name' => 'Sales Alpha',
            'email' => 'sales@alpha.com',
            'password' => bcrypt('password123'),
            'role' => Role::Sales,
            'company_id' => $this->companyA->id,
        ]);

        $this->managerCompanyA = User::create([
            'name' => 'Manager Alpha',
            'email' => 'manager@alpha.com',
            'password' => bcrypt('password123'),
            'role' => Role::Manager,
            'company_id' => $this->companyA->id,
        ]);

        $this->adminCompanyB = User::create([
            'name' => 'Admin Beta',
            'email' => 'admin@beta.com',
            'password' => bcrypt('password123'),
            'role' => Role::Admin,
            'company_id' => $this->companyB->id,
        ]);

        $this->memberCompanyB = User::create([
            'name' => 'Member Beta',
            'email' => 'member@beta.com',
            'password' => bcrypt('password123'),
            'role' => Role::Member,
            'company_id' => $this->companyB->id,
        ]);

        // Create initial test short URLs
        $this->urlCompanyAByUser1 = ShortUrl::create([
            'company_id' => $this->companyA->id,
            'user_id' => $this->memberCompanyA->id,
            'original_url' => 'https://alpha-link-1.com',
            'code' => 'alpha1',
            'clicks' => 0,
        ]);

        $this->urlCompanyAByUser2 = ShortUrl::create([
            'company_id' => $this->companyA->id,
            'user_id' => $this->salesCompanyA->id,
            'original_url' => 'https://alpha-link-2.com',
            'code' => 'alpha2',
            'clicks' => 0,
        ]);

        $this->urlCompanyBByUser1 = ShortUrl::create([
            'company_id' => $this->companyB->id,
            'user_id' => $this->memberCompanyB->id,
            'original_url' => 'https://beta-link-1.com',
            'code' => 'beta1',
            'clicks' => 0,
        ]);
    }

    public function test_admin_and_member_cant_create_short_urls(): void
    {
        // Admin attempt
        $responseAdmin = $this->actingAs($this->adminCompanyA)->post('/urls', [
            'original_url' => 'https://forbidden-admin-url.com',
        ]);
        $responseAdmin->assertForbidden();

        // Member attempt
        $responseMember = $this->actingAs($this->memberCompanyA)->post('/urls', [
            'original_url' => 'https://forbidden-member-url.com',
        ]);
        $responseMember->assertForbidden();

        $this->assertDatabaseMissing('short_urls', [
            'original_url' => 'https://forbidden-admin-url.com',
        ]);
        $this->assertDatabaseMissing('short_urls', [
            'original_url' => 'https://forbidden-member-url.com',
        ]);
    }

    public function test_superadmin_cannot_create_short_urls(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/urls', [
            'original_url' => 'https://forbidden-superadmin-url.com',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('short_urls', [
            'original_url' => 'https://forbidden-superadmin-url.com',
        ]);
    }

    public function test_superadmin_cant_see_the_list_of_all_short_urls_for_every_company(): void
    {
        $response = $this->actingAs($this->superAdmin)->get('/urls');
        $response->assertForbidden();
    }

    public function test_admin_can_only_see_the_list_of_all_short_urls_not_created_in_their_own_company(): void
    {
        $response = $this->actingAs($this->adminCompanyA)->get('/urls');

        $response->assertOk();
        // Should NOT see URLs from their own company (Alpha)
        $response->assertDontSee($this->urlCompanyAByUser1->original_url);
        $response->assertDontSee($this->urlCompanyAByUser2->original_url);

        // MUST see URLs from other company (Beta)
        $response->assertSee($this->urlCompanyBByUser1->original_url);
    }

    public function test_member_can_only_see_the_list_of_all_short_urls_not_created_by_themselves(): void
    {
        $response = $this->actingAs($this->memberCompanyA)->get('/urls');

        $response->assertOk();
        // Should NOT see URL created by themselves
        $response->assertDontSee($this->urlCompanyAByUser1->original_url);

        // MUST see URLs created by other users (Sales in Alpha, and Member in Beta)
        $response->assertSee($this->urlCompanyAByUser2->original_url);
        $response->assertSee($this->urlCompanyBByUser1->original_url);
    }

    public function test_short_urls_are_not_publicly_resolvable_and_redirect_to_the_original_url(): void
    {
        // 1. Unauthenticated / Public visitor: MUST NOT be publicly resolvable (403 Forbidden)
        $publicResponse = $this->get('/' . $this->urlCompanyAByUser1->code);
        $publicResponse->assertForbidden();

        // 2. Authenticated user: MUST resolve and redirect to original destination URL (302 Redirect)
        $authenticatedResponse = $this->actingAs($this->salesCompanyA)->get('/' . $this->urlCompanyAByUser1->code);
        $authenticatedResponse->assertRedirect($this->urlCompanyAByUser1->original_url);

        // Verify click count was incremented
        $this->assertEquals(1, $this->urlCompanyAByUser1->fresh()->clicks);
    }

    public function test_sales_and_manager_can_create_short_urls(): void
    {
        $salesResponse = $this->actingAs($this->salesCompanyA)->post('/urls', [
            'original_url' => 'https://sales-success-url.com',
            'code' => 'salessuccess',
        ]);
        $salesResponse->assertRedirect(route('urls.index'));

        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://sales-success-url.com',
            'code' => 'salessuccess',
            'user_id' => $this->salesCompanyA->id,
        ]);

        $managerResponse = $this->actingAs($this->managerCompanyA)->post('/urls', [
            'original_url' => 'https://manager-success-url.com',
            'code' => 'mgrsuccess',
        ]);
        $managerResponse->assertRedirect(route('urls.index'));

        $this->assertDatabaseHas('short_urls', [
            'original_url' => 'https://manager-success-url.com',
            'code' => 'mgrsuccess',
            'user_id' => $this->managerCompanyA->id,
        ]);
    }

    public function test_superadmin_seeder_using_raw_sql(): void
    {
        // Clear all users first
        User::query()->delete();

        $seeder = new SuperAdminSeeder();
        $seeder->run();

        $this->assertDatabaseHas('users', [
            'email' => 'superadmin@example.com',
            'role' => 'SuperAdmin',
        ]);
    }

    public function test_superadmin_cant_invite_an_admin_in_a_new_company(): void
    {
        $response = $this->actingAs($this->superAdmin)->post('/invitations', [
            'email' => 'invited-admin@company.com',
            'role' => Role::Admin->value,
            'company_id' => $this->companyA->id,
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('invitations', [
            'email' => 'invited-admin@company.com',
        ]);
    }

    public function test_admin_cant_invite_another_admin_or_member_in_their_own_company(): void
    {
        // Trying to invite an Admin
        $responseAdmin = $this->actingAs($this->adminCompanyA)->post('/invitations', [
            'email' => 'peer-admin@alpha.com',
            'role' => Role::Admin->value,
        ]);
        $responseAdmin->assertForbidden();

        // Trying to invite a Member
        $responseMember = $this->actingAs($this->adminCompanyA)->post('/invitations', [
            'email' => 'peer-member@alpha.com',
            'role' => Role::Member->value,
        ]);
        $responseMember->assertForbidden();
    }

    public function test_user_can_login_and_logout(): void
    {
        // Login
        $responseLogin = $this->post('/login', [
            'email' => 'admin@alpha.com',
            'password' => 'password123',
        ]);
        $responseLogin->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($this->adminCompanyA);

        // Logout
        $responseLogout = $this->post('/logout');
        $responseLogout->assertRedirect(route('login'));
        $this->assertGuest();
    }
}

