<?php

namespace App\Http\Controllers;

use App\Enums\Role;
use App\Models\Company;
use App\Models\Invitation;
use App\Models\User;
use App\Policies\InvitationPolicy;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvitationController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $query = Invitation::with(['company', 'inviter'])->latest();
        if (!$user->isSuperAdmin()) {
            $query->where('company_id', $user->company_id);
        }

        $invitations = $query->paginate(15);
        $companies = Company::all();
        $roles = Role::cases();

        return view('invitations.index', compact('invitations', 'companies', 'roles'));
    }

    public function create(): View
    {
        $companies = Company::all();
        $roles = Role::cases();

        return view('invitations.create', compact('companies', 'roles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $user = $request->user();

        $validated = $request->validate([
            'email' => ['required', 'email', 'unique:users,email'],
            'role' => ['required', Rule::in(Role::values())],
            'company_id' => ['nullable', 'exists:companies,id'],
        ]);

        $targetCompanyId = $user->isSuperAdmin() ? ($validated['company_id'] ?? null) : $user->company_id;
        $targetRole = $validated['role'];

        $policy = new InvitationPolicy();
        if (!$policy->invite($user, $targetRole, $targetCompanyId)) {
            abort(403, 'You are not authorized to send this invitation under the current role restrictions.');
        }

        $token = Str::random(40);

        Invitation::create([
            'company_id' => $targetCompanyId,
            'invited_by' => $user->id,
            'email' => $validated['email'],
            'role' => $targetRole,
            'token' => $token,
            'status' => 'pending',
            'expires_at' => now()->addDays(7),
        ]);

        // TODO: dispatch mail job (SendInvitationEmail::dispatch(...)) once mail credentials are wired up
        $inviteUrl = route('invitations.accept', ['token' => $token]);

        return redirect()->route('invitations.index')
            ->with('status', "Invitation created for {$validated['email']}! Invite Link: {$inviteUrl}");
    }

    public function accept(string $token): View
    {
        $invitation = Invitation::with('company')->where('token', $token)->firstOrFail();

        return view('invitations.accept', compact('invitation'));
    }

    public function processAccept(Request $request, string $token): RedirectResponse
    {
        $invitation = Invitation::where('token', $token)->firstOrFail();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'company_id' => $invitation->company_id,
            'name' => $validated['name'],
            'email' => $invitation->email,
            'password' => Hash::make($validated['password']),
            'role' => $invitation->role,
        ]);

        // Clean up invitation token after registration
        $invitation->delete();

        Auth::login($user);

        return redirect()->route('dashboard')
            ->with('status', "Welcome aboard, {$user->name}! Your account has been activated.");
    }
}
